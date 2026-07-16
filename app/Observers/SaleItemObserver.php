<?php

namespace App\Observers;

use App\Models\Batch;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleItemObserver
{
    /**
     * Before a sale item is created, make sure the batch actually has enough
     * remaining stock. Wrapped in a transaction so lockForUpdate() actually
     * holds the row lock until this check (and the insert that follows)
     * completes — without a transaction, the lock is released the instant
     * this query finishes, which defeats the whole point of locking.
     */
    public function creating(SaleItem $saleItem): void
    {
        DB::transaction(function () use ($saleItem) {
            $batch = Batch::whereKey($saleItem->batch_id)->lockForUpdate()->first();

            if (! $batch || $batch->remaining_quantity < $saleItem->quantity) {
                throw ValidationException::withMessages([
                    'batch_id' => "Only {$batch?->remaining_quantity} left in that batch — reduce the quantity or pick another batch.",
                ]);
            }
        });
    }

    public function created(SaleItem $saleItem): void
    {
        $this->adjustBatchStock($saleItem->batch_id, -$saleItem->quantity);
    }

    /**
     * If an existing sale item is edited (quantity or batch changed), undo the
     * old deduction first so we can validate the new one against a clean slate.
     * The whole restore -> validate -> (rollback on failure) sequence runs in
     * one transaction so it's atomic — either the full switch succeeds, or
     * none of it is applied.
     */
    public function updating(SaleItem $saleItem): void
    {
        if (! $saleItem->isDirty('quantity') && ! $saleItem->isDirty('batch_id')) {
            return;
        }

        $originalBatchId = $saleItem->getOriginal('batch_id');
        $originalQuantity = $saleItem->getOriginal('quantity');

        DB::transaction(function () use ($saleItem, $originalBatchId, $originalQuantity) {
            // Temporarily restore the old batch's stock so we can validate against it cleanly.
            $this->adjustBatchStock($originalBatchId, $originalQuantity);

            $newBatch = Batch::whereKey($saleItem->batch_id)->lockForUpdate()->first();

            if (! $newBatch || $newBatch->remaining_quantity < $saleItem->quantity) {
                // Roll back the temporary restore before failing.
                $this->adjustBatchStock($originalBatchId, -$originalQuantity);

                throw ValidationException::withMessages([
                    'batch_id' => "Only {$newBatch?->remaining_quantity} left in that batch — reduce the quantity or pick another batch.",
                ]);
            }
        });
    }

    public function updated(SaleItem $saleItem): void
    {
        if ($saleItem->wasChanged('quantity') || $saleItem->wasChanged('batch_id')) {
            $this->adjustBatchStock($saleItem->batch_id, -$saleItem->quantity);
        }
    }

    public function deleted(SaleItem $saleItem): void
    {
        $this->adjustBatchStock($saleItem->batch_id, $saleItem->quantity);
    }

    /**
     * Load the batch, mutate remaining_quantity, and save() it — deliberately
     * NOT using decrement()/increment() on the query builder, because those
     * bypass Eloquent model events and would leave LogsActivity blind to
     * every stock change caused by a sale. Wrapped in a transaction so the
     * lockForUpdate() actually protects the read-modify-write against a
     * concurrent sale touching the same batch at the same time.
     */
    private function adjustBatchStock(int $batchId, int $delta): void
    {
        DB::transaction(function () use ($batchId, $delta) {
            $batch = Batch::whereKey($batchId)->lockForUpdate()->first();

            if (! $batch) {
                return;
            }

            $batch->remaining_quantity = $batch->remaining_quantity + $delta;
            $batch->save();
        });
    }
}