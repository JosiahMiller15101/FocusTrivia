<?php

use App\Http\Controllers\QuestionSubmissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SelectDashboardController;
use App\Http\Controllers\SelectDepartmentDashboardController;
use App\Http\Controllers\QuestionCommentController;
use Carbon\Carbon;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;


//home
Route::get('/', [HomeController::class, 'index']);

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
Route::get('/question', [QuestionController::class, 'showAuthenticated'])->middleware('auth')->name('question.show');

//middelware
Route::middleware('auth')->get('/question', [QuestionController::class, 'showAuthenticated']);

//answer submission 
Route::middleware('auth')->post('/submit-answer', [QuestionSubmissionController::class, 'store']);

//Google Socialite
Route::get('/login/google/callback', [SocialiteController::class, 'handleGoogleCallback']);
Route::get('/login/google', [SocialiteController::class, 'redirectToGoogle']);

//Youtube Socialite
Route::get('/login/youtube', [SocialiteController::class, 'redirectToYouTube']);
Route::get('/login/youtube/callback', [SocialiteController::class, 'handleYouTubeCallback']);

//Edit Profile
Route::put('/profile', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');

// Player public dashboard route
Route::get('/player/{user}', [SelectDashboardController::class, 'show'])
    ->middleware('auth')
    ->name('player.dashboard');

// Department dashboard (public)
Route::get('/department/{department}', [SelectDepartmentDashboardController::class, 'show'])->name('department.dashboard');

//Explained
Route::get('/explained', function () {
    return view('explained');
});

// Post question comments
Route::post('/question/comment', [QuestionCommentController::class, 'store'])->middleware('auth')->name('question.comment');

// AJAX: React to a comment
Route::post('/question/comment/react', [QuestionCommentController::class, 'react'])->middleware('auth')->name('question.comment.react');

// Delete question comment
Route::delete('/question/comment/{id}', [QuestionCommentController::class, 'destroy'])->middleware('auth')->name('question.comment.delete');

// Profile image upload
Route::post('/profile/upload-image', [ProfileController::class, 'uploadImage'])->name('profile.uploadImage');

// Add route for question answer submission (original style)
Route::post('/question/submit', [QuestionSubmissionController::class, 'store']);

 Route::get('/env-check', function () {
    dd(app()->environment());
});

//Notification bell
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');


Route::get('/check-time', function () {
    return [
        'app_timezone' => config('app.timezone'),
        'php_timezone' => date_default_timezone_get(),
        'now' => Carbon::now()->toDateTimeString(),
    ];
});

//404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
