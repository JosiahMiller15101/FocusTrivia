<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class LeaderboardController extends Controller
{

public function index()
{
    // Leaderboard for top 10 users 
    $users = User::with('submissions')
    ->get()
    ->map(function ($user) {
        $correct = $user->submissions->where('is_correct', true)->count();
        $total = $user->submissions->count();
        $accuracy = $total > 0 ? round($correct / $total * 100, 1) : 0;
        $user->accuracy = $accuracy;
        return $user;
    })
    ->sortByDesc('accuracy')
    ->values()
    ->take(10);

    // New: Leaderboard for top 10 departments by average accuracy
    $departments = User::whereNotNull('department')
    ->with('submissions')
    ->get()
    ->groupBy('department')
    ->map(function ($users, $dept) {
        $accuracies = $users->map(function ($user) {
            $correct = $user->submissions->where('is_correct', true)->count();
            $total = $user->submissions->count();
            return $total > 0 ? ($correct / $total) * 100 : 0;
        });

        return [
            'department' => $dept,
            'average_accuracy' => round($accuracies->average(), 1),
        ];
    })
    ->sortByDesc('average_accuracy')
    ->values()
    ->take(10);

    return view('leaderboard', [
        'users' => $users,
        'departments' => $departments,
    ]);
}

}
