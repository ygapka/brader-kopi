<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    public function index(): View
    {
        $tables = Table::withCount('orders')->orderBy('table_number')->paginate(15);

        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:50', 'unique:tables,table_number'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'table_number.required' => 'Nomor meja wajib diisi.',
            'table_number.unique' => 'Nomor meja sudah terdaftar.',
        ]);

        // Otomatis generate token acak unik
        $token = Str::random(16);
        while (Table::where('token', $token)->exists()) {
            $token = Str::random(16);
        }

        Table::create([
            'table_number' => $validated['table_number'],
            'token' => $token,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.tables.index')
            ->with('success', "Meja {$validated['table_number']} berhasil ditambahkan.");
    }

    public function update(Request $request, Table $table): RedirectResponse
    {
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:50', 'unique:tables,table_number,'.$table->id],
            'is_active' => ['nullable', 'boolean'],
            'regenerate_token' => ['nullable', 'boolean'],
        ], [
            'table_number.required' => 'Nomor meja wajib diisi.',
            'table_number.unique' => 'Nomor meja sudah digunakan meja lain.',
        ]);

        $data = [
            'table_number' => $validated['table_number'],
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->boolean('regenerate_token')) {
            $token = Str::random(16);
            while (Table::where('token', $token)->exists()) {
                $token = Str::random(16);
            }
            $data['token'] = $token;
        }

        $table->update($data);

        return redirect()->route('admin.tables.index')
            ->with('success', "Data meja {$table->table_number} berhasil diperbarui.");
    }

    public function destroy(Table $table): RedirectResponse
    {
        $number = $table->table_number;
        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('success', "Meja {$number} berhasil dihapus.");
    }

    /**
     * Halaman tampilan siap cetak QR Code Meja.
     */
    public function print(Table $table): View
    {
        $orderUrl = url('/order/'.$table->token);
        $qrCodeSvg = QrCode::size(280)->margin(1)->generate($orderUrl);

        return view('admin.tables.print', compact('table', 'orderUrl', 'qrCodeSvg'));
    }

    /**
     * Download file QR Code format SVG.
     */
    public function downloadQr(Table $table): Response
    {
        $orderUrl = url('/order/'.$table->token);
        $qrCode = QrCode::size(500)->margin(2)->generate($orderUrl);

        $safeName = Str::slug($table->table_number);
        $filename = "qr-code-{$safeName}.svg";

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }
}
