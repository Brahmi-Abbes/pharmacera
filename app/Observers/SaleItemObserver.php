<?php

namespace App\Observers;

use App\Models\Batch;
use App\Models\SaleItem;
use Illuminate\Validation\ValidationException;

class SaleItemObserver
{
    /**
     * Before a sale item is created, make sure the batch actually has enough
     * remaining stock. This is the hard safety net — the form already warns
     * the cashier live, but this stops it even if that's ever bypassed.
     */
    public function creating(SaleItem $saleItem): void
    {
        $batch = Batch::whereKey($saleItem->batch_id)->lockForUpdate()->first();

        if (! $batch || $batch->remaining_quantity < $saleItem->quantity) {
            throw ValidationException::withMessages([
                'batch_id' => "Only {$batch?->remaining_quantity} left in that batch — reduce the quantity or pick another batch.",
            ]);
        }
    }

    public function created(SaleItem $saleItem): void
    {
        $saleItem->batch()->decrement('remaining_quantity', $saleItem->quantity);
    }

    /**
     * If an existing sale item is edited (quantity or batch changed), undo the
     * old deduction first so we can validate the new one against a clean slate.
     */
    public function updating(SaleItem $saleItem): void
    {
        if (! $saleItem->isDirty('quantity') && ! $saleItem->isDirty('batch_id')) {
            return;
        }

        $originalBatchId = $saleItem->getOriginal('batch_id');
        $originalQuantity = $saleItem->getOriginal('quantity');

        Batch::whereKey($originalBatchId)->increment('remaining_quantity', $originalQuantity);

        $newBatch = Batch::whereKey($saleItem->batch_id)->lockForUpdate()->first();

        if (! $newBatch || $newBatch->remaining_quantity < $saleItem->quantity) {
            // Roll back the temporary restore before failing.
            Batch::whereKey($originalBatchId)->decrement('remaining_quantity', $originalQuantity);

            throw ValidationException::withMessages([
                'batch_id' => "Only {$newBatch?->remaining_quantity} left in that batch — reduce the quantity or pick another batch.",
            ]);
        }
    }

    public function updated(SaleItem $saleItem): void
    {
        if ($saleItem->wasChanged('quantity') || $saleItem->wasChanged('batch_id')) {
            $saleItem->batch()->decrement('remaining_quantity', $saleItem->quantity);
        }
    }

    public function deleted(SaleItem $saleItem): void
    {
        $saleItem->batch()->increment('remaining_quantity', $saleItem->quantity);
    }
}
