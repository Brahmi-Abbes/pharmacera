<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'store_name',
        'currency',
        'phone',
        'address',
        'tax_rate',
    ];

    protected function casts(): array
    {
        return [
            'tax_rate' => 'decimal:2',
        ];
    }

    
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    // Convenience wrapper — most call sites (table columns, form fields,
    // the PDF report) just want the currency code, not the whole row.
    protected function currency(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Put your custom logic here (e.g. returning 'USD', 'DZD', etc.)
                return $this->attributes['value'] ?? 'DZD';
            }
        );
    }
}