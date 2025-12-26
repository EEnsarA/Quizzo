<?php

use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ====================================================
// 1. MİSAFİR ROTALARI (Giriş Yapmadan Erişilebilir)
// ====================================================

Route::get("/", [HomeController::class, "index"])->name("home");

// --- Auth İşlemleri ---
Route::post("/login", [UserController::class, "login"])->name("login");
Route::post("/register", [UserController::class, "register"])->name("register");
Route::post("/logout", [UserController::class, "logout"])->name("logout");

// --- Quiz (MANUEL OLUŞTURMA & ÇÖZME - Herkese Açık) ---
// Misafirler buraya girebilir ve elle soru ekleyebilir.
Route::get("/create_quiz", [QuizController::class, "create_quiz"])->name("quiz.create");
Route::post("/create_quiz/add", [QuizController::class, "add_quiz"])->name("quiz.add"); // Elle ekleme postu
Route::post("/create_questions/add", [QuizController::class, "add_questions"])->name("questions.add");
Route::get("/create_questions/{quiz}", [QuizController::class, "create_questions"])->name("quiz.add.questions");

// --- Quiz Çözme İşlemleri (Herkese Açık) ---
Route::get("/quiz/{quiz}", [QuizController::class, "show_quiz"])->name("quiz.show");
Route::get("/quiz/{quiz}/start", [QuizController::class, "start_quiz"])->name("quiz.start");
Route::post("/quiz/{quiz}/check/{quiz_result}", [QuizController::class, "check_quiz"])->name("quiz.check");
Route::get("/quiz/result/{result}", [QuizController::class, "show_result"])->name("quiz.result");

Route::get("/exam_create", [ExamController::class, "index"])->name("exam.create");
Route::get("exam_creator/test", [ExamController::class, "test"])->name("test");

// ====================================================
// 2. KORUMALI ROTALAR (Sadece Giriş Yapmış Üyeler)
// ====================================================
// Buraya girmeye çalışan misafir, otomatik olarak LOGIN sayfasına atılır.

Route::middleware(['auth'])->group(function () {

    // --- Kullanıcı Paneli ---
    Route::get("/dashboard", [UserController::class, "dashboard"])->name("dashboard");
    Route::get("/profile", [UserController::class, "profile"])->name("profile");
    Route::post("/profile/update-avatar", [UserController::class, "updateAvatar"])->name("profile.avatar");

    // --- EXAM CREATOR (Bizim Proje - Sadece Üyeler) ---
    Route::post('/exam/save', [ExamController::class, 'store'])->name('exam.save');


    // --- QUIZ AI ÖZELLİĞİ (Sadece Üyeler) ---
    // Manuel oluşturma yukarıda açık, ama AI butonu bu rotaya gidiyor.
    // Giriş yapmamış biri AI butonuna basarsa login'e gider.
    Route::post("/create_quiz/ai-generate", [QuizController::class, "ai_generate"])->name("quiz.ai_generate");

    // --- Kütüphane & Silme İşlemleri ---
    // Başkasının quizini silmemesi veya kütüphaneye eklemesi için üyelik şart.
    Route::delete("/quiz/{quiz}/delete", [QuizController::class, "delete_quiz"])->name("quiz.delete");
    Route::get("/library", [LibraryController::class, "show_library"])->name("library.show");
    Route::post("/library/add/{quiz}", [LibraryController::class, "add_library"])->name("library.add");
    Route::delete("/library/remove/{id}", [LibraryController::class, "remove_library"])->name("library.remove");
});
