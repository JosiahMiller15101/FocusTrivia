<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProfileComment;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\ProfileCommentReplyNotification;

class ProfileCommentController extends Controller
{
    public function store(Request $request, $userId)
    {
         $request->validate([
        'comment' => 'required|string|max:1000',
    ]);

    $commentText = $request->input('comment');
    $comment = ProfileComment::create([
        'comment' => $commentText,
        'user_id' => $userId,
        'author_id' => auth()->id(),
        'parent_id' => $request->input('parent_id') ? intval($request->input('parent_id')) : null,
    ]);

    // Get profile owner's name
    $profileOwner = User::find($userId);
    $profileOwnerName = $profileOwner ? $profileOwner->first_name . ' ' . $profileOwner->last_name : 'their';

    // Notify parent comment author if this is a reply
    $sentToParent = false;
    if ($comment->parent_id) {
        $parentComment = ProfileComment::find($comment->parent_id);
        if (
            $parentComment &&
            $parentComment->author_id !== auth()->id() &&
            $parentComment->author_id != $userId
        ) {
            Notification::create([
                'user_id' => $parentComment->author_id,
                'type' => 'profile_comment_reply',
                'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' replied to your comment on ' . $profileOwnerName . "'s profile.",
                'comment_content' => $comment->comment ?? $commentText,
                'read' => false,
                'comment_id' => null,
                'profile_comment_id' => $comment->id,
            ]);
            $sentToParent = true;
        }
    }

    // Notify profile owner if not self and not already notified as parent comment author
    if ($userId !== auth()->id() && (!$sentToParent || ($parentComment && $parentComment->author_id != $userId))) {
        $message = $comment->parent_id
            ? auth()->user()->first_name . ' ' . auth()->user()->last_name . ' replied to a comment on your dashboard profile.'
            : auth()->user()->first_name . ' ' . auth()->user()->last_name . ' left a comment on your dashboard profile.';

        Notification::create([
            'user_id' => $userId,
            'type' => 'profile_comment',
            'message' => $message,
            'comment_content' => $comment->comment ?? $commentText,
            'read' => false,
            'comment_id' => null,
            'profile_comment_id' => $comment->id,
        ]);
    }

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
    $notifications = Notification::where('user_id', $userId)
        ->latest()
        ->paginate(10);

    $user = User::findOrFail($userId);

    return view('notifications.index', compact('notifications', 'user'));
}

    public function destroy($id)
    {
        $comment = ProfileComment::findOrFail($id);
        if ($comment && auth()->id() !== $comment->author_id) {
            abort(403, 'Unauthorized');
        }
        if ($comment) {
            $comment->delete();
        }
        return back()->with('success', 'Comment deleted.');
    }
}

