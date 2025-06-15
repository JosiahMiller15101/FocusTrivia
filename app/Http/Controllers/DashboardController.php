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

    // 1. Get all users with their score, accuracy, and total answered (excluding guests)
    $allUsers = User::with('submissions')->get()->filter(function ($u) {
        return strtolower(trim($u->department)) !== 'guest';
    })->map(function ($u) {
        $correct = $u->submissions->where('is_correct', true)->count();
        $total = $u->submissions->count();
        $wrong = $total - $correct;
        $u->accuracy = $total > 0 ? round($correct / $total * 100, 1) : 0;
        $u->total_answered = $total;
        $u->score = ($correct * 10) - ($wrong * 10);
        return $u;
    })->sortByDesc(function ($u) {
        // Sort by score DESC, then by total_answered DESC
        return [$u->score, $u->total_answered];
    })->values();

    // 2. Determine player's rank (1-based)
    $playerRank = $allUsers->search(fn($u) => $u->id === $user->id);
    $playerRank = $playerRank !== false ? $playerRank + 1 : 'N/A';

    // 3. Get department stats using new score_per_player formula (excluding guests)
    $departments = $allUsers->groupBy('department')
        ->filter(function ($users, $dept) {
            return strtolower(trim($dept)) !== 'guest';
        })
        ->map(function ($users, $dept) {
            $totalScore = $users->sum('score');
            $numPlayers = $users->count();
            $scorePerPlayer = $numPlayers > 0 ? $totalScore / sqrt($numPlayers) : 0;
            $averageAccuracy = $users->avg('accuracy');
            return [
                'department' => $dept,
                'score_per_player' => $scorePerPlayer,
                'total_score' => $totalScore,
                'average_accuracy' => $averageAccuracy,
            ];
        })
        ->sortByDesc('score_per_player')
        ->values();

    $departmentRank = $departments->search(fn($d) => $d['department'] === $user->department);
    $departmentRank = $departmentRank !== false ? $departmentRank + 1 : 'N/A';

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
