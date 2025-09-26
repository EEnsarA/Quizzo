<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $user = $request->validate([
            "email" => "required|email",
            "password" => "required|min:6"
        ]);
        if (Auth::attempt($user)) {
            $request->session()->regenerate();
            return redirect()->intended("/");
        };
        return back()->withErrors([
            "email" => "invalid email",
            "password" => "password must be least 6 char"
        ], 'auth_form');
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                "name" => "required|string|max:30",
                "email" => "required|email|unique:users,email",
                "password" => "required|min:6|confirmed",
            ]);
        } catch (ValidationException $e) {

            return back()->withErrors($e->errors(), 'auth_form');
        }

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->intended("/");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("home");  // "/"->name('home')
    }

    public function dashboard()
    {
        if (Auth::check()) {
            $user = Auth::user();
            dd($user->name, $user->email);
        } else {
            return redirect()->route('home');
        }
    }
}
