<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionSubmission;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;


class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = $this->getUserStats($user->id);
        $allUsers = $this->getRankedUsers(false); // Pass false to include guests
        $playerRank = $this->getPlayerRank($allUsers, $user->id);
        $departments = $this->getRankedDepartments($allUsers);
        $departmentRank = $this->getDepartmentRank($departments, $user->department);
        $history = $this->getRecentSubmissions($user->id);
        $streak = $this->calculateStreak($user->id);

        
        $perPage = 3;
        $page = request('page', 1);
        $recentActivity = $this->getRecentSubmissions($user->id);

        // Build department player table and rank like SelectDepartmentDashboardController
        $departmentUsers = User::with('submissions')
            ->whereRaw('LOWER(TRIM(department)) = ?', [strtolower(trim($user->department))])
            ->whereRaw('LOWER(TRIM(department)) != ?', ['guest'])
            ->get();
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
        // Debug: Uncomment the next line to inspect the player list and user ID
        // dd($departmentPlayers, $user->id);
        $departmentPlayerRank = $departmentPlayers->search(function ($p) use ($user) {
            return (int)$p['id'] === (int)$user->id;
        });
        $departmentPlayerRank = $departmentPlayerRank !== false ? $departmentPlayerRank + 1 : 'N/A';

        return view('dashboard', [
            'first_name' => $user->first_name,
            'totalAnswered' => $stats['totalAnswered'],
            'correctAnswers' => $stats['correctAnswers'],
            'score' => $stats['score'],
            'playerRank' => $playerRank,
            'departmentRank' => $departmentRank,
            'departmentPlayerRank' => $departmentPlayerRank,
            'departmentPlayers' => $departmentPlayers,
            'history' => $recentActivity,
            'streak' => $streak,
        ]);
    }

    private function getUserStats($userId)
    {
        $totalAnswered = QuestionSubmission::where('user_id', $userId)->count();
        $correctAnswers = QuestionSubmission::where('user_id', $userId)->where('is_correct', true)->count();
        $wrongAnswers = $totalAnswered - $correctAnswers;
        $score = ($correctAnswers * 100) - ($wrongAnswers * 100);

        return [
            'totalAnswered' => $totalAnswered,
            'correctAnswers' => $correctAnswers,
            'score' => $score,
        ];
    }

    private function getRankedUsers($excludeGuests = true)
    {
        return User::with('submissions')
            ->get()
            ->filter(function($u) use ($excludeGuests) {
                return $excludeGuests ? strtolower(trim($u->department)) !== 'guest' : true;
            })
            ->map(function ($u) {
                $correct = $u->submissions->where('is_correct', true)->count();
                $total = $u->submissions->count();
                $wrong = $total - $correct;

                $u->accuracy = $total > 0 ? round($correct / $total * 100, 1) : 0;
                $u->total_answered = $total;
                $u->score = ($correct * 100) - ($wrong * 100);

                return $u;
            })
            ->sortByDesc(fn($u) => [$u->score, $u->total_answered])
            ->values();
    }

    private function getPlayerRank($allUsers, $userId)
    {
        $rank = $allUsers->search(fn($u) => $u->id === $userId);
        return $rank !== false ? $rank + 1 : 'N/A';
    }

    private function getRankedDepartments($users)
    {
        return $users->groupBy('department')
            ->filter(fn($users, $dept) => strtolower(trim($dept)) !== 'guest')
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
    }

    private function getDepartmentRank($departments, $userDept)
    {
        $rank = $departments->search(fn($d) => $d['department'] === $userDept);
        return $rank !== false ? $rank + 1 : 'N/A';
    }

    private function getRecentSubmissions($userId)
    {
        return QuestionSubmission::with('question')
            ->where('user_id', $userId)
            ->orderByDesc('submitted_at')
            ->simplePaginate(4);
    }

    public function calculateStreak($userId)
    {
        $dates = QuestionSubmission::where('user_id', $userId)
            ->orderByDesc('submitted_at')
            ->pluck('submitted_at')
            ->map(fn($dt) => Carbon::parse($dt)->toDateString())
            ->unique()
            ->values();

        $streak = 0;
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $expected = $today;

        foreach ($dates as $date) {
            if ($date === $expected) {
                $streak++;
                $expected = Carbon::parse($expected)->subDay()->toDateString();
            } else {
                if ($streak === 0 && $date === $yesterday) {
                    $streak++;
                    $expected = Carbon::parse($expected)->subDay()->toDateString();
                    continue;
                }
                break;
            }
        }

        return $streak;
    }
}
