<?php

namespace App\Observers;

use App\Models\Batch;
use App\Models\User;
use App\Notifications\OutOfStockAlert;

class BatchObserver
{
    /**
     * Fires after ANY save to a batch's remaining_quantity — whether that
     * came from a sale (via SaleItemObserver decrementing stock) or a
     * manual correction made directly on the Batch form. Either way, if
     * the medicine's total stock across all its batches just crossed from
     * available into empty, we notify immediately instead of waiting for
     * the next daily digest.
     */
    public function updated(Batch $batch): void
    {
        if (! $batch->isDirty('remaining_quantity')) {
            return;
        }

        $medicine = $batch->medicine;

        // total_stock is a live sum across all of this medicine's batches,
        // queried fresh — since this fires after the batch was already
        // saved, it reflects the state *after* this change.
        $stockAfter = $medicine->total_stock;

        // Reconstruct what the medicine's total stock was immediately
        // before this specific batch's change, by undoing just this
        // batch's own delta from the current total. Works correctly even
        // when the medicine has several other batches untouched by this save.
        $stockBefore = $stockAfter - $batch->remaining_quantity + $batch->getOriginal('remaining_quantity');

        $justWentEmpty = $stockBefore > 0 && $stockAfter <= 0;

        if (! $justWentEmpty) {
            return;
        }

        $recipients = User::role(['admin', 'pharmacist'])->get();

        foreach ($recipients as $user) {
            $user->notify(new OutOfStockAlert($medicine));
        }
    }
}
