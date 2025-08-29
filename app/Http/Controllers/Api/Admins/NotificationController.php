<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // GET /api/admin/notifications?unread=1
    public function index(Request $request)
    {
        $user = $request->user(); // admin user (must use Notifiable)
        $onlyUnread = $request->boolean('unread');

        $query = $onlyUnread ? $user->unreadNotifications() : $user->notifications();

        return response()->json([
            'data' => $query->latest()->limit(20)->get(),
        ]);
    }

    // POST /api/admin/notifications/{id}/read
    public function markRead(Request $request, string $id)
    {
        $request->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }

    // POST /api/admin/notifications/read-all
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }
}
