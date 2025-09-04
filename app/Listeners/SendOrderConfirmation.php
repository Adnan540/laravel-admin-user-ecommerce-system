<?php
// app/Listeners/SendOrderConfirmation.php
namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderPlacedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        // Re-hydrated model here thanks to SerializesModels
        $order = $event->order->load(['items.product', 'user']);

        $recipient = $order->user?->email; // or $order->customer_email
        if (!$recipient) return;

        Mail::to($recipient)->queue(new OrderPlacedMail($order));
    }
}
