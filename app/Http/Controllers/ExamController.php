<?php

namespace App\Http\Controllers;

use App\Models\ExamPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

class ExamController extends Controller
{
    public function index()
    {
        return view("pages.create_exam");
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
            'page_count' => 'required|integer'
        ]);

        $exam = ExamPaper::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'canvas_data' => $validated['elements'],
            'page_count' => $validated['page_count'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sınav kağıdı başarıyla kaydedildi!',
            'id' => $exam->id
        ]);
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $examPaper = ExamPaper::where('id', $id)->where('user_id', $userId)->firstOrFail();
        return view('pages.create_exam', compact('examPaper'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasyon
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'elements' => 'required|array', // Yeni canvas verisi
            'page_count' => 'required|integer'
        ]);

        // 2. Sınavı Bul (Sadece giriş yapan kullanıcının sınavı ise!)
        $exam = ExamPaper::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail(); // Bulamazsa 404 atar

        // 3. Güncelle
        $exam->update([
            'title' => $validated['title'],
            'canvas_data' => $validated['elements'],
            'page_count' => $validated['page_count']
        ]);

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
}
