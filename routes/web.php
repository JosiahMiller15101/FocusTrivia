<?php

use App\Http\Controllers\QuestionSubmissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\LeaderboardController;


//home
Route::get('/', function () {
    return view('home');
});

//leaderboard
Route::get('/leaderboard', [LeaderboardController::class, 'index']);

//dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');

//register
Route::get('/register', [RegisteredUserController::class, 'create']);
Route::post('/register', [RegisteredUserController::class, 'store']);

//login
Route::get('/login', [SessionController::class, 'create'])->name('login');
Route::post('/login', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy']);

//question 
Route::get('/question', [QuestionController::class, 'show']);

//middelware
Route::middleware('auth')->get('/question', [QuestionController::class, 'showAuthenticated']);

//answer submission 
Route::middleware('auth')->post('/submit-answer', [QuestionSubmissionController::class, 'store']);




