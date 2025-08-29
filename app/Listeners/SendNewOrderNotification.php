<?php

// app/Listeners/SendNewOrderNotification.php
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Models\Order;

class SendNewOrderNotification
{
    public bool $afterCommit = true; // ensures it runs after DB commit

    public function handle(\App\Events\OrderPlaced $event): void
    {
        $order = $event->$order;
        User::whereIn('role', ['admin', 'superadmin'])->get()
            ->each(fn($u) => $u->notify(new NewOrderNotification($order)));
    }
}
