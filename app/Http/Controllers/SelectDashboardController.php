<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\QuestionSubmission;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SelectDashboardController extends Controller
{
    public function show(User $user)
    {
        // Calculate stats for the selected user
        $submissions = $user->submissions;
        $totalAnswered = $submissions->count();
        $correctAnswers = $submissions->where('is_correct', true)->count();
        $wrongAnswers = $totalAnswered - $correctAnswers;
        $score = ($correctAnswers * 100) - ($wrongAnswers * 100);
        $correctPercentage = $totalAnswered > 0 ? round(($correctAnswers / $totalAnswered) * 100, 1) : 0;

        // Calculate player rank (including guests)
        $allUsers = User::select('id', 'first_name', 'last_name', 'department')
            ->with(['submissions' => function($q) {
                $q->select('id', 'user_id', 'is_correct');
            }])->get()->map(function ($u) {
            $correct = $u->submissions->where('is_correct', true)->count();
            $total = $u->submissions->count();
            $wrong = $total - $correct;
            $u->accuracy = $total > 0 ? round($correct / $total * 100, 1) : 0;
            $u->total_answered = $total;
            $u->score = ($correct * 100) - ($wrong * 100);
            return $u;
        })->sortByDesc(function ($u) {
            // Sort by score DESC, then by total_answered DESC
            return [$u->score, $u->total_answered];
        })->values();
        $playerRank = $allUsers->search(function ($u) use ($user) {
            return $u->id === $user->id;
        });
        $playerRank = $playerRank !== false ? $playerRank + 1 : 'N/A';

        // Calculate department rank (exclude guests, use score_per_player)
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
        $departmentRank = $departments->search(function ($dept) use ($user) {
            return $dept['department'] === $user->department;
        });
        $departmentRank = $departmentRank !== false ? $departmentRank + 1 : 'N/A';

        // Calculate department player rank for the selected user (including guests)
        $department = strtolower(trim($user->department));
        // Only load users in the same department
        $departmentUsers = User::select('id', 'first_name', 'last_name', 'department')
            ->whereRaw('LOWER(TRIM(department)) = ?', [$department])
            ->with(['submissions' => function($q) {
                $q->select('id', 'user_id', 'is_correct');
            }])->get();
        $departmentPlayers = $departmentUsers->map(function ($u) {
            $total = $u->submissions->count();
            $correct = $u->submissions->where('is_correct', true)->count();
            $score = ($correct * 100) - (($total - $correct) * 100);
            return [
                'id' => $u->id,
                'name' => $u->first_name . ' ' . $u->last_name,
                'score' => $score,
                'accuracy' => $total > 0 ? round(($correct / $total) * 100, 1) : 0,
                'total_answered' => $total,
            ];
        })->sortByDesc(function ($p) {
            return [$p['score'], $p['total_answered']];
        })->values();
        $departmentPlayerRank = $departmentPlayers->search(function ($p) use ($user) {
            return (int)$p['id'] === (int)$user->id;
        });
        $departmentPlayerRank = $departmentPlayerRank !== false ? $departmentPlayerRank + 1 : 'N/A';

        $perPage = 3;
        // $history = $user->submissions()->with('question')->orderByDesc('submitted_at')->paginate($perPage);
       $authUser = Auth::user();

// Check if user answered today's question
$now = Carbon::now();

// Determine the start of the current question window
$currentWindowStart = $now->copy()->hour < 12
    ? $now->copy()->startOfDay()       // 12am window
    : $now->copy()->setTime(12, 0, 0); // 12pm window

// Check if user has submitted during this window
$hasAnsweredCurrentWindow = QuestionSubmission::where('user_id', $authUser->id)
    ->where('submitted_at', '>=', $currentWindowStart)
    ->exists();

// Paginate submissions (all)
$paginated = $user->submissions()
    ->with('question')
    ->orderByDesc('submitted_at')
    ->paginate(4); // Don't change this perPage logic

// Show submissions only if current user is owner or has answered in the current window
$displayed = $paginated->map(function ($submission) use ($authUser, $hasAnsweredCurrentWindow) {
    if ($authUser->id === $submission->user_id || $hasAnsweredCurrentWindow) {
        return $submission;
    }
    return null;
});

// You need to preserve the total and current pagination state
$history = new \Illuminate\Pagination\LengthAwarePaginator(
    $displayed->filter()->values(), // Filter nulls from map
    $paginated->total(),            // Keep original total
    $paginated->perPage(),
    $paginated->currentPage(),
    ['path' => request()->url(), 'query' => request()->query()]
);

        $streak = app(DashboardController::class)->calculateStreak($user->id);
        return view('dashboard', [
            'user' => $user,
            'totalAnswered' => $totalAnswered,
            'correctAnswers' => $correctAnswers,
            'score' => $score,
            'playerRank' => $playerRank,
            'departmentRank' => $departmentRank,
            'departmentPlayerRank' => $departmentPlayerRank,
            'history' => $history,
            'streak' => $streak,
            'hasAnsweredCurrentWindow' => $hasAnsweredCurrentWindow,
        ]);
    }
}
