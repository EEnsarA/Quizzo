<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function create_quiz()
    {
        return view("pages.create");
    }

    public function show_quiz(Quiz $quiz)
    {
        $filters = request()->input("filters", []);

        $rankings = $quiz->rankings($filters);


        if (Auth::check()) {
            $current_user_id = Auth::id();
        } else {
            $current_user_id = session()->getId();
        }

        return view("pages.quiz", compact("quiz", "rankings", "current_user_id"));
    }

    public function start_quiz(Quiz $quiz)
    {

        $user = Auth::user();
        $sessionId = session()->getId();
        $is_new_attempt = false;
        // zaten önceden oluşturulmuşsa 
        $quiz_result = QuizResult::where("quiz_id", $quiz->id)
            ->when(Auth::check(), fn($q) => $q->where('user_id', $user->id))
            ->when(!Auth::check(), fn($q) => $q->where('session_id', $sessionId))
            ->where('time_spent', 0)
            ->first();


        //?  time_spenti doğru hesaplamak için started_at'i quiz başlatıldığı an doldurup kaydediyorum 

        // yeni oluşturuluyor ise
        if (!$quiz_result) {
            $is_new_attempt = true;
            $attemptNumber = QuizResult::where('quiz_id', $quiz->id)
                ->when(Auth::check(), fn($q) => $q->where('user_id', $user->id))
                ->when(!Auth::check(), fn($q) => $q->where('session_id', $sessionId))
                ->count() + 1;


            $quiz_result = QuizResult::create([
                "quiz_id"        => $quiz->id,
                'user_id'        => Auth::id(), // guest ise null
                'session_id'     => Auth::check() ? null : $sessionId,
                "attempt_number" => $attemptNumber,
                "started_at"     => now()
            ]);
        }

        $quiz->load("questions.answers");
        return view("pages.quiz_start", compact("quiz", "quiz_result", "is_new_attempt"));
    }

    public function check_quiz(Request $request, Quiz $quiz, QuizResult $quiz_result)
    {
        /* 
        ? laravel otomatik id ye göre quizi çağırdığı için $quiz objesi parametre olarak alıyoruz: http://127.0.0.1:8000/quiz/5/check -> $quiz = Quiz::findOrFail(5) 
        * {soruId:seçilenCevapId} ["1" => 2, "2" => 5]
        * soruId ye göre girilen cevabı alıyorum eğer yoksa boş bırakılmıştır !.
        * doğru cevabı sorunun şıklarına gidip fkey'i bizim question Idle aynı olan answerslardan is_correct kolonu true olanı first ile ilk bulduğun anda dönüyoruz !.
        */

        if ($quiz_result->quiz_id !== $quiz->id) {
            return response()->json(["error" => "Invalid quiz result"], 403);
        }


        $correct = 0;
        $wrong = 0;
        $empty = 0;

        $answers = $request->all();
        $results = [];
        foreach ($quiz->questions as $question) {
            $givenAnswerId = $answers[$question->id] ?? null;
            $correctAnswer = $question->answers()->where("is_correct", true)->first();
            $results[] = [
                "question_id" => $question->id,
                "given_answer" => $givenAnswerId ?? null,
                "correct_answer" => $correctAnswer?->id,
                "is_correct" => $givenAnswerId ?  $givenAnswerId == $correctAnswer?->id : null,
            ];

            if (!$givenAnswerId) $empty++;
            elseif ($givenAnswerId == $correctAnswer->id) $correct++;
            else $wrong++;
        }
        $net = $correct - ($wrong / ($quiz->wrong_to_correct_ratio));

        //? artık now - started_at ile gerçek zaman farkını doğru bir şekilde bulabiliriz 
        $timespent = now()->diffInSeconds($quiz_result->started_at);
        $timespent = (int) $timespent;
        $timespent = abs($timespent);


        $quiz_result->update([
            'details'        => $results,
            'correct_count'  => $correct,
            'wrong_count'    => $wrong,
            'empty_count'    => $empty,
            'net'            => $net,
            'time_spent'     => $timespent,
        ]);


        // $quizResult = QuizResult::create([
        //     'quiz_id'        => $quiz->id,
        //     'user_id'        => Auth::id(), // guest ise null
        //     'session_id'     => Auth::check() ? null : session()->getId(),
        //     'details'        => $results,
        //     'correct_count'  => $correct,
        //     'wrong_count'    => $wrong,
        //     'empty_count'    => $empty,
        //     'net'            => $net,
        //     'time_spent'     => $timespent,
        //     'attempt_number' => $attemptNumber,
        // ]);

        return response()->json([
            "redirect" => route("quiz.result", $quiz_result->id)
        ]);
    }

    public function show_result(QuizResult $result)
    {
        $result->load('quiz');

        $filters = request()->input("filters", []);
        $rankings =  $result->quiz->rankings($filters);

        return view('pages.quiz_result', compact("result", "rankings"));
    }
}
