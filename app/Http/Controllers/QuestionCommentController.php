<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionComment;
use Illuminate\Support\Facades\Auth;

class QuestionCommentController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'question_id' => 'required|exists:questions,id',
        'comment' => 'required|string|max:1000',
        'parent_id' => 'nullable|exists:question_comments,id',
    ]);

    $comment = QuestionComment::create([
        'user_id' => Auth::id(),
        'question_id' => $request->question_id,
        'comment' => $request->comment,
        'parent_id' => $request->parent_id,
    ]);

    if ($request->parent_id) {
        $parent = QuestionComment::find($request->parent_id);
        if ($parent && $parent->user_id !== Auth::id()) {
            \App\Models\Notification::create([
                'user_id' => $parent->user_id,
                'comment_id' => $parent->id, // associate notification with parent comment
                'type' => 'reply',
                'message' => Auth::user()->first_name . ' ' . Auth::user()->last_name . ' replied to your comment.',
                'read' => false,
            ]);
        }
    }

    return back()->with('success', 'Comment posted!');
}

    public function react(Request $request)
{
    $request->validate([
        'comment_id' => 'required|exists:question_comments,id',
        'type' => 'required|string|in:like,laughing,crying,angry,dislike',
    ]);

    $userId = Auth::id();
    $commentId = $request->comment_id;
    $newType = $request->type;

    // Delete existing reactions by this user on this comment
    \App\Models\CommentReaction::where('comment_id', $commentId)
        ->where('user_id', $userId)
        ->delete();

    // Add new reaction
    \App\Models\CommentReaction::create([
        'comment_id' => $commentId,
        'user_id' => $userId,
        'type' => $newType,
    ]);

    $comment = QuestionComment::find($commentId);

    if ($comment && $comment->user_id !== $userId) {
        \App\Models\Notification::create([
            'user_id' => $comment->user_id,
            'comment_id' => $comment->id,  // associate notification with comment
            'type' => 'reaction',
            'message' => Auth::user()->first_name . ' ' . Auth::user()->last_name . ' replied to your comment.',
            'read' => false,
        ]);
    }

    // Return updated reaction counts
    $counts = \App\Models\CommentReaction::where('comment_id', $commentId)
        ->selectRaw('type, COUNT(*) as count')
        ->groupBy('type')
        ->pluck('count', 'type');

    return response()->json(['status' => 'added', 'counts' => $counts]);
}


    public function destroy($id)
    {
        $comment = QuestionComment::findOrFail($id);
        if (auth()->id() !== $comment->user_id) {
            abort(403, 'Unauthorized');
        }
        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }
}
