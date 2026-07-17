<?php

namespace App\Notifications;

use App\Models\Medicine;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    public function __construct(
        public Medicine $medicine,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Pharmacera — {$this->medicine->name} is running low")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->medicine->name} has dropped to {$this->medicine->total_stock} units, at or below its alert threshold of {$this->medicine->alert_threshold}.")
            ->line('It is still available to sell, but you may want to reorder soon.')
            ->action('Open medicine', url('/admin/medicines/'.$this->medicine->id.'/edit'));
    }
}