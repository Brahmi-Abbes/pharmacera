<?php

namespace App\Observers;

use App\Models\Batch;
use App\Models\User;
use App\Notifications\LowStockAlert;
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

        $stockAfter = $medicine->total_stock;
        $stockBefore = $stockAfter - $batch->remaining_quantity + $batch->getOriginal('remaining_quantity');

        $justWentEmpty = $stockBefore > 0 && $stockAfter <= 0;

        // Mutually exclusive with justWentEmpty — if stock skipped straight from
        // healthy to zero in one sale, only the out-of-stock email fires below,
        // not both, to avoid two notifications for the same underlying event.
        $justWentLow = $stockAfter > 0
            && $stockBefore > $medicine->alert_threshold
            && $stockAfter <= $medicine->alert_threshold;

        if (! $justWentEmpty && ! $justWentLow) {
            return;
        }

        $recipients = User::role(['admin', 'pharmacist'])->get();

        foreach ($recipients as $user) {
            $user->notify(
                $justWentEmpty
                    ? new OutOfStockAlert($medicine)
                    : new LowStockAlert($medicine)
            );
        }
    }
}
