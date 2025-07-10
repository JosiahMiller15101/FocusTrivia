<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

    // Mark only unread as read
    Notification::where('user_id', $userId)
        ->where('read', false)
        ->update(['read' => true]);

    // Eager-load comment and question
    $notifications = Notification::with('comment.question')
        ->where('user_id', $userId)
        ->latest()
        ->paginate(10);

    $unreadCount = Notification::where('user_id', $userId)
    ->where('read', false)
    ->count();

    return view('notifications.index', compact('notifications', 'unreadCount'));
    }
}

