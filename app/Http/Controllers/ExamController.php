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

        $exam->increment('downloads_count');

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
        Aşağıdaki JSON şemasına kesinlikle sadık kal. Her 'GRUP' için ayrı bir dizi elemanı oluştur.
        
        {
        \"groups\": [
            {
            \"group_id\": 1,
            \"type\": \"multiple_choice (veya open_ended/true_false/fill_in_blanks)\",
            \"difficulty\": \"easy\",
            \"questions\": [
                { 
                    \"question\": \"Soru metni...\",
                    \"options\": [\"A) Seçenek\", \"B) Seçenek\", \"C) Seçenek\", \"D) Seçenek\"], // (Sadece multiple_choice ise doldur, yoksa boş dizi bırak)
                    \"answer\": \"B\", // DİKKAT: Eğer multiple_choice ise SADECE doğru şıkkın harfini (A, B, C, D) yaz. Eğer true_false ise SADECE 'Doğru' veya 'Yanlış' yaz. Eğer fill_in_blanks ise boşluklara gelecek kelimeleri virgülle ayırarak yaz. Eğer open_ended ise kısa ve öz bir örnek cevap yaz.,
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


    public function fork($id)
    {
        // Orijinal sınav
        $originalExam = ExamPaper::findOrFail($id);

        $newExam = $originalExam->replicate();
      
        $newExam->user_id = Auth::id();
        $newExam->title = $originalExam->title . ' (Kopya)';
        $newExam->is_public = false; 
        $newExam->downloads_count = 0; 
        $newExam->save();

        if ($originalExam->categories) {
            $newExam->categories()->sync($originalExam->categories->pluck('id'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Sınav başarıyla kütüphanenize kopyalandı!',
            'redirect' => route('exam.edit', $newExam->id)
        ]);
    }


    // Özet Çıkarma (Dosya Yükleme)
    public function createStudyGuide()
    {
        $categories = Category::all();
        return view("pages.study_quide",compact("categories")); 
        
    }

    
    public function generateStudyGuide(Request $request)
    {
        // 1. Validasyon
        $request->validate([
            'document' => 'nullable|file|mimes:pdf,txt,docx|max:10240', // 10MB sınır
            'text_content' => 'nullable|string',
            'detail_level' => 'required|in:concise,detailed,exam_prep',
            'custom_instructions' => 'nullable|string|max:2500',
            'output_language' => 'nullable|string', 
            'tone' => 'nullable|string' ,
        ]);

        if (!$request->hasFile('document') && empty($request->text_content)) {
            return back()->with('error', 'Lütfen bir dosya yükleyin veya metin girin.');
        }

        set_time_limit(300); // Süreyi 5 dakikaya (300 saniye) çıkarır

        $apiKey = env('GEMINI_API_KEY');
        $fileUri = null;

        // 2. Dosya Yükleme (Google Gemini File API)
        if ($request->hasFile('document')) {
            try {
                $file = $request->file('document');
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

                if ($uploadResponse->failed()) {
                    throw new \Exception("Gemini Upload Failed");
                }
                
                $fileUri = $uploadResponse->json()['file']['uri'];

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Dosya analiz için yapay zekaya yüklenemedi: ' . $e->getMessage()
                ], 500);
            }
        }

        
        // 3. Prompt (İstek) Kurgusu (Sertleştirilmiş Kurallar)
        $detailInstruction = match($request->detail_level) {
            'concise' => 'Çok kısa ve öz, sadece en kritik maddeleri içeren',
            'detailed' => 'Detaylı anlatımlı, konuyu tam kavratan',
            'exam_prep' => 'Sınavda çıkabilecek önemli vurguları barındıran',
            default => 'Detaylı'
        };

        $systemInstruction = "Sen uzman bir eğitimcisin. Verilen kaynağı analiz et ve bir 'Ders Notu / Özet' oluştur.
        Detay Seviyesi: {$detailInstruction}.
        
        ÇOK ÖNEMLİ KURALLAR:
        1. Çıktın sadece düz metin destekleyen bir çizim motoruna gidecek. Bu yüzden KESİNLİKLE satır içi markdown (**kalın**, *italik*, `kod`) KULLANMA.
        2. LİSTELER: Madde işaretli listeleri KESİNLİKLE 'box' olarak verme! Bunları 'text' tipi olarak ver ve her bir maddenin başına '• ' (yuvarlak madde imi) koy.
        3. KUTULAR: Sadece çok kritik uyarıları ve formülleri 'box' objesi olarak ver.
        4. KODLAR: Yazılım kodlarını, terminal komutlarını veya '#include <...>' gibi yapıları kesinlikle 'code' objesi olarak ver.
        
        ÇIKTI FORMATI (SADECE JSON):
        [
            { \"type\": \"heading\", \"content\": \"Ana Başlık\" },
            { \"type\": \"sub_heading\", \"content\": \"Alt Başlık\" },
            { \"type\": \"text\", \"content\": \"Düz açıklayıcı paragraf metni.\" },
            { \"type\": \"text\", \"content\": \"• Birinci madde\n• İkinci madde\n• Üçüncü madde\" },
            { \"type\": \"box\", \"content\": \"Önemli not, tanım, formül veya uyarı.\" },
            { \"type\": \"code\", \"content\": \"#include <iostream>\n\nint main() {\n    return 0;\n}\" }  
        ]";
        
        $customFocus = "";
        if (!empty($request->custom_instructions)) {
            $customFocus = "\n\nKULLANICININ ÖZEL TALİMATI: \"" . $request->custom_instructions . "\"\nBu talimata kesinlikle uymalısın.";
        }

        $languageInstruction = "";
        if (!empty($request->output_language) && $request->output_language !== 'auto') {
            $languageInstruction = "\n\nÇIKTI DİLİ: Özeti KESİNLİKLE " . $request->output_language . " dilinde oluştur.";
        }

        $toneInstruction = "";
        if (!empty($request->tone) && $request->tone !== 'standard') {
            $toneInstruction = "\n\nANLATIM TARZI VE ZORLUK: Anlatım dilini şu seviyeye göre uyarla: " . $request->tone . ".";
        }

        // Bütün komutları birleştir
        $userPrompt = "KAYNAK METİN: " . ($request->text_content ?? 'Sadece dosyayı analiz et.') . $customFocus . $languageInstruction . $toneInstruction;

        // 4. Payload ve Gemini İstek
        $parts = [];
        if ($fileUri) {
            $parts[] = ['file_data' => ['mime_type' => $request->file('document')->getMimeType(), 'file_uri' => $fileUri]];
        }
        $parts[] = ['text' => $systemInstruction . "\n" . $userPrompt];

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(180)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => $parts]],
                    // Çıktıyı JSON formatında zorlamak için Config ekliyoruz
                    'generationConfig' => ['responseMimeType' => 'application/json', 'temperature' => 0.7]
                ]);

            if ($response->failed()) {
                 throw new \Exception("Gemini API isteği başarısız oldu.");
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
            
            // JSON Temizliği (Her ihtimale karşı)
            $cleanText = preg_replace('/^```json|```$/m', '', trim($text));
            $aiItems = json_decode($cleanText, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($aiItems)) {
                throw new \Exception("Yapay zeka geçerli bir JSON üretemedi.");
            }
            $elements = [];
            $currentY = 60; 
            $currentPage = 1; 
            $pageHeightLimit = 1000; 

            foreach ($aiItems as $item) {
                $aiType = $item['type'] ?? 'text';
                $content = trim($item['content'] ?? '');
                
                if (empty($content)) continue; 
                
                $content = str_replace('**', '', $content); 
                $content = str_replace('`', '', $content);  
                $content = preg_replace('/^[\-\*]\s/m', '• ', $content); 
                
                // --- AKIL FİLTRESİ ---
                // Eğer AI inat edip listeyi (•) veya uzun bir yazıyı kutu yaparsa, zorla texte çevir!
                if ($aiType === 'box' && (strpos($content, '•') !== false || mb_strlen($content) > 150)) {
                    $aiType = 'text';
                }
                
                // --- KULLANICI TEMA SEÇİMİ (Döngünün hemen içine ekle) ---
                $isColored = ($request->color_theme === 'colored');

                // --- SENİN ORİJİNAL KODLARIN VE DEĞİŞKENLERİN BAŞLIYOR ---
                $elementType = in_array($aiType, ['box', 'code']) ? 'text' : $aiType;

                $fontSize = 14; $fontWeight = 'normal'; $width = 700; $height = 50;
                $backgroundColor = 'transparent'; $borderWidth = 0; $fontFamily = 'sans-serif';
                $textColor = '#000000'; $borderColor = '#000000';

                if ($aiType === 'heading') { 
                    $fontSize = 22; $fontWeight = 'bold'; 
                    // YENİ: Renkliyse Kırmızı, yoksa standart siyah
                    if ($isColored) $textColor = '#d32f2f'; 
                    $lines = ceil(mb_strlen($content) / 40); 
                    $height = ($lines * 35) + 30; 
                }
                elseif ($aiType === 'sub_heading') { 
                    $fontSize = 16; $fontWeight = 'bold'; 
                    // YENİ: Renkliyse Açık Kırmızı/Bordo tonu, yoksa siyah
                    if ($isColored) $textColor = '#ef4444'; 
                    $lines = ceil(mb_strlen($content) / 60); 
                    $height = ($lines * 28) + 25;
                }
                elseif ($aiType === 'box') { 
                    $width = 700; 
                    $borderWidth = 0; 
                    $backgroundColor = 'transparent'; 
                    $fontWeight = 'bold'; 
                    
                    // YENİ: Renkliyse raptiye ve kırmızı, siyah beyazsa ünlem ve siyah (Senin orijinalindeki gibi)
                    if ($isColored) {
                        $textColor = '#d32f2f';
                        $content = "📌 " . $content;
                    } else {
                        $content = "! " . $content;
                    }
                    
                    $lines = ceil(mb_strlen($content) / 90); 
                    $height = ($lines * 20) + 20;
                }
                elseif ($aiType === 'code') {
                    $width = 680; $borderWidth = 1; 
                    $backgroundColor = '#1e1e1e'; 
                    $borderColor = '#333333'; 
                    $fontSize = 13; $fontFamily = 'monospace'; 
                    
                    // YENİ: Orijinalinde yazı gri/beyazdı (#d4d4d4). Renkliyse Yeşil (#4ade80) olacak.
                    $textColor = $isColored ? '#4ade80' : '#d4d4d4'; 
                    
                    $manualLines = substr_count($content, "\n") + 1; 
                    $autoLines = ceil(mb_strlen($content) / 80);
                    $lines = max($manualLines, $autoLines); 
                    
                    $height = ($lines * 18) + 40; 
                }
                elseif ($aiType === 'text') {
                    $manualLines = substr_count($content, "\n") + 1;
                    $autoLines = ceil(mb_strlen($content) / 75); 
                    $lines = max($manualLines, $autoLines);
                    $height = ($lines * 22) + 30;
                }

                if ($currentY + $height > $pageHeightLimit) {
                    $currentPage++;
                    $currentY = 60; 
                }

                $elements[] = [
                    'id' => time() . rand(1000, 99999),
                    'page' => $currentPage,
                    'type' => $elementType,
                    'content' => $content,
                    'x' => 47, 'y' => $currentY, 'w' => $width, 'h' => $height,
                    'styles' => [
                        'fontSize' => $fontSize,
                        'fontWeight' => $fontWeight,
                        'fontFamily' => $fontFamily,
                        'color' => $textColor,
                        'textAlign' => 'left',
                        'borderWidth' => $borderWidth,
                        'borderColor' => $borderColor,
                        'backgroundColor' => $backgroundColor,
                        'padding' => ($borderWidth > 0 ? 15 : 0) // Kutu ve Kod için rahat iç boşluk
                    ]
                ];
                
                $currentY += $height + 25; 
            }

            // 6. Veritabanına Kaydet (page_count kısmını dinamik yaptık!)
            $exam = ExamPaper::create([
                'user_id' => Auth::id(),
                'title' => 'AI Ders Notu - ' . date('d.m.Y'),
                'description' => 'Yapay zeka tarafından üretilmiş çalışma kağıdı.',
                'is_public' => false,
                'page_count' => $currentPage, // Kaç sayfa sürdüyse onu kaydet
                'document_type' => 'study_guide',
                'canvas_data' => $elements
            ]);
            
            if ($request->has('categories') && is_array($request->categories)) {
                $exam->categories()->sync($request->categories);
            }

            // 7. Yönlendirmedeki gereksiz auto_layout parametresini sildik!
            return response()->json([
                'success' => true,
                'message' => 'Özet başarıyla oluşturuldu! Editör hazırlanıyor...',
                'redirect' => route('exam.edit', ['id' => $exam->id]) // TEMİZ URL
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

}
