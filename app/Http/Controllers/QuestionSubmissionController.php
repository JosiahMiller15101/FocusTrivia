<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\QuestionSubmission;
use Illuminate\Support\Facades\Auth;

class QuestionSubmissionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $question = Question::find($request->question_id);

        // Check if the user already submitted an answer for this question
        $alreadySubmitted = QuestionSubmission::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->exists();

        $isCorrect = trim(strtolower($request->answer)) === trim(strtolower($question->correct_answer));

        try {
            QuestionSubmission::create([
                'user_id' => $user->id,
                'question_id' => $question->id,
                'submitted_at' => now(),
                'is_correct' => $isCorrect,
                'answer' => $request->answer,
            ]);
        } catch (QueryException $e) {
            // Handle the case where the submission already exists
            if ($e->getCode() === '23000') { // Integrity constraint violation
                return back()->with('error', 'You have already submitted an answer for this question.');
            } else {
                return back()->with('error', 'An error occurred while submitting your answer. Please try again later.');
            }
        }
        
        if ($isCorrect) {
            return back()->with('success', 'Correct! Well done. See you again in a few hours.');
        } else {
            return back()->with('error', "Not quite. Its alright we'll get 'em next time");
        }    
    }
}
