<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Models\QuizResult;
use App\Models\UserLibrary;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Prompts\Output\ConsoleOutput;

class QuizController extends Controller
{
    public function create_quiz()
    {
        return view("pages.create");
    }

    public function create_questions(Quiz $quiz)
    {


        return view("pages.create_question", compact("quiz"));
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

        $net = $correct;
        if ($quiz->wrong_to_correct_ratio > 0) {
            $net = $correct - ($wrong / ($quiz->wrong_to_correct_ratio));
        }

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

        // Kullanıcı için Çözüm olduğu an Librarye Ekleme ve UserLibrary tablosunu güncelleme
        if (Auth::check()) {
            $userId = Auth::id();

            UserLibrary::updateOrCreate(
                [
                    "quiz_id" => $quiz->id,
                    "user_id" => $userId
                ],
                [
                    "is_completed" => true,
                    "score" => $quiz_result->net,
                    "time_spent" => $quiz_result->time_spent,
                ]
            );
        }


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

    public function add_quiz(Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Oturum süreniz dolmuş.'], 401);
            }
            return redirect()->route("home");
        }


        $request->validate([
            "title" => "required|string|max:255",
            "img_url" => "nullable|image|mimes:jpg,jpeg,png|max:2048",
            "subject" => "required|string|max:255",
            "description" => "nullable|string|max:500",
            "number_of_questions" => "required|integer|min:4|max:20",
            "number_of_options" => "required|integer|min:2|max:6",
            "difficulty" => "required|string|in:easy,medium,hard,expert",
            "duration_minutes" => "required|integer|min:1|max:120",
            "wrong_to_correct_ratio" => "nullable|integer|min:0|max:10",
        ]);

        #img için store işlemleri
        $path = null;
        if ($request->hasFile("img_url")) {
            # benzersiz isim 
            $filename = uniqid() . "-" . $request->file("img_url")->getClientOriginalName();

            $path = $request->file("img_url")->storeAs("uploads/quizImages", $filename, "public");
        }


        $quiz = Quiz::create([
            "title" => $request->title,
            "subject" => $request->subject,
            "description" => $request->description ?? null,
            "img_url" => $path ?? null,
            "number_of_questions" => $request->number_of_questions,
            "number_of_options" => $request->number_of_options,
            "difficulty" => $request->difficulty,
            "duration_minutes" => $request->duration_minutes,
            "wrong_to_correct_ratio" => $request->wrong_to_correct_ratio ?? 0,
            "user_id" => Auth::id(),
        ]);


        #librarye ekleme
        $user = Auth::user();

        /** @var \App\Models\User $user */  #PHP Intelephense sorunu
        $user->libraryQuizzes()->attach($quiz->id);

