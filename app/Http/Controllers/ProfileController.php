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
        $user->fill($request->only('first_name', 'last_name', 'email', 'department'));
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|max:2048',
        ]);

        $user = auth()->user();
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 's3');
            $url = \Storage::disk('s3')->url($path);
            $user->profile_image = $url; // Save the full URL for easy display
            $user->save();
            return back()->with('success', 'Profile image updated successfully.');
        }
        return back()->withErrors(['profile_image' => 'No image was uploaded.']);
    }
}

//+7r0gaKsfWKxYU+r2n5pqPRD8BVbFSUgIRXjsCgX
//key->AKIA2CGFJUHTSO3RWT6C