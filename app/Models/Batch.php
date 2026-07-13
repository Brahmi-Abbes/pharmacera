<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'supplier_id',
        'quantity',
        'remaining_quantity',
        'purchase_price',
        'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date'      => 'date',
            'purchase_price'   => 'decimal:2',
        ];
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Expiry status: safe, warning, danger, expired
    public function getExpiryStatusAttribute(): string
    {
        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);

        if ($daysUntilExpiry < 0)  return 'expired';
        if ($daysUntilExpiry <= 30) return 'danger';
        if ($daysUntilExpiry <= 90) return 'warning';

        return 'safe';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date->isPast();
    }

    public function getIsEmptyAttribute(): bool
    {
        return $this->remaining_quantity <= 0;
    }
}