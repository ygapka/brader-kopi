<?php

namespace App\Models;

use Database\Factories\MenuVariantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuVariant extends Model
{
    /** @use HasFactory<MenuVariantFactory> */
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'name',
        'extra_price',
    ];

    protected function casts(): array
    {
        return [
            'extra_price' => 'decimal:2',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