        return response()->json([
            'success' => true,
            'message' => 'Yeni Quiz başarıyla oluşturuldu! 🚀',
            'redirect' => route("quiz.add.questions", $quiz) // Yönlendirilecek adresi JS'e gönderiyoruz
        ]);
        // return redirect()->route("quiz.add.questions", $quiz)->with('success', 'Yeni Quiz Oluşturuldu.');
    }

    public function add_questions(Request $request)
    {

        if (!Auth::check()) {
            return redirect()->route("home");
        }
        //  Laravel array parse edebilmesi için
        //  questions[index][field] şeklinde veri bekliyor 
        //  o yüzden o şekilde gönderdiki validate işlemi yapılabilsin
        $request->validate([
            'questions.*.title' => 'required|string|max:255',
            'questions.*.content' => 'required|string|max:1000',
            'questions.*.points' => 'required|integer|min:1|max:10',
            'questions.*.img_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*.answer_content' => 'required|string|max:255',
            'questions.*.answers.*.is_correct' => 'required|boolean',
        ]);

        foreach ($request->questions as $index => $q) {
            $path = null;
            if (isset($q["img_url"])) {
                $filename = uniqid() . "-" . $q['img_url']->getClientOriginalName();
                $path = $q['img_url']->storeAs("uploads", $filename, "public");
            }

            $question = Question::create([
                'quiz_id' =>  $request->quizId,
                'title' => $q['title'],
                'question_text' => $q['content'],
                'points' => $q['points'],
                'img_url' => $path, // yoksa null gidecek
            ]);

            foreach ($q["answers"] as $answer) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $answer['answer_content'],
                    'is_correct' => $answer['is_correct'] ?? false,
                ]);
            }
        }


        return response()->json([
            "redirect" => route("library.show")
        ]);
    }

    public function ai_generate(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route("home");
        }

        $client = new Client();

        $request->validate([
            "title" => "required|string|max:255",
            "img_url" => "nullable|image|mimes:jpg,jpeg,png|max:2048",
            "subject" => "required|string|max:255",
            "description" => "nullable|string|max:500",
            "number_of_questions" => "required|integer|min:4|max:20",
            "number_of_options" => "required|integer|min:2|max:6",
            "difficulty" => "required|string|in:easy,medium,hard,expert",
            "duration_minutes" => "required|integer|min:1|max:120",
            "wrong_to_correct_ratio" => "nullable|integer|min:0|max:10",
        ]);


        $prompt = "Bana \"{$request->title}\" başlığında , \" {$request->subject}\" konusunda ,\"{$request->description}\" buna uygun ,{$request->number_of_questions} adet çoktan seçmeli soru oluştur .
        Her soru {$request->number_of_options} seçenekli olacak ve zorluk seviyesi \"{$request->difficulty}\" olacak.
        Çıktı formatı KESİNLİKLE ve SADECE aşağıdaki JSON yapısına uymalıdır. JSON bloğu dışında HİÇBİR ek metin, açıklama veya başlık (örneğin 'İşte sorularınız:' gibi) **EKLEME**:

        {
        \"questions\": [
            {
            \"title\": \"Soru Başlığı\",
            \"content\": \"Soru metni...\",
            \"points\": 1,
            \"answers\": [
                { \"answer_content\": \"Seçenek A\", \"is_correct\": false },
                { \"answer_content\": \"Seçenek B\", \"is_correct\": true },
                { \"answer_content\": \"Seçenek C\", \"is_correct\": false },
                { \"answer_content\": \"Seçenek D\", \"is_correct\": false }
            ]
            }
        ]
        }";

        $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'contents' => [[
                    'parts' => [['text' => $prompt]]
                ]]
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        $outputText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        // Konsola yazmak için
        $out = new ConsoleOutput();
        $out->writeln("🔹 AI Raw Output:");
        $out->writeln($outputText ?? '--- boş geldi ---');

        $cleanText = preg_replace('/^```json|```$/m', '', trim($outputText));
        $cleanText = preg_replace('/```[a-z]*\n?/', '', $cleanText);
        $cleanText = str_replace(["\r", "\n"], ' ', $cleanText);

        $generatedQuestions = json_decode($cleanText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            dd('JSON Hatası:', json_last_error_msg(), $cleanText);
        }

        #img için store işlemleri
        $path = null;
        if ($request->hasFile("img_url")) {
            # benzersiz isim 
            $filename = uniqid() . "-" . $request->file("img_url")->getClientOriginalName();
            # storage/app/public/uploads/ kısmına img unique bir şekilde kaydetme 
            $path = $request->file("img_url")->storeAs("uploads", $filename, "public");
        }

        $quiz = Quiz::create([
            'title' => $request->title,
            'subject' => $request->subject,
            'description' => $request->description,
            'difficulty' => $request->difficulty,
            'number_of_questions' => $request->number_of_questions,
            'number_of_options' => $request->number_of_options,
            'duration_minutes' => $request->duration_minutes,
            'wrong_to_correct_ratio' => $request->wrong_to_correct_ratio ?? 0,
            'img_url' => $path ?? null,
            'user_id' => Auth::id(),
        ]);

        try {
            foreach ($generatedQuestions['questions'] as $q) {
                $question = $quiz->questions()->create([
                    'title' => $q['title'],
                    'question_text' => $q['content'],
                    'points' => $q['points'] ?? 1,
                ]);

                foreach ($q['answers'] as $a) {
                    $question->answers()->create([
                        'answer_text' => $a['answer_content'],
                        'is_correct' => (bool)$a['is_correct'],
                    ]);
                }
            }
        } catch (\Throwable $e) {
            dd('Hata:', $e->getMessage());
        }

        #librarye ekleme
        $user = Auth::user();

        /** @var \App\Models\User $user */  #PHP Intelephense sorunu
        $user->libraryQuizzes()->attach($quiz->id);

        //Eski
        //return redirect()->route("library.show")->with('success', 'Yeni Quiz Oluşturuldu.');

        return response()->json([
            'success' => true,
            'message' => 'Yeni Quiz başarıyla oluşturuldu! 🚀',
            'redirect' => route("library.show")
        ]);
    }


    public function delete_quiz(Quiz $quiz)
    {
        if (!Auth::check())  return redirect()->route("home")->with("error", "Önce giriş yapmalısınız.");
        $userId = Auth::id();
        $quiz_item = Quiz::find($quiz->id);
        if (!$quiz_item) {
            return redirect()->back()->with("error", "Quiz bulunamadı.");
        }
        if ($quiz_item->user_id !== $userId) {
            return redirect()->back()->with("error", "Bu işlemi yapmaya yetkiniz yok.");
        }
        $quiz_item->delete();

        return redirect()->route("library.show");
    }
}
