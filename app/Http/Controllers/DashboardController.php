<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionSubmission;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = $this->getUserStats($user->id);
        $allUsers = $this->getRankedUsers();
        $playerRank = $this->getPlayerRank($allUsers, $user->id);
        $departments = $this->getRankedDepartments($allUsers);
        $departmentRank = $this->getDepartmentRank($departments, $user->department);
        $history = $this->getRecentSubmissions($user->id);
        $streak = $this->calculateStreak($user->id);

        return view('dashboard', [
            'first_name' => $user->first_name,
            'totalAnswered' => $stats['totalAnswered'],
            'correctAnswers' => $stats['correctAnswers'],
            'score' => $stats['score'],
            'playerRank' => $playerRank,
            'departmentRank' => $departmentRank,
            'history' => $history,
            'streak' => $streak,
        ]);
    }

    private function getUserStats($userId)
    {
        $totalAnswered = QuestionSubmission::where('user_id', $userId)->count();
        $correctAnswers = QuestionSubmission::where('user_id', $userId)->where('is_correct', true)->count();
        $wrongAnswers = $totalAnswered - $correctAnswers;
        $score = ($correctAnswers * 10) - ($wrongAnswers * 10);

        return [
            'totalAnswered' => $totalAnswered,
            'correctAnswers' => $correctAnswers,
            'score' => $score,
        ];
    }

    private function getRankedUsers()
    {
        return User::with('submissions')
            ->get()
            ->filter(fn($u) => strtolower(trim($u->department)) !== 'guest')
            ->map(function ($u) {
                $correct = $u->submissions->where('is_correct', true)->count();
                $total = $u->submissions->count();
                $wrong = $total - $correct;

                $u->accuracy = $total > 0 ? round($correct / $total * 100, 1) : 0;
                $u->total_answered = $total;
                $u->score = ($correct * 10) - ($wrong * 10);

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
            ->take(10)
            ->get();
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
