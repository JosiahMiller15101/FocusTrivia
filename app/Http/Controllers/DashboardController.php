<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\QuestionSubmission;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
      public function index()
{
    $user = Auth::user();

    $totalAnswered = QuestionSubmission::where('user_id', $user->id)->count();
    $correctAnswers = QuestionSubmission::where('user_id', $user->id)->where('is_correct', true)->count();
    $wrongAnswers = $totalAnswered - $correctAnswers;
    $score = ($correctAnswers * 10) - ($wrongAnswers * 10);
    $accuracy = $totalAnswered > 0 ? ($correctAnswers / $totalAnswered) * 100 : 0;

    // 1. Get all users with their accuracy and total answered
    $allUsers = User::with('submissions')->get()->filter(function ($u) {
        return strtolower(trim($u->department)) !== 'guest';
    })->map(function ($u) {
        $correct = $u->submissions->where('is_correct', true)->count();
        $total = $u->submissions->count();
        $u->accuracy = $total > 0 ? round($correct / $total * 100, 1) : 0;
        $u->total_answered = $total;
        return $u;
    })->sortByDesc(function ($u) {
        // Sort by accuracy DESC, then by total_answered DESC
        return sprintf('%08.1f%08d', $u->accuracy, $u->total_answered);
    })->values();

    // 2. Determine player's rank (1-based)
    $playerRank = $allUsers->search(fn($u) => $u->id === $user->id) + 1;

    // 3. Get department stats using new score_per_player formula
    $departments = $allUsers->groupBy('department')
        ->map(function ($users, $dept) {
            $totalScore = $users->sum('score');
            $numPlayers = $users->count();
            $scorePerPlayer = $numPlayers > 0 ? $totalScore / sqrt($numPlayers) : 0;
            return [
                'department' => $dept,
                'score_per_player' => $scorePerPlayer,
            ];
        })
        ->sortByDesc('score_per_player')
        ->values();

    $departmentRank = $departments->search(fn($d) => $d['department'] === $user->department) + 1;

    return view('dashboard', [
        'first_name' => $user->first_name,
        'totalAnswered' => $totalAnswered,
        'correctAnswers' => $correctAnswers,
        'score' => $score,
        'playerRank' => $playerRank,
        'departmentRank' => $departmentRank,
    ]);
}
}
