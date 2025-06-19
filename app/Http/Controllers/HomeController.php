<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\QuestionSubmission;

class HomeController extends Controller
{
    public function index()
    {
        $nowLocal = Carbon::now('America/Denver');

        $nextQuestionTime = $nowLocal->hour < 12
            ? Carbon::today($nowLocal->timezone)->addHours(12)
            : Carbon::tomorrow($nowLocal->timezone)->addHours(12);

        $totalUsers = User::count();
        $totalSubmissions = QuestionSubmission::count();
        $uniqueDepartments = User::whereNotNull('department')->distinct('department')->count('department');

        // Calculate overall accuracy average of all users
$users = User::with('submissions')->get();

$totalCorrect = 0;
$totalSubmissions = 0;

foreach ($users as $user) {
    $total = $user->submissions->count();
    $correct = $user->submissions->where('is_correct', true)->count();
    $totalCorrect += $correct;
    $totalSubmissions += $total;
}

$overallAccuracy = $totalSubmissions > 0 ? round(($totalCorrect / $totalSubmissions) * 100, 1) : 0;

        return view('home', compact('nowLocal', 'nextQuestionTime', 'totalUsers', 'totalSubmissions', 'uniqueDepartments', 'overallAccuracy'));
    }
}
