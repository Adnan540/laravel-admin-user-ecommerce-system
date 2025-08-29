<?php

// app/Notifications/NewOrderNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {   // <-- no 'public $order'
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id'  => $this->order->id,
            'customer'  => $this->order->customer_name
                ?? optional($this->order->user)->name
                ?? 'Guest',
            'total'     => $this->order->total_amount,
            'placed_at' => $this->order->created_at,
        ];
    }
}
