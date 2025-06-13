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
            return sprintf('%08d%08d', $u->score, $u->total_answered);
        })->values();
        $playerRank = $allUsers->search(function ($u) use ($user) {
            return $u->id === $user->id;
        });
        $playerRank = $playerRank !== false ? $playerRank + 1 : 'N/A';

        // Calculate department rank
        $departments = $allUsers->groupBy('department')->map(function ($users, $dept) {
            $accuracies = $users->map(function ($u) {
                return $u->accuracy;
            });
            return [
                'department' => $dept,
                'average_accuracy' => $accuracies->average(),
            ];
        })->sortByDesc('average_accuracy')->values();
        $departmentRank = $departments->search(function ($dept) use ($user) {
            return $dept['department'] === $user->department;
        });
        $departmentRank = $departmentRank !== false ? $departmentRank + 1 : 'N/A';

        return view('dashboard', [
            'user' => $user,
            'totalAnswered' => $totalAnswered,
            'correctAnswers' => $correctAnswers,
            'score' => $score,
            'correctPercentage' => $correctPercentage,
            'playerRank' => $playerRank,
            'departmentRank' => $departmentRank,
        ]);
    }
}
