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
        ]);

        QuestionComment::create([
            'user_id' => Auth::id(),
            'question_id' => $request->question_id,
            'comment' => $request->comment,
        ]);

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

    // Delete any existing reaction by this user on this comment (regardless of type)
    \App\Models\CommentReaction::where('comment_id', $commentId)
        ->where('user_id', $userId)
        ->delete();

    // Add new reaction
    \App\Models\CommentReaction::create([
        'comment_id' => $commentId,
        'user_id' => $userId,
        'type' => $newType,
    ]);

    // Return all updated counts for the comment
    $counts = \App\Models\CommentReaction::where('comment_id', $commentId)
        ->selectRaw('type, COUNT(*) as count')
        ->groupBy('type')
        ->pluck('count', 'type');

    return response()->json(['status' => 'added', 'counts' => $counts]);
    }
}
