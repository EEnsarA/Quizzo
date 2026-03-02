<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\UserLibrary;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\ExamPaper;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
public function index(Request $request)
    {
        // 1. URL'den aktif sekmeyi al (Yoksa 'papers' varsayılan)
        $activeTab = $request->get('tab', 'quizzes');

        // --- 1. SINAV KAĞITLARI SORGUSU ---
        // Sadece 'exam' olanları (veya eskiden kalma null olanları) getir
        $paperQuery = ExamPaper::with(['categories', 'user'])
            ->where('is_public', true)
            ->where(function($q) {
                $q->where('document_type', 'exam')->orWhereNull('document_type');
            });

        // --- 2. ÖZETLER / DERS NOTLARI SORGUSU (YENİ) ---
        // Sadece 'study_guide' olanları getir
        $guideQuery = ExamPaper::with(['categories', 'user'])
            ->where('is_public', true)
            ->where('document_type', 'study_guide');

        // --- 3. ONLINE QUIZLER SORGUSU ---
        $quizQuery = Quiz::with(['user', 'results', 'categories'])
            ->withCount(['questions', 'results']); 

        // --- 4. KULLANICI FİLTRESİ ---
        if (Auth::check()) {
            $userId = Auth::id();
            
            // Kendi içeriklerini ana sayfada görmesin
            $paperQuery->where('user_id', '!=', $userId);
            $guideQuery->where('user_id', '!=', $userId); // Özetler için de geçerli
            
            $user_library_ids = UserLibrary::where('user_id', $userId)->pluck('quiz_id');
            $quizQuery->where('user_id', '!=', $userId)->whereNotIn('id', $user_library_ids);
        }

        // --- 5. ARAMA FİLTRESİ ---
        if ($request->filled('q')) {
            $search = $request->get('q');
            
            // Kağıtlarda Ara
            $paperQuery->where(function($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")->orWhere('description', 'ilike', "%{$search}%");
            });

            // Özetlerde Ara
            $guideQuery->where(function($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")->orWhere('description', 'ilike', "%{$search}%");
            });

            // Quizlerde Ara
            $quizQuery->where(function($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        // --- 6. KATEGORİ FİLTRESİ ---
        if ($request->filled('category')) {
            $catId = $request->get('category');
            
            $categoryFilter = function($q) use ($catId) {
                $q->where('categories.id', $catId);
            };

            $paperQuery->whereHas('categories', $categoryFilter);
            $guideQuery->whereHas('categories', $categoryFilter); // Özetler için kategori filtresi
            $quizQuery->whereHas('categories', $categoryFilter);
        }

        // --- 7. SIRALAMA ---
        $sort = $request->get('sort', 'newest');

        if ($sort === 'popular') {
            $paperQuery->orderByDesc('downloads_count'); 
            $guideQuery->orderByDesc('downloads_count'); // Özetler için en çok indirilenler
            $quizQuery->orderByDesc('results_count');    
        } else {
            $paperQuery->latest(); 
            $guideQuery->latest();
            $quizQuery->latest();
        }

        // --- 8. VERİLERİ ÇEK VE SAYFALA ---
        $papers = $paperQuery->paginate(12, ['*'], 'papers_page')
            ->appends(array_merge($request->all(), ['tab' => 'papers']));
            
        $guides = $guideQuery->paginate(12, ['*'], 'guides_page')
            ->appends(array_merge($request->all(), ['tab' => 'guides']));
            
        $quizzes = $quizQuery->paginate(12, ['*'], 'quizzes_page')
            ->appends(array_merge($request->all(), ['tab' => 'quizzes']));

        $categories = Category::orderBy('name')->get();

        // View'a $guides değişkenini de yolluyoruz
        return view("pages.home", compact('papers', 'guides', 'quizzes', 'categories', 'activeTab'));
    }
}
