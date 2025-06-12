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
    ]);

    $user = auth()->user();
    $user->update($request->only('first_name', 'last_name', 'email', 'department'));

    return back()->with('success', 'Profile updated successfully.');
}
}
