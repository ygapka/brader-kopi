<?php

namespace App\Models;

use Database\Factories\TableFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    /** @use HasFactory<TableFactory> */
    use HasFactory;

    protected $fillable = [
        'table_number',
        'token',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        $number = trim($this->table_number ?? '');

        return str_starts_with($number, 'Meja') ? $number : 'Meja '.$number;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
