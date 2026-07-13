<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class SaleItem extends Model
{
    use LogsActivity;

    protected $fillable = [
        'sale_id',
        'batch_id',
        'medicine_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal'   => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['sale_id', 'batch_id', 'medicine_id', 'quantity', 'unit_price', 'subtotal'])
            ->logOnlyDirty()
            ->useLogName('sale_item')
            ->dontSubmitEmptyLogs();
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}