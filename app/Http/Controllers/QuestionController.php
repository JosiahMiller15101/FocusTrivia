<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\QuestionSubmission;


class QuestionController extends Controller
{
     public function show()
    {
        $questions = Question::orderBy('id')->get();

        if ($questions->isEmpty()) {
            abort(404, 'No questions found.');
        }

        $dayOfMonth = Carbon::now()->day;
        $index = $dayOfMonth % $questions->count();

        $question = $questions[$index];

        $allAnswers = json_decode($question->incorrect_answers);
        $allAnswers[] = $question->correct_answer;
        shuffle($allAnswers);

        return view('question', [
            'question' => $question,
            'answers' => $allAnswers,
            'alreadySubmitted' => false
        ]);
    }

    public function showAuthenticated() {
        $user = Auth::user();
        $questions = Question::orderBy('id')->get();

        if ($questions->isEmpty()) {
            abort(404, 'No questions found.');
        }

        $day = now()->day;
        $index = ($day-1) % $questions->count();
        $question = $questions[$index];

        $alreadySubmitted = QuestionSubmission::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->exists();

        $allAnswers = json_decode($question->incorrect_answers);
        $allAnswers[] = $question->correct_answer;
        shuffle($allAnswers);

        return view('question', [
            'question' => $question,
            'answers' => $allAnswers,
            'alreadySubmitted' => $alreadySubmitted
        ]);
    }
}
