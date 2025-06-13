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
        $correctPercentage = $totalAnswered > 0 ? round(($correctAnswers / $totalAnswered) * 100, 1) : 0;

        // Calculate player rank (excluding guests)
        $allUsers = \App\Models\User::with('submissions')->get()->filter(function ($u) {
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
            'correctPercentage' => $correctPercentage,
            'playerRank' => $playerRank,
            'departmentRank' => $departmentRank,
        ]);
    }
}
