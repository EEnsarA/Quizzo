<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with('user')->latest()->get();
        return view("pages.home", compact("quizzes"));
    }
}
