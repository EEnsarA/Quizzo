<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Models\QuizResult;
use App\Models\UserLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Prompts\Output\ConsoleOutput;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Category;

class QuizController extends Controller
{
    public function create_quiz()
    {
        
        $categories = Category::orderBy('name')->get();
        return view("pages.create", compact('categories'));
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
            "categories" => "nullable|array", 
            "categories.*" => "exists:categories,id",
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

        
        if ($request->has('categories')) {
            $quiz->categories()->sync($request->categories);
        }



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

    public function save_questions(Request $request)
    {
        // 1. Yetki ve Varlık Kontrolü
        if (!Auth::check()) {
            return response()->json(['message' => 'Oturum süreniz dolmuş.'], 401);
        }

        $quiz = Quiz::find($request->quizId);

        if (!$quiz) {
            return response()->json(['message' => 'Quiz bulunamadı.'], 404);
        }

        if ($quiz->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bu işlem için yetkiniz yok.'], 403);
        }

        // 2. Validasyon (Gevşetilmiş)
        // ID alanı bazen olabilir bazen olmayabilir (nullable), resim bazen string bazen dosya olabilir.
        $request->validate([
            'questions' => 'required|array',
            'questions.*.title' => 'required|string|max:255',
            'questions.*.content' => 'required|string|max:1000',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.answers' => 'required|array|min:2',
        ]);

        // 3. İşlem Gören ID'leri Tutacak Dizi (Silme işlemi için gerekli)
        $processedQuestionIds = [];

        foreach ($request->questions as $qData) {
            
            $question = null;

            // A) GÜNCELLEME KONTROLÜ
            // Eğer formdan 'id' geldiyse, bu eski bir sorudur. Veritabanından bulalım.
            if (isset($qData['id']) && $qData['id']) {
                $question = Question::where('id', $qData['id'])->where('quiz_id', $quiz->id)->first();
            }

            // B) RESİM YÜKLEME MANTIĞI
            // Varsayılan olarak eski yolu koruruz.
            $path = $question ? $question->img_url : null; 

            // Eğer yeni bir dosya yüklendiyse (UploadedFile nesnesi ise)
            if (isset($qData["img_url"]) && $qData["img_url"] instanceof \Illuminate\Http\UploadedFile) {
                // (İstersen burada eski resmi silebilirsin: Storage::delete($question->img_url))
                $filename = uniqid() . "-" . $qData['img_url']->getClientOriginalName();
                $path = $qData['img_url']->storeAs("uploads", $filename, "public");
            }

            // C) KAYIT VEYA GÜNCELLEME (UpdateOrCreate Mantığı)
            if ($question) {
                // --- GÜNCELLEME ---
                $question->update([
                    'title' => $qData['title'],
                    'question_text' => $qData['content'],
                    'points' => $qData['points'],
                    'img_url' => $path, // Yeni resim varsa o, yoksa eskisi
                ]);
            } else {
                // --- YENİ KAYIT ---
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'title' => $qData['title'],
                    'question_text' => $qData['content'],
                    'points' => $qData['points'],
                    'img_url' => $path,
                ]);
            }

            // Bu ID'yi işlenenler listesine ekle (Silinmeyecekler listesi)
            $processedQuestionIds[] = $question->id;

            // D) CEVAPLARI SENKRONİZE ET (En Temiz Yöntem: Sil ve Yeniden Ekle)
            // Cevapların ID'lerini takip etmek zordur, sıfırlayıp eklemek daha güvenlidir.
            $question->answers()->delete();

            if (isset($qData["answers"]) && is_array($qData["answers"])) {
                foreach ($qData["answers"] as $answer) {
                    // Boş şıkları kaydetme (Validation yapsa da ekstra önlem)
                    if(empty($answer['answer_content'])) continue;

                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $answer['answer_content'],
                        'is_correct' => isset($answer['is_correct']) ? (bool)$answer['is_correct'] : false,
                    ]);
                }
            }
        }

        // 4. TEMİZLİK (SİLME) İŞLEMİ
        // Veritabanında olan AMA formdan gelen listede ($processedQuestionIds) OLMAYAN soruları sil.
        // Yani kullanıcı arayüzde "Soruyu Sil" dediyse veya soru sayısını düşürdüyse buradan uçar.
        $quiz->questions()->whereNotIn('id', $processedQuestionIds)->delete();

        return response()->json([
            "success" => true,
            "message" => "Sorular başarıyla kaydedildi ve güncellendi! ✅",
            "redirect" => route("library.show")
        ]);
    }

    public function ai_generate(Request $request)
    {
        set_time_limit(180);
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Oturum süreniz dolmuş.'], 401);
            }
            return redirect()->route("home");
        }

        $apiKey = env('GEMINI_API_KEY');

        $request->validate([
            "title" => "required|string|max:255",
            "img_url" => "nullable|image|mimes:jpg,jpeg,png|max:2048",
            // PDF, DOCX, TXT 
            "source_file" => "nullable|file|mimes:pdf,docx,txt|max:10240",
            "subject" => "nullable|string|max:255",
            "description" => "nullable|string|max:500",
            "number_of_questions" => "required|integer|min:4|max:20",
            "number_of_options" => "required|integer|min:2|max:6",
            "difficulty" => "required|string|in:easy,medium,hard,expert",
            "duration_minutes" => "required|integer|min:1|max:120",
            "wrong_to_correct_ratio" => "nullable|integer|min:0|max:10",
            "categories" => "nullable|array", 
            "categories.*" => "exists:categories,id",
        ]);

        // Dosya Yükleme (Google File API)
        $fileUri = null;
        if ($request->hasFile('source_file')) {
            try {
                $file = $request->file('source_file');
                $mimeType = $file->getMimeType();
                $fileSize = $file->getSize();
                $fileData = file_get_contents($file->getPathname());

                $uploadResponse = Http::withHeaders([
                    'X-Goog-Upload-Protocol' => 'raw',
                    'X-Goog-Upload-Command' => 'start, upload, finalize',
                    'X-Goog-Upload-Header-Content-Length' => $fileSize,
                    'X-Goog-Upload-Header-Content-Type' => $mimeType,
                    'Content-Type' => $mimeType,
                ])->withBody($fileData, $mimeType)
                    ->post("https://generativelanguage.googleapis.com/upload/v1beta/files?key={$apiKey}");

                if ($uploadResponse->failed()) {
                    throw new \Exception("Google Upload Hatası: " . $uploadResponse->body());
                }

                
                $fileUri = $uploadResponse->json()['file']['uri'] ?? null;
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Dosya işleme hatası: ' . $e->getMessage()], 500);
            }
        }

        
        $jsonSchema = '{
            "questions": [
                {
                "title": "Soru Başlığı",
                "content": "Soru metni...",
                "points": 5,
                "answers": [
                    { "answer_content": "Seçenek A", "is_correct": false },
                    { "answer_content": "Seçenek B", "is_correct": true }
                ]
                }
            ]
        }';

       
        $systemInstruction = "Sen uzman bir sınav hazırlayıcısısın. ";

        if ($fileUri) {
            $systemInstruction .= "Sana verdiğim DÖKÜMANI kaynak olarak kullanacaksın. ";
            $systemInstruction .= "ÖNEMLİ: Dökümanın tamamından rastgele soru sorma. ";
            $systemInstruction .= "Dökümanın içinden SADECE aşağıda belirttiğim 'Konu' ve 'Açıklama' ile alakalı kısımları bul, analiz et ve oradan soru türet. ";
        } else {
            $systemInstruction .= "Aşağıdaki konu başlıklarına göre özgün ve öğretici sorular üret. ";
        }

        $userPrompt = "
        --- KURALLAR ---
        1. KONU ODAĞI: \"{$request->title}\" - \"{$request->subject}\".
        2. DETAY/BAĞLAM: \"{$request->description}\". (Sorular bu bağlama uygun olmalı).
        3. ZORLUK: \"{$request->difficulty}\" (Lütfen bu seviyeye tam uy).
        4. ADET: Tam olarak {$request->number_of_questions} soru.
        5. SEÇENEK: Her soruda {$request->number_of_options} seçenek.
        
        ÇIKTI FORMATI: Sadece ve sadece aşağıdaki JSON yapısında çıktı ver. Başka hiçbir metin, başlık veya açıklama yazma.";

      
        $parts = [];

       
        if ($fileUri) {
            $parts[] = [
                'file_data' => [
                    'mime_type' => $request->file('source_file')->getMimeType(),
                    'file_uri' => $fileUri
                ]
            ];
        }

     
        $parts[] = [
            'text' => $systemInstruction . "\n" . $userPrompt . "\n\nJSON ŞEMASI:\n" . $jsonSchema
        ];

        // Gemini 2.5 API İsteği
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])
                ->timeout(180)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['parts' => $parts]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json', // JSON Garantisi
                        'temperature' => 0.7
                    ]
                ]);

            $data = $response->json();

        
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error("AI Boş Döndü", ['response' => $data]);
                throw new \Exception("AI yanıt üretemedi.");
            }

            $jsonString = $data['candidates'][0]['content']['parts'][0]['text'];

            $cleanText = preg_replace('/^```json|```$/m', '', trim($jsonString));
            $generatedQuestions = json_decode($cleanText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON Parse Hatası");
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'AI Hatası: ' . $e->getMessage()], 500);
        }

        //Veritabanı Kayıt

        //Kapak Resmi 
        $path = null;
        if ($request->hasFile("img_url")) {
            $filename = uniqid() . "-" . $request->file("img_url")->getClientOriginalName();
            $path = $request->file("img_url")->storeAs("uploads/quizImages", $filename, "public");
        }

        //Quiz 
        $quiz = Quiz::create([
            'title' => $request->title,
            'subject' => $request->subject ?? 'AI Generated',
            'description' => $request->description,
            'difficulty' => $request->difficulty,
            'number_of_questions' => $request->number_of_questions,
            'number_of_options' => $request->number_of_options,
            'duration_minutes' => $request->duration_minutes,
            'wrong_to_correct_ratio' => $request->wrong_to_correct_ratio ?? 0,
            'img_url' => $path,
            'user_id' => Auth::id(),
        ]);

        if ($request->has('categories')) {
            $quiz->categories()->sync($request->categories);
        }

        // Soruları ve Cevapları Kaydet
        try {
            foreach ($generatedQuestions['questions'] as $q) {
                $question = $quiz->questions()->create([
                    'title' => $q['title'] ?? 'Soru',
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
            $quiz->delete(); // Hata varsa quizi temizle
            return response()->json(['success' => false, 'message' => 'Kayıt hatası: ' . $e->getMessage()], 500);
        }
        
        // Kütüphaneye Ekle
        /** @var \App\Models\User $user */  #PHP Intelephense sorunu
        Auth::user()->libraryQuizzes()->attach($quiz->id);

        // 8. Başarılı Dönüş
        return response()->json([
            'success' => true,
            'message' => 'Yapay Zeka Quizi Başarıyla Oluşturdu! 🚀',
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




    // --- 4. DÜZENLEME (ADIM 1: Metadata) ---
    public function edit_quiz(Quiz $quiz)
    {
        // Yetki Kontrolü
        if (Auth::id() !== $quiz->user_id) abort(403);

        // Kategorileri yükle
        $categories = Category::orderBy('name')->get();
        
        // Quiz'in seçili kategorilerini ID dizisi olarak al (AlpineJS için)
        $selectedCategories = $quiz->categories->pluck('id')->toArray();

        // create.blade.php'yi yeniden kullanıyoruz ama dolu gönderiyoruz
        return view("pages.create", compact('quiz', 'categories', 'selectedCategories'));
    }

    // --- 5. GÜNCELLEME (ADIM 1: Metadata Kaydı) ---
    public function update_quiz(Request $request, Quiz $quiz)
    {
        if (Auth::id() !== $quiz->user_id) abort(403);

        $request->validate([
            "title" => "required|string|max:255",
            "img_url" => "nullable|image|mimes:jpg,jpeg,png|max:2048",
            "subject" => "required|string|max:255",
            "categories" => "nullable|array",
            // Diğer validasyonlar...
        ]);

        // Resim güncelleme varsa
        $path = $quiz->img_url;
        if ($request->hasFile("img_url")) {
            $filename = uniqid() . "-" . $request->file("img_url")->getClientOriginalName();
            $path = $request->file("img_url")->storeAs("uploads/quizImages", $filename, "public");
        }

        $quiz->update([
            "title" => $request->title,
            "subject" => $request->subject,
            "description" => $request->description,
            "img_url" => $path,
            "difficulty" => $request->difficulty,
            "duration_minutes" => $request->duration_minutes,
            // Soru sayısını değiştirirse aşağıda mantık kurmak gerekir, şimdilik güncelliyoruz
            "number_of_questions" => $request->number_of_questions,
            "number_of_options" => $request->number_of_options,
        ]);

        if ($request->has('categories')) {
            $quiz->categories()->sync($request->categories);
        }

        // Başarılı ise Soruları Düzenleme Sayfasına Yönlendir
        return response()->json([
            'success' => true,
            'message' => 'Quiz ayarları güncellendi! Sorulara geçiliyor...',
            'redirect' => route("quiz.edit.questions", $quiz)
        ]);
    }

    // --- 6. SORULARI DÜZENLEME SAYFASI (ADIM 2) ---
    public function edit_questions(Quiz $quiz)
    {
        if (Auth::id() !== $quiz->user_id) abort(403);
        
        // Soruları ve Cevapları yükle
        $quiz->load('questions.answers');

        // create_question.blade.php'yi kullanacağız
        return view("pages.create_question", compact("quiz"));
    }
}
