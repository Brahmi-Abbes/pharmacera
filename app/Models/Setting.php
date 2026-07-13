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

    /**
     * There's only ever one row in this table. This helper always returns
     * it, creating it with defaults on first use so the app never has to
     * handle a "settings don't exist yet" case anywhere else.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}