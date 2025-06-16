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
            'type' => 'required|string',
        ]);

        $user = Auth::user();
        $commentId = $request->comment_id;
        $type = $request->type;

        $reaction = \App\Models\CommentReaction::where('comment_id', $commentId)
            ->where('user_id', $user->id)
            ->where('type', $type)
            ->first();

        if ($reaction) {
            $reaction->delete(); // Toggle off
            $status = 'removed';
        } else {
            \App\Models\CommentReaction::create([
                'comment_id' => $commentId,
                'user_id' => $user->id,
                'type' => $type,
            ]);
            $status = 'added';
        }

        $count = \App\Models\CommentReaction::where('comment_id', $commentId)
            ->where('type', $type)
            ->count();

        return response()->json(['status' => $status, 'count' => $count]);
    }
}
