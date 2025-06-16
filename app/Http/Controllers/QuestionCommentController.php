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
}
