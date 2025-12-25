@extends("layouts.app")

@props(['quiz', 'rankings', 'current_user_id'])

@section("content")

@use("App\Enums\Difficulty")

@php
    $diffColor = match($quiz->difficulty) {
        'easy' => 'text-green-400 border-green-400',
        'medium' => 'text-blue-400 border-blue-400',
        'hard' => 'text-rose-500 border-rose-500',
        'expert' => 'text-yellow-500 border-yellow-500',
        default => 'text-gray-400 border-gray-400',
    };
    
    $diffBg = match($quiz->difficulty) {
        'easy' => 'bg-green-400/10',
        'medium' => 'bg-blue-400/10',
        'hard' => 'bg-rose-500/10',
        'expert' => 'bg-yellow-500/10',
        default => 'bg-gray-400/10',
    };

    $imgUrl = $quiz->img_url ? asset('storage/' . $quiz->img_url) : null;
    $authorAvatar = $quiz->user->avatar_url ? asset('storage/' . $quiz->user->avatar_url) : null;
@endphp

<div class="min-h-screen w-full p-4 md:p-8">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

       
        <div class="lg:col-span-2 space-y-6">
   
            <div class="bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-700">
                
                <div class="relative h-64 md:h-80 w-full group">
                    @if($quiz->img_url != null)
                        <img src="{{ $imgUrl }}" alt="{{ $quiz->title }}" class="w-full h-full object-cover ">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex flex-col items-center justify-center">
                            <i class="fa-solid fa-book-open text-4xl text-gray-600 mb-2 group-hover:text-gray-500 transition-colors"></i>  
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
                    
            
                    <div class="absolute bottom-4 left-4 md:left-6">
                        <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-lg mb-2 inline-block">
                            {{ $quiz->subject }}
                        </span>
                        <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight drop-shadow-lg">
                            {{ $quiz->title }}
                        </h1>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    
                    
                    <div class="flex items-start justify-between mb-8 border-b border-gray-700 pb-6">
                        <div class="flex items-center gap-4">
                            @if($authorAvatar)
                                <img src="{{ $authorAvatar }}" class="w-12 h-12 rounded-full border-2 border-gray-600">
                            @else
                                <i class="fa-solid fa-circle-user text-gray-400 text-5xl"></i>
                            @endif
                            <div>
                                <p class="text-sm text-gray-400">Oluşturan</p>
                                <p class="text-lg font-bold text-gray-200">{{ $quiz->user->name }}</p>
                            </div>
                        </div>
                        
                       
                        <div class="text-right">
                             <div class="flex items-center gap-2 text-gray-400 text-sm">
                                <i class="fa-solid fa-users"></i> Çözülme
                             </div>
                             <p class="text-2xl font-mono font-bold text-white">
                                 {{ $quiz->solvers()->wherePivot("is_completed",true)->count() }}
                             </p>
                        </div>
                    </div>

                  
                    @if($quiz->description)
                        <div class="mb-8 p-4 bg-gray-700/30 rounded-xl border-l-4 border-blue-500">
                            <h3 class="text-sm font-bold text-gray-300 mb-1">Açıklama</h3>
                            <p class="text-gray-400 italic">{{ $quiz->description }}</p>
                        </div>
                    @endif

                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                     
                        <div class="bg-gray-700/40 p-4 rounded-xl text-center border border-gray-700">
                            <i class="fa-solid fa-list-ol text-2xl text-purple-400 mb-2"></i>
                            <p class="text-xs text-gray-400 uppercase">Soru Sayısı</p>
                            <p class="text-xl font-bold text-white">{{ $quiz->number_of_questions }}</p>
                        </div>

                        <div class="bg-gray-700/40 p-4 rounded-xl text-center border border-gray-700">
                            <i class="fa-regular fa-clock text-2xl text-blue-400 mb-2"></i>
                            <p class="text-xs text-gray-400 uppercase">Süre</p>
                            <p class="text-xl font-bold text-white">{{ $quiz->duration_minutes }} dk</p>
                        </div>

                  
                        <div class="bg-gray-700/40 p-4 rounded-xl text-center border {{ $diffColor }} {{ $diffBg }}">
                            <i class="fa-solid fa-layer-group text-2xl mb-2"></i>
                            <p class="text-xs opacity-70 uppercase">Zorluk</p>
                            <p class="text-xl font-bold uppercase">{{ $quiz->difficulty }}</p>
                        </div>

                    
                        <div class="bg-gray-700/40 p-4 rounded-xl text-center border border-gray-700">
                            <i class="fa-regular fa-circle-dot text-2xl text-pink-400 mb-2"></i>
                            <p class="text-xs text-gray-400 uppercase">Seçenek</p>
                            <p class="text-xl font-bold text-white">{{ $quiz->number_of_options }} Şık</p>
                        </div>

                        <div class="bg-gray-700/40 p-4 rounded-xl text-center border border-gray-700 col-span-2 md:col-span-2">
                            <div class="flex items-center justify-center gap-4">
                                <i class="fa-solid fa-scale-unbalanced text-3xl text-orange-400"></i>
                                <div class="text-left">
                                    <p class="text-xs text-gray-400 uppercase">Değerlendirme</p>
                                    <p class="text-sm font-bold text-white">
                                        @if($quiz->wrong_to_correct_ratio > 0)
                                            <span class="text-orange-400">{{ $quiz->wrong_to_correct_ratio }} Yanlış</span>, 1 Doğruyu Götürür.
                                        @else
                                            <span class="text-green-400">Net Hesabı Yok</span> (Yanlış doğruyu götürmez).
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                  
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('quiz.start', $quiz) }}" class="flex-1">
                            <button class="w-full py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 text-white rounded-xl font-bold text-lg shadow-lg shadow-green-900/50 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-play"></i> Quize Başla
                            </button>
                        </a>

                        @if (Auth::check())
                            <form action="{{ route('library.add', $quiz) }}" method="POST" class="sm:w-auto">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-gray-700 hover:bg-gray-600 text-blue-300 border border-gray-600 rounded-xl font-bold text-lg shadow-lg transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-bookmark"></i>
                                    <span class="hidden sm:inline">Kütüphaneye Ekle</span>
                                    <span class="sm:hidden">Ekle</span>
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>

     
        <div class="lg:col-span-1 h-full">
            <x-success_rank_sidebar :quiz="$quiz" :rankings="$rankings" :current_user_id="$current_user_id"/>
        </div>

    </div>
</div>

@endsection