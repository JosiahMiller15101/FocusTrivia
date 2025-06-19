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
            ];
        })->sortByDesc(function ($p) {
            return [$p['score'], $p['total_answered']];
        })->values();

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
        ]);
    }
}
