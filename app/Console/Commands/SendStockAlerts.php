<?php

namespace App\Console\Commands;

use App\Models\Medicine;
use App\Models\Batch;
use App\Models\User;
use App\Notifications\StockAlertDigest;
use Illuminate\Console\Command;

class SendStockAlerts extends Command
{
    protected $signature = 'pharmacera:send-stock-alerts';
    protected $description = 'Email admins and pharmacists a daily digest of low-stock medicines and expiring batches.';

    public function handle(): void
    {
        $lowStock = Medicine::all()->filter(fn ($m) => $m->is_low_stock)->values();

        $expiring = Batch::where('remaining_quantity', '>', 0)
            ->whereBetween('expiry_date', [now(), now()->addDays(90)])
            ->with('medicine')
            ->orderBy('expiry_date')
            ->get();

        if ($lowStock->isEmpty() && $expiring->isEmpty()) {
            $this->info('Nothing to report — no alerts sent.');
            return;
        }

        $recipients = User::role(['admin', 'pharmacist'])->get();

        foreach ($recipients as $user) {
            $user->notify(new StockAlertDigest($lowStock, $expiring));
        }

        $this->info("Stock alert digest sent to {$recipients->count()} user(s).");
    }
}