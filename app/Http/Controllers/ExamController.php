<?php

namespace App\Http\Controllers;

use App\Models\ExamPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Http;
use App\Models\Category; 



class ExamController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view("pages.create_exam",compact("categories"));
    }

    public function test()
    {
        return view("pages.test");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'elements' => 'required|array', // Canvas verisi
            'page_count' => 'required|integer',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id'
        ]);

        $exam = ExamPaper::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'canvas_data' => $validated['elements'],
            'page_count' => $validated['page_count'],
            'description'=> $validated['description'],
            'is_public' => $validated['is_public']           
        ]);

        // 3. Kategorileri Bağla (Varsa)
        if ($request->has('categories')) {
            $exam->categories()->sync($request->categories);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sınav kağıdı başarıyla kaydedildi!',
            'id' => $exam->id
        ]);
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $examPaper = ExamPaper::with('categories')->where('id', $id)->where('user_id', $userId)->firstOrFail();
        $categories = Category::all(); 
        return view('pages.create_exam', compact('examPaper','categories'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasyon
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'elements' => 'required|array', // Yeni canvas verisi
            'page_count' => 'required|integer',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id'
        ]);

        // 2. Sınavı Bul (Sadece giriş yapan kullanıcının sınavı ise!)
        $exam = ExamPaper::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail(); // Bulamazsa 404 atar

        // 3. Güncelle
        $exam->update([
            'title' => $validated['title'],
            'canvas_data' => $validated['elements'],
            'page_count' => $validated['page_count'],
            'description'=> $validated['description'],
            'is_public' => $validated['is_public']   
        ]);

        if ($request->has('categories')) {
            $exam->categories()->sync($request->categories);
        } else {
            // Eğer boş array geldiyse veya hiç gelmediyse kategorileri temizle
            // (Opsiyonel: Eğer kategori silinsin istemiyorsan burayı kaldır)
            $exam->categories()->detach();
        }

        // 4. JSON Cevap Dön (Axios için)
        return response()->json([
            'success' => true,
            'message' => 'Sınav kağıdı güncellendi!'
        ]);
    }

    public function destroy($id)
    {
        $exam = ExamPaper::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $exam->delete();

        return redirect()->route('library.show')->with('success', 'Sınav kağıdı başarıyla silindi.');
    }


    public function uploadImage(Request $request)
    {
        // 1. Dosya var mı ve resim mi kontrol et
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // 2. Benzersiz bir isim oluştur (zaman + rastgele sayı)
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // 3. 'public' diskindeki 'exam-images' klasörüne kaydet
            // (storage/app/public/exam-images altına gider)
            $path = $file->storeAs('exam-images', $filename, 'public');

            // 4. URL'i geri döndür
            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Dosya yüklenemedi'], 400);
    }

    public function downloadPDF($id)
    {
        $exam = ExamPaper::findOrFail($id);

        // 1. JSON verisini al (Senin attığın array)
        $rawData = is_string($exam->canvas_data) ? json_decode($exam->canvas_data, true) : $exam->canvas_data;

        // 2. Veriyi SAYFA numarasına göre grupla
        // Çıktı şöyle olacak: [ 1 => [öğeler...], 2 => [öğeler...] ]
        $pages = collect($rawData)->groupBy('page');

        $html = View::make('pdf.exam-canvas-pdf', compact('exam', 'pages'))->render();

        $filename = Str::slug($exam->title) . '.pdf';

        $pdf = Browsershot::html($html)
            ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
            ->format('A4')
            ->margins(0, 0, 0, 0) // Kenar boşluğu 0, çünkü canvas'ta her şeyi sen ayarladın
            ->showBackground()
            ->pdf();

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function previewPDF($id)
    {
        $exam = ExamPaper::findOrFail($id);

        // --- (HTML oluşturma kısımları aynen kalsın) ---
        $rawData = is_string($exam->canvas_data) ? json_decode($exam->canvas_data, true) : $exam->canvas_data;
        $pages = collect($rawData)->groupBy('page');
        $html = view('pdf.exam-canvas-pdf', compact('exam', 'pages'))->render();
        $filename = Str::slug($exam->title) . '.pdf';

        // Browsershot ayarları (Aynen kalsın)
        $pdf = Browsershot::html($html)
            ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->pdf();

        // --- FARKLILIK BURADA ---
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            // 'attachment' yerine 'inline' yapıyoruz. Bu "Tarayıcıda Göster" demektir.
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }


    public function generate_batch_questions(Request $request)
    {
        // 1. Yetki ve Validasyon
        if (!Auth::check()) {
            return response()->json(['message' => 'Oturum süreniz dolmuş.'], 401);
        }

        $apiKey = env('GEMINI_API_KEY');

        $request->validate([
            'prompt' => 'nullable|string',
            'context' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,docx,txt|max:10240',
            'rules' => 'required|string', // JSON string olarak gelecek
        ]);

        $rules = json_decode($request->rules, true); // Kuralları array'e çevir

        // 2. Dosya Yükleme (Varsa)
        $fileUri = null;
        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $mimeType = $file->getMimeType();
                $fileData = file_get_contents($file->getPathname());

                $uploadResponse = Http::withHeaders([
                    'X-Goog-Upload-Protocol' => 'raw',
                    'X-Goog-Upload-Command' => 'start, upload, finalize',
                    'X-Goog-Upload-Header-Content-Length' => $file->getSize(),
                    'X-Goog-Upload-Header-Content-Type' => $mimeType,
                    'Content-Type' => $mimeType,
                ])->withBody($fileData, $mimeType)
                ->post("https://generativelanguage.googleapis.com/upload/v1beta/files?key={$apiKey}");

                if ($uploadResponse->failed()) throw new \Exception("Upload Failed");
                $fileUri = $uploadResponse->json()['file']['uri'];

            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Dosya yüklenemedi: ' . $e->getMessage()], 500);
            }
        }

        // 3. Prompt Kurgusu (Kurallara Göre)
        $rulesText = "";
        foreach ($rules as $idx => $rule) {
            $num = $idx + 1;
            $typeMap = [
                'multiple_choice' => 'Çoktan Seçmeli (A,B,C,D şıklı)',
                'open_ended' => 'Klasik (Açık Uçlu)',
                'true_false' => 'Doğru/Yanlış',
                'fill_in_blanks' => 'Boşluk Doldurma'
            ];
            $typeName = $typeMap[$rule['type']] ?? $rule['type'];
            $optCount = isset($rule['option_count']) ? "{$rule['option_count']} seçenekli" : "";
            
            $rulesText .= "GRUP {$num}: {$rule['count']} adet, {$typeName}, Zorluk: {$rule['difficulty']}. {$optCount}\n";
        }

        $systemInstruction = "Sen bir sınav hazırlama asistanısın. Verilen kaynağı (dosya veya metin) analiz et ve aşağıdaki KURALLARA tam uyarak soru grupleri oluştur.";
        
        $userPrompt = "
        KAYNAK KONU: {$request->prompt}
        KAYNAK METİN: {$request->context}
        
        İSTENEN SORU GRUPLARI:
        {$rulesText}

        ÇIKTI FORMATI (JSON):
        Aşağıdaki JSON şemasına sadık kal. Her 'GRUP' için ayrı bir dizi elemanı oluştur.
        
        {
        \"groups\": [
            {
            \"group_id\": 1,
            \"type\": \"multiple_choice (veya open_ended/true_false/fill_in_blanks)\",
            \"difficulty\": \"easy\",
            \"questions\": [
                { 
                    \"question\": \"Soru metni...\",
                    \"options\": [\"A şıkkı\", \"B şıkkı\", \"C şıkkı\", \"D şıkkı\"], (Sadece çoktan seçmeli ise)
                    \"answer\": \"Doğru cevap veya anahtar kelime\",
                    \"point\": \"10\"
                }
            ]
            }
        ]
        }
        ";

        // 4. Payload ve İstek
        $parts = [];
        if ($fileUri) {
            $parts[] = ['file_data' => ['mime_type' => $request->file('file')->getMimeType(), 'file_uri' => $fileUri]];
        }
        $parts[] = ['text' => $systemInstruction . "\n" . $userPrompt];

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(180)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => $parts]],
                    'generationConfig' => ['responseMimeType' => 'application/json', 'temperature' => 0.7]
                ]);

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Temizlik
            $cleanText = preg_replace('/^```json|```$/m', '', trim($text));
            $result = json_decode($cleanText, true);

            if (json_last_error() !== JSON_ERROR_NONE) throw new \Exception("JSON Parse Hatası");

            return response()->json(['success' => true, 'data' => $result]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function togglePublic($id)
    {
        $exam = ExamPaper::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        // Durumu tersine çevir
        $exam->is_public = !$exam->is_public;
        $exam->save();

        return response()->json([
            'success' => true,
            'is_public' => $exam->is_public,
            'message' => $exam->is_public ? 'Sınav herkese açık hale getirildi.' : 'Sınav gizlendi (Private).'
        ]);
    }

}
