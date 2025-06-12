<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class LeaderboardController extends Controller
{

public function index()
{
    // Get all users (excluding Guests), calculate accuracy, and paginate manually
    $users = User::with('submissions')
        ->get()
        ->filter(function ($user) {
            return strtolower(trim($user->department)) !== 'guest';
        })
        ->map(function ($user) {
            $correct = $user->submissions->where('is_correct', true)->count();
            $total = $user->submissions->count();
            $user->accuracy = $total > 0 ? round($correct / $total * 100, 1) : 0;
            $user->total_answered = $total;
            return $user;
        })
        ->sortByDesc(function ($user) {
            // Sort by accuracy DESC, then by total_answered DESC
            return sprintf('%08.1f%08d', $user->accuracy, $user->total_answered);
        })
        ->values();

    // Manual pagination for the users collection
    $perPage = 10;
    $page = request('page', 1);
    $paginatedUsers = new LengthAwarePaginator(
        $users->forPage($page, $perPage)->values(), // reset keys here!
        $users->count(),
        $perPage,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    // Leaderboard for top departments by average accuracy (excluding 'guest'), cached with Redis
    $departments = Cache::remember('top_departments', now()->addMinutes(1), function () {
        return User::whereNotNull('department')
            ->with('submissions')
            ->get()
            ->groupBy('department')
            ->filter(function ($users, $dept) {
                return strtolower(trim($dept)) !== 'guest';
            })
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
            ->values();
    });

    return view('leaderboard', [
        'users' => $paginatedUsers,
        'departments' => $departments,
    ]);
}

}
