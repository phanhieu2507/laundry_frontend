<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->id;
        $notifications = Notification::where('user_id', $userId)
                                     ->orderBy('created_at', 'desc')
                                     ->get();

        return response()->json($notifications);
    }

    public function markAllAsRead(Request $request)
    {
        $userId = $request->id;
        Notification::where('user_id', $userId)->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
