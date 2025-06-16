<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
{
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:users,email,' . auth()->id(),
        'department' => 'required|string|max:255',
        'profile_image' => 'nullable|image|max:2048', // 2MB max
    ]);

        $user = auth()->user();

        if ($request->hasFile('profile_image')) {
        $path = $request->file('profile_image')->store('profile_images', 'public');
        $user->profile_image = $path;
    }
    $user->save();

    $user->update($request->only('first_name', 'last_name', 'email', 'department'));

    return back()->with('success', 'Profile updated successfully.');
}
}
