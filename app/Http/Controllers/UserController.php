<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function profile()
    {
        if (!Auth::check())  return redirect()->route("home");

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $myQuizzos = $user->quizzes()
            ->withCount('results')
            ->latest()
            ->paginate(12);

        $stats = [
            'created_count' => $user->quizzes()->count(),      // Toplam oluşturduğu quiz sayısı
            'solved_count'  => $user->results()->distinct('quiz_id')->count(), // Çözdüğü *farklı* quiz sayısı
            'rank'          => $user->rank ?? '#24'           // Varsa rank, yoksa varsayılan
        ];

        return view("pages.user_profile", compact("myQuizzos", "user"));
    }


    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar_img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->hasFile('avatar_img')) {


            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $filename = uniqid() . "-" . $request->file('avatar_img')->getClientOriginalName();

            $path = $request->file('avatar_img')->storeAs('uploads/avatars', $filename, 'public');

            $user->avatar_url = $path;
            $user->save();

            return response()->json(['message' => 'Avatar güncellendi', 'path' => $path], 200);
        }

        return response()->json(['message' => 'Dosya yüklenemedi'], 400);
    }
}
