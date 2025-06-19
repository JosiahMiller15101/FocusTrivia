<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\QuestionSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class LeaderboardController extends Controller
{

public function index()
{
    // Calculate the current round (e.g., every 12 hours since a fixed start date)
    $startLocal = now('America/Denver')->setDate(2024, 1, 1)->startOfDay();
    $nowLocal = now('America/Denver');
    $daysPassed = $startLocal->diffInDays($nowLocal);
    $halfOfDay = (int) floor($nowLocal->hour / 12); // 0 before noon, 1 after
    $currentRound = (int) ($daysPassed * 2 + $halfOfDay);

    // Get all users (including Guests), calculate accuracy, and paginate manually
    $users = User::with('submissions')
        ->get();

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
            'display_score' => ($correct * 100) - ($wrong * 100),
            'submissions' => $user->submissions,
        ];
        return (object) $display;
    })->sortByDesc(function ($user) {
        return [$user->display_score, $user->display_total_answered];
    })->values();

    // Only update previous_rank at the start of a new round
    foreach ($sortedUsers as $i => $displayUser) {
        $currentRank = $i + 1;
        $displayUser->current_rank = $currentRank;
        $displayUser->rank_movement = null;
        $user = $users->firstWhere('id', $displayUser->id);
        $previousRank = isset($user->previous_rank) && $user->previous_rank !== null ? $user->previous_rank : (count($sortedUsers) + 1);
        if ($previousRank > $currentRank) {
            $displayUser->rank_movement = 'up';
        } elseif ($previousRank < $currentRank) {
            $displayUser->rank_movement = 'down';
        }
        // Only update previous_rank and last_rank_update_round if a new round has started
        if ($user->last_rank_update_round !== $currentRound) {
            $user->update([
                'previous_rank' => $currentRank,
                'last_rank_update_round' => $currentRound
            ]);
        }
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

    // Leaderboard for top departments by score per player (including 'guest')
    $departments = Cache::remember('top_departments', now()->addMinutes(1), function () use ($sortedUsers) {
        return $sortedUsers
            ->groupBy('department')
            ->map(function ($users, $dept) {
                $totalScore = $users->sum('display_score');
                $averageAccuracy = $users->avg('display_accuracy');
                $numPlayers = $users->count();
                $userIds = $users->pluck('id');
                $numSubs = QuestionSubmission::whereIn('user_id', $userIds)->count();
                $scorePerPlayer = $numPlayers > 0 && $numSubs > 0 ? $totalScore / sqrt($numSubs) : 0;
                return [
                    'department' => $dept,
                    'total_score' => $totalScore,
                    'average_accuracy' => $averageAccuracy,
                    'score_per_player' => $scorePerPlayer,
                    'num_players' => $numPlayers,
                    'num_submissions' => $numSubs,
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
