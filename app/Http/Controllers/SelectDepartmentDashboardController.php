<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\QuestionSubmission;

class SelectDepartmentDashboardController extends Controller
{
    public function show($department)
    {
        // Get all users in the department (excluding guests)
        $users = User::with('submissions')
            ->where('department', $department)
            ->whereRaw('LOWER(TRIM(department)) != ?', ['guest'])
            ->get();

        if ($users->isEmpty()) {
            abort(404, 'Department not found');
        }

        // Department stats
        $totalScore = $users->sum(function ($u) {
            $correct = $u->submissions->where('is_correct', true)->count();
            $wrong = $u->submissions->count() - $correct;
            return ($correct * 100) - ($wrong * 100);
        });
        $numPlayers = $users->count();
        $totalQuestionsAnswered = $users->sum(function ($u) {
            return $u->submissions->count();
        });
        $totalCorrectAnswers = $users->sum(function ($u) {
            return $u->submissions->where('is_correct', true)->count();
        });
        $userIds = $users->pluck('id');
        $numSubs = QuestionSubmission::whereIn('user_id', $userIds)->count();
        $scorePerPlayer = ($numPlayers > 0 && $numSubs > 0) ? $totalScore / sqrt($numSubs) : 0;
        $averageAccuracy = $users->avg(function ($u) {
            $total = $u->submissions->count();
            $correct = $u->submissions->where('is_correct', true)->count();
            return $total > 0 ? ($correct / $total) * 100 : 0;
        });

        // Player list with stats
        $players = $users->map(function ($u) {
            $total = $u->submissions->count();
            $correct = $u->submissions->where('is_correct', true)->count();
            $wrong = $total - $correct;
            $score = ($correct * 100) - ($wrong * 100);
            $accuracy = $total > 0 ? round(($correct / $total) * 100, 1) : 0;
            return [
                'id' => $u->id,
                'name' => $u->first_name . ' ' . $u->last_name,
                'score' => $score,
                'accuracy' => $accuracy,
                'total_answered' => $total,
                'profile_image' => $u->profile_image,
                'isCurrentUser' => ($u->id == auth()->id()),
            ];
        })->sortByDesc(function ($p) {
            return [$p['score'], $p['total_answered']];
        })->values();

        // Calculate department rank among all departments by scorePerPlayer
        $allDepartments = User::with('submissions')
            ->whereRaw('LOWER(TRIM(department)) != ?', ['guest'])
            ->get()
            ->groupBy(function($u) { return strtolower(trim($u->department)); });
        $departmentScores = $allDepartments->map(function($users, $dept) {
            $correct = $users->sum(fn($u) => $u->submissions->where('is_correct', true)->count());
            $wrong = $users->sum(fn($u) => $u->submissions->count() - $u->submissions->where('is_correct', true)->count());
            $totalScore = ($correct * 100) - ($wrong * 100);
            $numPlayers = $users->count();
            $numSubs = $users->sum(fn($u) => $u->submissions->count());
            $scorePerPlayer = ($numPlayers > 0 && $numSubs > 0) ? $totalScore / sqrt($numSubs) : 0;
            return [
                'department' => $dept,
                'score_per_player' => $scorePerPlayer,
            ];
        })->sortByDesc('score_per_player')->values();
        $departmentRank = $departmentScores->search(function($d) use ($department) {
            return strtolower(trim($d['department'])) === strtolower(trim($department));
        });
        $departmentRank = $departmentRank !== false ? $departmentRank + 1 : 'N/A';

        // Created At logic
        $createdAt = null;
        $deptKey = strtolower(trim($department));
        switch ($deptKey) {
            case 'marketing':
            case 'it':
            case 'events':
            case 'donor communications':
            case 'hr':
            case 'accounting':
                $createdAt = 'June 16, 2025'; break;
            case 'summer projects':
                $createdAt = 'June 18, 2025'; break;
            case 'media operations':
                $createdAt = 'June 17, 2025'; break;
            case 'other':
                $createdAt = 'June 23, 2025'; break;
            default:
                $createdAt = 'N/A';
        }

        return view('department_dashboard', [
            'department' => $department,
            'scorePerPlayer' => $scorePerPlayer,
            'totalScore' => $totalScore,
            'averageAccuracy' => $averageAccuracy,
            'players' => $players,
            'numPlayers' => $numPlayers,
            'totalQuestionsAnswered' => $totalQuestionsAnswered,
            'totalCorrectAnswers' => $totalCorrectAnswers,
            'numSubmissions' => $numSubs,
            'departmentRank' => $departmentRank,
            'createdAt' => $createdAt,
        ]);
    }
}
