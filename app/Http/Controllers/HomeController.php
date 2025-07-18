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

    $totalUsers = cache()->remember('user_count', 60, fn() => User::count());
    $totalSubmissions = cache()->remember('submission_count', 60, fn() => QuestionSubmission::count());
    $uniqueDepartments = cache()->remember('unique_departments', 60, fn() =>
        User::whereNotNull('department')->distinct('department')->count('department')
    );

    // More efficient accuracy calculation
    $accuracyStats = QuestionSubmission::selectRaw('
        COUNT(*) as total,
        SUM(CASE WHEN is_correct IS TRUE THEN 1 ELSE 0 END) as correct
    ')->first();

    $overallAccuracy = $accuracyStats->total > 0
        ? round(($accuracyStats->correct / $accuracyStats->total) * 100, 1)
        : 0;

    return view('home', compact(
        'nowLocal',
        'nextQuestionTime',
        'totalUsers',
        'totalSubmissions',
        'uniqueDepartments',
        'overallAccuracy'
    ));
}
}
