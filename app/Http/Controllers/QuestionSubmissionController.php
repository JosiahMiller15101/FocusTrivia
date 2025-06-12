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

        // 🚨 Check if the user already submitted an answer for this question
        $alreadySubmitted = QuestionSubmission::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->exists();

        if ($alreadySubmitted) {
            return back()->with('error', 'You have already answered this question.');
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
            return back()->with('error', "Not quite, it's alright though, we'll get 'em tomorrow. CORRECT ANSWER: {$question->correct_answer}");
    }    
    }
}
