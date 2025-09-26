<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\UserLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {

        //* pluck() => get() gibi ancak parametre olarak belirtilen sütunu döner
        //* whereNotIn() => koşula uygunları çıkarır where tersi ikinci parametresi dizi bekler ! 
        if (Auth::check()) {
            $userId = Auth::id();

            // library den userın librarye eklediği quizlerin id lerini alıyorum
            $user_quiz_ids = UserLibrary::where("user_id", $userId)->pluck("quiz_id");
            // quizlerden userın librarysinde bulunanları çıkartıyorum
            $quizzes = Quiz::with(["user", "results"])
                ->whereNotIn("id", $user_quiz_ids)
                ->latest()
                ->get();
        } else {
            $quizzes = Quiz::with(["user", "results"])->latest()->get();
        }

        return view("pages.home", compact("quizzes"));
    }
}
