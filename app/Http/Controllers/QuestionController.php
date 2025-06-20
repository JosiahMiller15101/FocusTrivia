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
    public function showAuthenticated() {
        $user = Auth::user();
        $count = Question::count();
        if ($count === 0) {
            abort(404, 'No questions found.');
        }
        $startLocal = Carbon::create(2024, 1, 1, 0, 0, 0, 'America/Denver');
        $nowLocal = Carbon::now('America/Denver');
        $hoursPassed = $startLocal->diffInHours($nowLocal);
        $periods = (int) floor($hoursPassed / 12);
        $count = Question::count();
        $index = ($periods + 5) % $count;
        $question = Question::orderBy('id')->skip($index)->first();

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

    public function submit(Request $request)
{
    $user = Auth::user();
    $question = Question::findOrFail($request->input('question_id'));
    $selected = $request->input('answer');
    $isCorrect = $selected === $question->correct_answer;

    // Save the submission...
    QuestionSubmission::create([
        'user_id' => $user->id,
        'question_id' => $question->id,
        'selected_answer' => $selected,
        'is_correct' => $isCorrect,
    ]);

    return redirect()->back()->with([
        'submitted' => true,
        'is_correct' => $isCorrect,
        'correct_answer' => $question->correct_answer,
        'selected_answer' => $selected,
    ]);
}
}
