<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    protected ?string $url;

    protected ?string $key;

    protected string $bucket;

    public function __construct()
    {
        $this->url = rtrim((string) (config('services.supabase.url') ?? env('SUPABASE_URL', '')), '/');
        $this->key = (string) (config('services.supabase.key') ?? env('SUPABASE_KEY', ''));
        $this->bucket = (string) (config('services.supabase.bucket') ?? env('SUPABASE_BUCKET', 'menus'));
    }

    /**
     * Check if Supabase credentials are configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->url) && ! empty($this->key);
    }

    /**
     * Upload an uploaded file to Supabase Storage or fallback to public disk.
     */
    public function upload(UploadedFile $file, string $folder = 'menus'): ?string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = $folder.'/'.Str::random(24).'.'.$extension;

        if ($this->isConfigured()) {
            try {
                $endpoint = "{$this->url}/storage/v1/object/{$this->bucket}/{$filename}";
                $mimeType = $file->getMimeType() ?: 'image/jpeg';

                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->key}",
                    'Content-Type' => $mimeType,
                ])->withBody(
                    (string) file_get_contents($file->getRealPath()),
                    $mimeType
                )->post($endpoint);

                if ($response->successful()) {
                    return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$filename}";
                }

                Log::error('Supabase upload failed: '.$response->status().' - '.$response->body());
            } catch (\Exception $e) {
                Log::error('Supabase upload exception: '.$e->getMessage());
            }
        }

        // Fallback to local public disk if not on serverless or if Supabase is offline
        try {
            return $file->store($folder, 'public');
        } catch (\Exception $e) {
            Log::error('Local storage store exception: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Delete a file from Supabase Storage or local storage.
     */
    public function delete(?string $pathOrUrl): bool
    {
        if (empty($pathOrUrl)) {
            return false;
        }

        // If it's a Supabase URL
        if ($this->isConfigured() && str_contains($pathOrUrl, $this->url)) {
            try {
                $prefix = "{$this->url}/storage/v1/object/public/{$this->bucket}/";
                if (str_starts_with($pathOrUrl, $prefix)) {
                    $objectPath = substr($pathOrUrl, strlen($prefix));
                    $endpoint = "{$this->url}/storage/v1/object/{$this->bucket}/{$objectPath}";

                    $response = Http::withHeaders([
                        'Authorization' => "Bearer {$this->key}",
                    ])->delete($endpoint);

                    return $response->successful();
                }
            } catch (\Exception $e) {
                Log::warning('Failed deleting from Supabase: '.$e->getMessage());
            }
        }

        // If it's a local storage path
        try {
            if (Storage::disk('public')->exists($pathOrUrl)) {
                return Storage::disk('public')->delete($pathOrUrl);
            }
        } catch (\Exception $e) {
            // ignore
        }

        return false;
    }
}
