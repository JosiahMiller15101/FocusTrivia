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

        // Eager-load only needed fields for comment and question
        $notifications = Notification::with([
                'comment:id,comment,question_id',
                'comment.question:id,question'
            ])
            ->where('user_id', $userId)
            ->select('id', 'type', 'message', 'reaction_type', 'comment_id', 'created_at')
            ->latest()
            ->paginate(10);

        $unreadCount = Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function apiList()
    {
        $userId = auth()->id();
        $notifications = Notification::with(['comment.question', 'comment.replies', 'comment.reactions'])
            ->where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get();
        return response()->json($notifications);
    }
}

