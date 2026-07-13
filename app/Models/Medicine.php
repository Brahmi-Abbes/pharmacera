<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'generic_name',
        'barcode',
        'unit',
        'selling_price',
        'purchase_price',
        'alert_threshold',
    ];

    protected function casts(): array
    {
        return [
            'selling_price'  => 'decimal:2',
            'purchase_price' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Total remaining stock across all batches
    public function getTotalStockAttribute(): int
    {
        return $this->batches()->sum('remaining_quantity');
    }

    // Is stock below alert threshold
    public function getIsLowStockAttribute(): bool
    {
        return $this->total_stock <= $this->alert_threshold;
    }

    // Next batch to expire (FIFO)
    public function getNextExpiryAttribute()
    {
        return $this->batches()
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date')
            ->value('expiry_date');
    }
}