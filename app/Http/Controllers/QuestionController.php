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

        // New logic: reload question at midnight and at 12pm
        $now = Carbon::now();
        $day = $now->day;
        $half = $now->hour < 12 ? 0 : 1;
        $index = (($day - 1) * 2 + $half) % $questions->count();
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

        // New logic: reload question at midnight and at 12pm
        $now = Carbon::now();
        $day = $now->day;
        $half = $now->hour < 12 ? 0 : 1;
        $index = (($day - 1) * 2 + $half) % $questions->count();
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
