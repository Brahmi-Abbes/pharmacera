<?php

namespace App\Notifications;

use App\Models\Medicine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OutOfStockAlert extends Notification implements ShouldQueue
{
    use Queueable;

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
            ->subject("Pharmacera — {$this->medicine->name} is out of stock")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->medicine->name} has just run out of stock across all batches.")
            ->line('No units are currently available to sell.')
            ->action('Open medicine', url('/admin/medicines/'.$this->medicine->id.'/edit'))
            ->line('You are receiving this immediately because stock just reached zero — separate from the daily digest.');
    }
}
