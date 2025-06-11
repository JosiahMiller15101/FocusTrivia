<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'first_name' => $googleUser->user['given_name'] ?? $googleUser->getName(),
                    'last_name' => $googleUser->user['family_name'] ?? '',
                    'department' => 'Other', // default
                    //'email' => $googleUser->getName(),
                    'password' => bcrypt(str()->random(24)), // optional placeholder
                    //'google_id' => $googleUser->getId(), // store it
                ]
            );

            Auth::login($user);

            return redirect('/'); // Redirect to home after login
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['oauth' => 'Google login failed']);
        }
    }

    public function redirectToYouTube()
{
    return Socialite::driver('google')
        ->scopes([
            'https://www.googleapis.com/auth/youtube.readonly',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ])
        ->redirect();
}

public function handleYouTubeCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
            'first_name' => $googleUser->user['given_name'] ?? $googleUser->getName(),
            'last_name' => $googleUser->user['family_name'] ?? '',
            'department' => 'Other', // default
            //'email' => $googleUser->getEmail(),
            'password' => bcrypt(str()->random(24)), // secure random password placeholder
            ]
        );

        Auth::login($user);

        return redirect('/'); 
    } catch (\Exception $e) {
        return redirect('/login')->withErrors(['oauth' => 'YouTube login failed']);
    }
  }
}

