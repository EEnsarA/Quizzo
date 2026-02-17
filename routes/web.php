<?php

use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get("/", [HomeController::class, "index"])->name("home");

// --- Auth İşlemleri ---
Route::post("/login", [UserController::class, "login"])->name("login");
Route::post("/register", [UserController::class, "register"])->name("register");
Route::post("/logout", [UserController::class, "logout"])->name("logout");

// --- Quiz ---
// Misafirler buraya girebilir ve elle soru ekleyebilir.
Route::get("/create_quiz", [QuizController::class, "create_quiz"])->name("quiz.create");
Route::post("/create_quiz/add", [QuizController::class, "add_quiz"])->name("quiz.add"); // Elle ekleme postu
Route::post("/create_questions/add", [QuizController::class, "add_questions"])->name("questions.add");
Route::get("/create_questions/{quiz}", [QuizController::class, "create_questions"])->name("quiz.add.questions");
Route::get("/edit_quiz",[QuizController::class,"edit_quiz"])->name("quiz.edit");

// --- Quiz Çözme İşlemleri  ---
Route::get("/quiz/{quiz}", [QuizController::class, "show_quiz"])->name("quiz.show");
Route::get("/quiz/{quiz}/start", [QuizController::class, "start_quiz"])->name("quiz.start");
Route::post("/quiz/{quiz}/check/{quiz_result}", [QuizController::class, "check_quiz"])->name("quiz.check");
Route::get("/quiz/result/{result}", [QuizController::class, "show_result"])->name("quiz.result");

Route::get("/exam_create", [ExamController::class, "index"])->name("exam.create");
Route::get("exam_creator/test", [ExamController::class, "test"])->name("test");
Route::get('/exam/edit/{id}', [ExamController::class, 'edit'])->name('exam.edit');
Route::post('/exam/update/{id}', [ExamController::class, 'update'])->name('exam.update');
Route::delete('/exam/delete/{id}', [ExamController::class, 'destroy'])->name('exam.delete');
Route::post('/exam/upload-image', [ExamController::class, 'uploadImage'])->name('exam.upload.image');
Route::get('/exam/{id}/download', [ExamController::class, 'downloadPDF'])->name('exam.download');
Route::get('/exam/{id}/preview', [ExamController::class, 'previewPDF'])->name('exam.preview');


Route::middleware(['auth'])->group(function () {

    // --- Kullanıcı Paneli ---
    Route::get("/dashboard", [UserController::class, "dashboard"])->name("dashboard");
    Route::get("/profile", [UserController::class, "profile"])->name("profile");
    Route::post("/profile/update-avatar", [UserController::class, "updateAvatar"])->name("profile.avatar");

    // --- EXAM CREATOR ---
    Route::post('/exam/save', [ExamController::class, 'store'])->name('exam.save');
    Route::post('/exam/ai-batch-generate', [ExamController::class, 'generate_batch_questions'])->name('exam.ai_batch');

    Route::post("/create_quiz/ai-generate", [QuizController::class, "ai_generate"])->name("quiz.ai_generate");
    Route::post('/exam/{id}/toggle-public', [ExamController::class, 'togglePublic'])->name('exam.toggle-public');
    // --- Kütüphane & Silme İşlemleri ---
    Route::delete("/quiz/{quiz}/delete", [QuizController::class, "delete_quiz"])->name("quiz.delete");
    Route::get("/library", [LibraryController::class, "show_library"])->name("library.show");
    Route::post("/library/add/{quiz}", [LibraryController::class, "add_library"])->name("library.add");
    Route::delete("/library/remove/{id}", [LibraryController::class, "remove_library"])->name("library.remove");
});
