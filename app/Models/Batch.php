<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    // How many days out counts as "warning" vs "danger" on an expiry badge.
    // Pulled out as constants so changing the cutoff is a one-line change,
    // not a hunt through the model for a hardcoded 30/90.
    private const DANGER_WITHIN_DAYS = 30;
    private const WARNING_WITHIN_DAYS = 90;

    public function getExpiryStatusAttribute(): string
    {
        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);

        if ($daysUntilExpiry < 0) {
            return 'expired';
        }

        if ($daysUntilExpiry <= self::DANGER_WITHIN_DAYS) {
            return 'danger';
        }

        if ($daysUntilExpiry <= self::WARNING_WITHIN_DAYS) {
            return 'warning';
        }

        return 'safe';
    }

    // Maps expiry_status to a Filament badge color. Both BatchesTable and
    // the dashboard's ExpiringBatches widget need this exact mapping — used
    // to be copy-pasted in both places, now it just lives here once.
    public function getExpiryBadgeColorAttribute(): string
    {
        return match ($this->expiry_status) {
            'expired', 'danger' => 'danger',
            'warning' => 'warning',
            default => 'success',
        };
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