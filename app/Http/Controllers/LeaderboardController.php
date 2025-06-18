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
        });

    // Prepare a display array to avoid dirtying Eloquent models
    $displayUsers = collect();
    $sortedUsers = $users->map(function ($user) {
        $correct = $user->submissions->where('is_correct', true)->count();
        $total = $user->submissions->count();
        $wrong = $total - $correct;
        $display = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'department' => $user->department,
            'profile_image' => $user->profile_image,
            'previous_rank' => $user->previous_rank,
            'display_accuracy' => $total > 0 ? round($correct / $total * 100, 1) : 0,
            'display_total_answered' => $total,
            'display_score' => ($correct * 10) - ($wrong * 10),
            'submissions' => $user->submissions,
        ];
        return (object) $display;
    })->sortByDesc(function ($user) {
        return [$user->display_score, $user->display_total_answered];
    })->values();

    // Assign current rank and compare with previous_rank
    foreach ($sortedUsers as $i => $displayUser) {
        $currentRank = $i + 1;
        $displayUser->current_rank = $currentRank;
        $displayUser->rank_movement = null;
        $user = $users->firstWhere('id', $displayUser->id);
        // If previous_rank is null, treat as a high value so first calculation always shows up arrow if not first place
        $previousRank = isset($user->previous_rank) && $user->previous_rank !== null ? $user->previous_rank : (count($sortedUsers) + 1);
        if ($previousRank > $currentRank) {
            $displayUser->rank_movement = 'up';
        } elseif ($previousRank < $currentRank) {
            $displayUser->rank_movement = 'down';
        }
        // Debug: log rank movement for troubleshooting
        // \Log::info("User {$user->id} prev: {$previousRank}, curr: {$currentRank}, move: {$displayUser->rank_movement}");
        $user->update(['previous_rank' => $currentRank]);
    }

    // Manual pagination for the users collection
    $perPage = 10;
    $page = request('page', 1);
    $paginatedUsers = new LengthAwarePaginator(
        $sortedUsers->forPage($page, $perPage)->values(),
        $sortedUsers->count(),
        $perPage,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    // Leaderboard for top departments by score per player (excluding 'guest'), cached with Redis
    $departments = Cache::remember('top_departments', now()->addMinutes(1), function () use ($sortedUsers) {
        return $sortedUsers
            ->groupBy('department')
            ->filter(function ($users, $dept) {
                return strtolower(trim($dept)) !== 'guest';
            })
            ->map(function ($users, $dept) {
                $totalScore = $users->sum('display_score');
                $averageAccuracy = $users->avg('display_accuracy');
                $numPlayers = $users->count();
                $scorePerPlayer = $numPlayers > 0 ? $totalScore / sqrt($numPlayers) : 0;
                return [
                    'department' => $dept,
                    'total_score' => $totalScore,
                    'average_accuracy' => $averageAccuracy,
                    'score_per_player' => $scorePerPlayer,
                    'num_players' => $numPlayers,
                ];
            })
            ->sortByDesc('score_per_player')
            ->values();
    });

    return view('leaderboard', [
        'users' => $paginatedUsers,
        'departments' => $departments,
    ]);
}

}
