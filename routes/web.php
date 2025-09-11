<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('pages/home');
// });

Route::get("/", [HomeController::class, "index"])->name("home");

Route::get("/create_quiz", [QuizController::class, "create_quiz"])->name("quiz.create");

Route::get("/quiz/{quiz}", [QuizController::class, "show_quiz"])->name("quiz.show");

Route::get("/quiz/{quiz}/start", [QuizController::class, "start_quiz"])->name("quiz.start");

Route::post("/quiz/{quiz}/check", [QuizController::class, "check_quiz"])->name("quiz.check");

Route::get("/quiz/result/{result}", [QuizController::class, "show_result"])->name("quiz.result");

Route::post("/login", [UserController::class, "login"])->name("login");

Route::post("/register", [UserController::class, "register"])->name("register");

Route::post("/logout", [UserController::class, "logout"])->name("logout");

Route::get("/dashboard", [UserController::class, "dashboard"])->middleware("auth")->name("dashboard");
