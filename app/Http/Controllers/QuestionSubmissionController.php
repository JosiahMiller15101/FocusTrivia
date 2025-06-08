<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\QuestionSubmission;
use Illuminate\Support\Facades\Auth;

class QuestionSubmissionController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        $question = Question::find($request->question_id);

        if (!$question) {
            return back()->with('error', 'Question not found.');
        }

        $isCorrect = $request->answer === $question->correct_answer;

        QuestionSubmission::create([
            'user_id' => $user->id,
            'question_id' => $question->id,
            'submitted_at' => now(),
            'is_correct' => $isCorrect,
        ]);

        if ($isCorrect) {
            return back()->with('success', 'Correct! Well done. See you tomorrow.');
        } else {
            return back()->with('error', "Not quite, it's alright though, we'll get 'em tomorrow. Correct answer: {$question->correct_answer}");
        }    
    }
}
