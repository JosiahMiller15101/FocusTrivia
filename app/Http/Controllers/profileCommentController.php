<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProfileComment;
use App\Models\Notification;
use App\Models\User;

class ProfileCommentController extends Controller
{
    public function store(Request $request, $userId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment = ProfileComment::create([
            'comment' => $request->comment,
            'user_id' => $userId,
            'author_id' => auth()->id(),
        ]);

        Notification::create([
            'user_id' => $userId,
            'type' => 'profile_comment',
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' left a comment on your dashboard profile.',
            'read' => false,
            'comment_id' => $comment->id, // Associate notification with the comment
        ]);

        return redirect()->back()->with('success', 'Comment posted!');
    }

    public function index($userId)
    {
        $comments = ProfileComment::with('author')
        ->where('user_id', $userId)
        ->latest()
        ->paginate(10);

        $user = User::findOrFail($userId);

        return view('profileComment', compact('comments', 'user'));
    }

    public function notifications($userId)
    {
        $comments = ProfileComment::with('author')
        ->where('user_id', $userId)
        ->latest()
        ->paginate(10);

        $user = User::findOrFail($userId);

        return view('notifications.index', compact('comments', 'user'));
    }
}

