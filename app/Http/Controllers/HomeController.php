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
        $activeTab = $request->get('tab', 'papers');

        // --- 1. SINAV KAĞITLARI SORGUSU ---
        $paperQuery = ExamPaper::with(['categories', 'user'])->where('is_public', true);

        // --- 2. ONLINE QUIZLER SORGUSU ---
        // categories ilişkisini de (with) çekiyoruz ki N+1 sorunu olmasın
        $quizQuery = Quiz::with(['user', 'results', 'categories'])
            ->withCount(['questions', 'results']); 

        // --- 3. KULLANICI FİLTRESİ ---
        if (Auth::check()) {
            $userId = Auth::id();
            $paperQuery->where('user_id', '!=', $userId);
            
            $user_library_ids = UserLibrary::where('user_id', $userId)->pluck('quiz_id');
            $quizQuery->where('user_id', '!=', $userId)->whereNotIn('id', $user_library_ids);
        }

        // --- 4. ARAMA FİLTRESİ (BÜYÜK/KÜÇÜK HARF DUYARSIZ - POSTGRESQL) ---
        if ($request->filled('q')) {
            $search = $request->get('q');
            
            // PostgreSQL için 'ILIKE' kullanılır. Eğer MySQL kullansaydın 'LIKE' yeterliydi.
            
            // Kağıtlarda Ara
            $paperQuery->where(function($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });

            // Quizlerde Ara
            $quizQuery->where(function($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        // --- 5. KATEGORİ FİLTRESİ (HEM PAPER HEM QUIZ İÇİN) ---
        if ($request->filled('category')) {
            $catId = $request->get('category');
            
            // Paper Filtrele
            $paperQuery->whereHas('categories', function($q) use ($catId) {
                $q->where('categories.id', $catId);
            });

            // Quiz Filtrele (Artık çalışır çünkü pivot tabloyu kurduk)
            $quizQuery->whereHas('categories', function($q) use ($catId) {
                $q->where('categories.id', $catId);
            });
        }

        // --- 6. SIRALAMA ---
        $sort = $request->get('sort', 'newest');

        if ($sort === 'popular') {
            $paperQuery->orderByDesc('downloads_count'); 
            $quizQuery->orderByDesc('results_count');    
        } else {
            $paperQuery->latest(); 
            $quizQuery->latest();
        }

        // --- 7. VERİLERİ ÇEK ---
        // append() ile filtreleri sayfalama linklerine ekliyoruz.
        // Ayrıca 'tab' bilgisini de ekliyoruz ki 2. sayfaya geçince sekme değişmesin.
        $papers = $paperQuery->paginate(12, ['*'], 'papers_page')
            ->appends(array_merge($request->all(), ['tab' => 'papers']));
            
        $quizzes = $quizQuery->paginate(12, ['*'], 'quizzes_page')
            ->appends(array_merge($request->all(), ['tab' => 'quizzes']));

        $categories = Category::orderBy('name')->get();

        return view("pages.home", compact('papers', 'quizzes', 'categories', 'activeTab'));
    }
}
