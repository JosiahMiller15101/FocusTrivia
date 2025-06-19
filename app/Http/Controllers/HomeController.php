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

        return view('home', compact('nowLocal', 'nextQuestionTime', 'totalUsers', 'totalSubmissions', 'uniqueDepartments'));
    }
}
