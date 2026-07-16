<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class StockAlertDigest extends Notification
{
    /**
     * @param Collection $lowStockMedicines Each item needs ->name, ->stock_sum, ->alert_threshold
     * @param Collection $expiringBatches   Each item needs ->medicine->name, ->expiry_date, ->remaining_quantity
     */
    public function __construct(
        public Collection $lowStockMedicines,
        public Collection $expiringBatches,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Pharmacera — Weekly stock alert')
            ->greeting("Hello {$notifiable->name},");

        if ($this->lowStockMedicines->isNotEmpty()) {
            $message->line('The following medicines are at or below their alert threshold:');

            foreach ($this->lowStockMedicines as $medicine) {
                $message->line("• {$medicine->name} — {$medicine->stock_sum} left (threshold: {$medicine->alert_threshold})");
            }
        }

        if ($this->expiringBatches->isNotEmpty()) {
            $message->line('The following batches are expiring within the next 90 days:');

            foreach ($this->expiringBatches as $batch) {
                $expiry = $batch->expiry_date->format('Y-m-d');
                $message->line("• {$batch->medicine->name} — expires {$expiry} ({$batch->remaining_quantity} units)");
            }
        }

        return $message
            ->action('Open Pharmacera', url('/admin'))
            ->line('This is an automated weekly summary.');
    }
}