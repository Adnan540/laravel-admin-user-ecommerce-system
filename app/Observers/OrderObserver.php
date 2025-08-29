<?php

namespace App\Observers; // ✅ add this
// app/Observers/OrderObserver.php
use App\Models\User;
use App\Notifications\NewOrderNotification;

class OrderObserver
{
    public function created(\App\Models\Order $order): void
    {
        User::whereIn('role', ['admin', 'superadmin'])->get()
            ->each(fn($u) => $u->notify(new NewOrderNotification($order)));
    }
}
