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
            'comment' => $request->input('comment'),
            'user_id' => $userId,
            'author_id' => auth()->id(),
            'parent_id' => $request->input('parent_id'), 
        ]);

        Notification::create([
            'user_id' => $userId,
            'type' => 'profile_comment',
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' left a comment on your dashboard profile.',
            'read' => false,
            'comment_id' => null, // No question_comment association for profile comments
        ]);

        return redirect()->back()->with('success', 'Comment posted!');
    }

    public function index($userId)
    {
        $comments = ProfileComment::with(['author', 'replies.author'])
        ->where('user_id', $userId)
        ->whereNull('parent_id') 
        ->latest()
        ->paginate(10);

        $user = User::findOrFail($userId);

        return view('profileComments', compact('comments', 'user'));
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

    public function destroy($id)
    {
        $comment = ProfileComment::findOrFail($id);
        if (auth()->id() !== $comment->author_id) {
            abort(403, 'Unauthorized');
        }
        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }
}

