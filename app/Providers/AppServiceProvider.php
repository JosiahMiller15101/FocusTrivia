<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    //    if (app()->environment('production')) {
    //     \URL::forceScheme('https');
    //     }

    // Only inject notification dropdown data into the layout component
    View::composer('components.layout', function ($view) {
        if (Auth::check()) {
            $userId = Auth::id();

            // Pass unread count
            $unreadCount = Notification::where('user_id', $userId)
                ->where('read', false)
                ->count();

            // Pass recent notifications (limit to 10 or so for dropdown)
            $headerNotifications = Notification::with('comment.question')
                ->where('user_id', $userId)
                ->latest()
                ->take(10)
                ->get();

            $view->with(compact('unreadCount', 'headerNotifications'));
        }
    });
    }
}
