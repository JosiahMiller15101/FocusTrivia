<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\QuestionSubmission;
use App\Models\QuestionComment;


class QuestionController extends Controller
{
     public function show()
    {
        $questions = Question::orderBy('id')->get();

        if ($questions->isEmpty()) {
            abort(404, 'No questions found.');
        }

        // Use a fixed start date for the question cycle
        $start = Carbon::create(2024, 1, 1, 0, 0, 0, 'UTC'); // Set this to your actual trivia launch date
        $now = Carbon::now('UTC');
        $periods = floor($start->diffInHours($now) / 12);
        $index = $periods % $questions->count();
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

        // Use a fixed start date for the question cycle
        $start = Carbon::create(2024, 1, 1, 0, 0, 0, 'America/Denver'); // Set this to your actual trivia launch date
        $now = Carbon::now('America/Denver');
        $periods = floor($start->diffInHours($now) / 12 + 1);
        $index = $periods % $questions->count();
        $question = $questions[$index];

        $alreadySubmitted = QuestionSubmission::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->exists();

        $allAnswers = json_decode($question->incorrect_answers);
        $allAnswers[] = $question->correct_answer;
        shuffle($allAnswers);

        $comments = [];
        if ($alreadySubmitted) {
            $comments = QuestionComment::with('user')
                ->where('question_id', $question->id)
                ->latest()
                ->get();
        }

        return view('question', [
            'question' => $question,
            'answers' => $allAnswers,
            'alreadySubmitted' => $alreadySubmitted,
            'comments' => $comments,
        ]);
    }
}
