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
        $nowLocal   = Carbon::now('America/Denver');
        $daysPassed = $startLocal->diffInDays($nowLocal);
        $halfOfDay = ($nowLocal->hour < 12) ? 0 : 1;
        // $halfOfDay  = (int) floor($nowLocal->hour / 12); // 2 before noon, 1 after --> //am=2, pm=1
        $periods = $daysPassed * 2 + $halfOfDay;  
        $index   = (($periods + 9) % $count); // 0-based index
        $question = Question::orderBy('id')->skip($index)->first();

        // as of 6/21/2025 (2:43 PM moutnain time) : 
        // days passed = 538.6070627353
        // periods = 1077.2141254706
        // half = 0
        // index = 16
        // count = 51

        $alreadySubmitted = QuestionSubmission::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->exists();

        $allAnswers = json_decode($question->incorrect_answers);
        $allAnswers[] = $question->correct_answer;
        shuffle($allAnswers);

        $comments = [];
        if ($alreadySubmitted) {
            $comments = QuestionComment::with(['user', 'reactions', 'replies.user', 'replies.reactions', 'replies.replies'])
                ->where('question_id', $question->id)
                ->whereNull('parent_id')
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
