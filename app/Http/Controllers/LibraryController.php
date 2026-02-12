<?php

namespace App\Http\Controllers;

use App\Models\ExamPaper;
use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\User;
use App\Models\UserLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function show_library()
    {
        if (!Auth::check())  return redirect()->route("home");

        $user = Auth::user();
        /** @var \App\Models\User $user */
        $all_library = $user->libraryQuizzes()
            ->with('user')
            ->withCount('questions')
            ->get();


        // Benim oluşturduklarım
        $myQuizzos = $all_library->filter(function ($quiz) use ($user) {
            return $quiz->user_id === $user->id;
        });

        // Başkalarının oluşturup kütüphaneme eklediklerim
        $libraryQuizzos = $all_library->filter(function ($quiz) use ($user) {
            return $quiz->user_id !== $user->id;
        });

        // exams
        $examPapers = ExamPaper::with('categories')
                    ->where('user_id', $user->id)
                    ->latest()
                    ->get();


        return view("pages.library", compact("myQuizzos", "libraryQuizzos", "examPapers"));
    }

    public function add_library(Quiz $quiz)
    {

        if (!Auth::check())  return redirect()->route("home")->with("error", "Önce giriş yapmalısınız.");

        $userId = Auth::id();

        $exists = UserLibrary::where("quiz_id", $quiz->id)
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
                UserLibrary::updateOrCreate(
                    [
                        "quiz_id" => $quiz->id,
                        "user_id" => $userId
                    ],
                    [
                        "is_completed" => $latestResult ? true : false,
                        "score" => $latestResult ? $latestResult->net : null,
                        "time_spent" => $latestResult ? $latestResult->time_spent : null
                    ]
                );
            }
            //? Kullanıcı Quizi hiç çözmeden öyle kütüphaneye ekliyor
            else {
                UserLibrary::create([
                    'quiz_id' => $quiz->id,
                    'user_id' => $userId,
                    'is_completed' => false,
                    'score' => null,
                    'time_spent' => null
                ]);
            }
        }

        return redirect()->route("library.show")->with('success', 'Quiz kütüphaneye eklendi.');
    }

    public function remove_library($id)
    {
        if (!Auth::check())  return redirect()->route("home")->with("error", "Önce giriş yapmalısınız.");

        $userId = Auth::id();

        $library_item = UserLibrary::find($id);

        if (!$library_item) {
            return redirect()->back()->with("error", "Kütüphane kaydı bulunamadı.");
        };

        if ($library_item->user_id !== $userId) {
            return redirect()->back()->with("error", "Bu işlemi yapmaya yetkiniz yok.");
        };

        $library_item->delete();

        return redirect()->route("library.show");
    }
}
