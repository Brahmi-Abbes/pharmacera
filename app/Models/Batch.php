<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Batch extends Model
{
    use HasFactory, LogsActivity;
    

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['medicine_id', 'supplier_id', 'quantity', 'remaining_quantity', 'purchase_price', 'expiry_date'])
            ->logOnlyDirty()
            ->useLogName('batch')
            ->dontLogEmptyChanges();
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

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