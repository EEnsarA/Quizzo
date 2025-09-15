<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\QuizUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function show_library()
    {
        if (!Auth::check())  return redirect()->route("login");

        $userId = Auth::id();
        $user = Auth::user();


        $quizzes = $user->solvedQuizzes;
        return view("pages.library", compact("quizzes"));
    }

    public function add_library(Quiz $quiz)
    {

        if (!Auth::check())  return redirect()->route("home");

        $userId = Auth::id();

        $exists = QuizUser::where("quiz_id", $quiz->id)
            ->where("user_id", $userId)
            ->exists();

        if (!$exists) {

            $latestResult = QuizResult::where("quiz_id", $quiz->id)
                ->where("user_id", $userId)
                ->orderByDesc("created_at")
                ->first();
            //^^ Tarih olarak en son çözdüğü quiz sonucunu alıyoruz
            //? Kullanıcı Quizi çözüp öyle kütüphaneye ekliyor (score ve is_completed bilgisi eklenmeli)
            if ($latestResult) {
                QuizUser::updateOrCreate(
                    [
                        "quiz_id" => $quiz->id,
                        "user_id" => $userId
                    ],
                    [
                        "is_completed" => $latestResult ? true : false,
                        "score" => $latestResult ? $latestResult->net : null,
                    ]
                );
            }
            //? Kullanıcı Quizi hiç çözmeden öyle kütüphaneye ekliyor
            else {
                QuizUser::create([
                    'quiz_id' => $quiz->id,
                    'user_id' => $userId,
                    'is_completed' => false,
                    'score' => null,
                ]);
            }
        }

        return back()->with('success', 'Quiz kütüphaneye eklendi.');
    }

    public function remove_library() {}
}
