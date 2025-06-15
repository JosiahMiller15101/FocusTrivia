<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class SelectDashboardController extends Controller
{
    public function show(User $user)
    {
        // Calculate stats for the selected user
        $submissions = $user->submissions;
        $totalAnswered = $submissions->count();
        $correctAnswers = $submissions->where('is_correct', true)->count();
        $wrongAnswers = $totalAnswered - $correctAnswers;
        $score = ($correctAnswers * 10) - ($wrongAnswers * 10);
        $correctPercentage = $totalAnswered > 0 ? round(($correctAnswers / $totalAnswered) * 100, 1) : 0;

        // Calculate player rank (excluding guests)
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

        return view('dashboard', [
            'user' => $user,
            'totalAnswered' => $totalAnswered,
            'correctAnswers' => $correctAnswers,
            'score' => $score,
            'playerRank' => $playerRank,
            'departmentRank' => $departmentRank,
        ]);
    }
}
