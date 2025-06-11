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
                    'name' => $googleUser->getName(),
                    'password' => bcrypt(str()->random(24)), // optional placeholder
                    'google_id' => $googleUser->getId(), // if you want to store it
                ]
            );

            Auth::login($user);

            return redirect('/'); // or wherever you want to go after login
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['oauth' => 'Google login failed']);
        }
    }
}

