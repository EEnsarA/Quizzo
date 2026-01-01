<?php

namespace App\Http\Controllers;

use App\Models\ExamPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
