<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('pages/home');
// });

Route::get("/", [HomeController::class, "index"])->name("home");

Route::get("/create_quiz", [QuizController::class, "create_quiz"])->name("quiz.create");

Route::post("/create_quiz/add", [QuizController::class, "add_quiz"])->name("quiz.add");

Route::post("/create_quiz/ai-generate", [QuizController::class, "ai_generate"])->name("quiz.ai_generate");

Route::post("/create_questions/add", [QuizController::class, "add_questions"])->name("questions.add");

Route::get("/create_questions/{quiz}", [QuizController::class, "create_questions"])->name("quiz.add.questions");

Route::get("/quiz/{quiz}", [QuizController::class, "show_quiz"])->name("quiz.show");

Route::get("/quiz/{quiz}/start", [QuizController::class, "start_quiz"])->name("quiz.start");

Route::post("/quiz/{quiz}/check/{quiz_result}", [QuizController::class, "check_quiz"])->name("quiz.check");

Route::get("/quiz/result/{result}", [QuizController::class, "show_result"])->name("quiz.result");

Route::delete("/quiz/{quiz}/delete", [QuizController::class, "delete_quiz"])->name("quiz.delete");

Route::get("/library", [LibraryController::class, "show_library"])->name("library.show");

Route::post("/library/add/{quiz}", [LibraryController::class, "add_library"])->name("library.add");

Route::delete("/library/remove/{id}", [LibraryController::class, "remove_library"])->name("library.remove");

Route::post("/login", [UserController::class, "login"])->name("login");

Route::post("/register", [UserController::class, "register"])->name("register");

Route::post("/logout", [UserController::class, "logout"])->name("logout");

Route::get("/dashboard", [UserController::class, "dashboard"])->middleware("auth")->name("dashboard");
