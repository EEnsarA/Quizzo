@extends("layouts.app")

@props(['quiz', 'rankings', 'current_user_id'])

@section("content")

    @use("App\Enums\Difficulty")

    @php
        $diffColor = match ($quiz->difficulty) {
            'easy' => 'text-green-400 border-green-400',
            'medium' => 'text-blue-400 border-blue-400',
            'hard' => 'text-rose-500 border-rose-500',
            'expert' => 'text-yellow-500 border-yellow-500',
            default => 'text-gray-400 border-gray-400',
        };

        $diffBg = match ($quiz->difficulty) {
            'easy' => 'bg-green-400/10',
            'medium' => 'bg-blue-400/10',
            'hard' => 'bg-rose-500/10',
            'expert' => 'bg-yellow-500/10',
            default => 'bg-gray-400/10',
        };

        $imgUrl = $quiz->img_url ? asset('storage/' . $quiz->img_url) : null;
        $authorAvatar = $quiz->user->avatar_url ? asset('storage/' . $quiz->user->avatar_url) : null;
    @endphp

    <div class="min-h-screen w-full p-4 md:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">

            <div class="lg:col-span-2 space-y-6">

                {{-- ANA KART (Gölgeler hafifletildi, köşeler küçüldü) --}}
                <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-700">

                    {{-- KAPAK GÖRSELİ (Yüksekliği azaltıldı: h-80'den h-48/56'ya çekildi) --}}
                    <div class="relative h-48 md:h-56 w-full group">
                        @if($quiz->img_url != null)
                            <img src="{{ $imgUrl }}" alt="{{ $quiz->title }}" class="w-full h-full object-cover">
                        @else
                            <div
                                class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex flex-col items-center justify-center">
                                <i
                                    class="fa-solid fa-book-open text-3xl text-gray-600 mb-2 group-hover:text-gray-500 transition-colors"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent"></div>

                        <div class="absolute bottom-4 left-4 md:left-6">
                            <span
                                class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider shadow mb-2 inline-block">
                                {{ $quiz->subject }}
                            </span>
                            <h1 class="text-2xl md:text-3xl font-extrabold text-white leading-tight drop-shadow">
                                {{ $quiz->title }}
                            </h1>
                        </div>
                    </div>

                    <div class="p-5 md:p-6">

                        {{-- KULLANICI BİLGİSİ (Daha kompakt) --}}
                        <div class="flex items-center justify-between mb-6 border-b border-gray-700 pb-4">
                            <div class="flex items-center gap-3">
                                @if($authorAvatar)
                                    <img src="{{ $authorAvatar }}" class="w-10 h-10 rounded-full border-2 border-gray-600">
                                @else
                                    <i class="fa-solid fa-circle-user text-gray-400 text-4xl"></i>
                                @endif
                                <div>
                                    <p class="text-xs text-gray-400">Oluşturan</p>
                                    <p class="text-base font-bold text-gray-200">{{ $quiz->user->name }}</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="flex items-center justify-end gap-1.5 text-gray-400 text-xs">
                                    <i class="fa-solid fa-users"></i> Çözülme
                                </div>
                                <p class="text-xl font-mono font-bold text-white">
                                    {{ $quiz->solvers()->wherePivot("is_completed", true)->count() }}
                                </p>
                            </div>
                        </div>

                        {{-- AÇIKLAMA --}}
                        @if($quiz->description)
                            <div class="mb-6 p-3 bg-gray-700/30 rounded-lg border-l-4 border-blue-500">
                                <h3 class="text-xs font-bold text-gray-300 mb-1">Açıklama</h3>
                                <p class="text-gray-400 italic text-sm">{{ $quiz->description }}</p>
                            </div>
                        @endif

                        {{-- İSTATİSTİKLER (İkonlar küçültüldü, padding azaltıldı) --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">

                            <div class="bg-gray-700/40 p-3 rounded-xl text-center border border-gray-700">
                                <i class="fa-solid fa-list-ol text-xl text-purple-400 mb-1"></i>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Soru Sayısı</p>
                                <p class="text-lg font-bold text-white">{{ $quiz->number_of_questions }}</p>
                            </div>

                            <div class="bg-gray-700/40 p-3 rounded-xl text-center border border-gray-700">
                                <i class="fa-regular fa-clock text-xl text-blue-400 mb-1"></i>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Süre</p>
                                <p class="text-lg font-bold text-white">{{ $quiz->duration_minutes }} dk</p>
                            </div>

                            <div class="bg-gray-700/40 p-3 rounded-xl text-center border {{ $diffColor }} {{ $diffBg }}">
                                <i class="fa-solid fa-layer-group text-xl mb-1"></i>
                                <p class="text-[10px] opacity-70 uppercase tracking-wide">Zorluk</p>
                                <p class="text-lg font-bold uppercase">{{ $quiz->difficulty }}</p>
                            </div>

                            <div class="bg-gray-700/40 p-3 rounded-xl text-center border border-gray-700">
                                <i class="fa-regular fa-circle-dot text-xl text-pink-400 mb-1"></i>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Seçenek</p>
                                <p class="text-lg font-bold text-white">{{ $quiz->number_of_options }} Şık</p>
                            </div>

                            {{-- Net Kuralı (Daha kompakt ve satır içine yedirildi) --}}
                            <div
                                class="bg-gray-700/40 p-3 rounded-xl text-center border border-gray-700 col-span-2 sm:col-span-4 flex items-center justify-center gap-3">
                                <i class="fa-solid fa-scale-unbalanced text-xl text-orange-400"></i>
                                <div class="text-left flex flex-row items-center gap-2">
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wide">Değerlendirme:</p>
                                    <p class="text-xs font-bold text-white">
                                        @if($quiz->wrong_to_correct_ratio > 0)
                                            <span class="text-orange-400">{{ $quiz->wrong_to_correct_ratio }} Yanlış</span>, 1
                                            Doğruyu Götürür.
                                        @else
                                            <span class="text-green-400">Net Hesabı Yok</span> (Yanlış doğruyu götürmez).
                                        @endif
                                    </p>
                                </div>
                            </div>

                        </div>

                        {{-- 🟢 BAŞLAMA BUTONLARI (MOD SEÇİMİ) 🟢 --}}
                        <div class="flex flex-col gap-3 mt-2 border-t border-gray-700 pt-5">

                            <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Çözüm Modunu Seç
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {{-- MOD 1: SINAV MODU --}}
                                <a href="{{ route('quiz.start', $quiz) }}" class="block">
                                    <button
                                        class="w-full p-3 bg-gray-700/40 hover:bg-gray-700/60 border border-gray-700 hover:border-emerald-500/50 rounded-xl transition-all flex items-center gap-3 text-left group shadow-sm cursor-pointer">
                                        <div
                                            class="w-9 h-9 rounded-lg bg-gray-800 border border-gray-600 flex items-center justify-center flex-shrink-0 text-emerald-500 group-hover:text-emerald-300 transition-colors">
                                            <i class="fa-solid fa-flag-checkered text-sm"></i>
                                        </div>
                                        <div>
                                            <p
                                                class="font-bold text-sm text-gray-200 group-hover:text-white transition-colors">
                                                Sınav Modu</p>
                                            @if(isset($showRankWarning) && $showRankWarning)


                                                <p class="text-[9px] text-orange-300 leading-tight">
                                                    <i
                                                        class="fa-solid fa-circle-exclamation text-orange-400 text-[10px] mt-0.5"></i>
                                                    {{ $rankWarningText }}
                                                </p>
                                            @else
                                                <p class="text-[9px] text-emerald-400 mt-0.5">Süreli ve sıralamaya dahil</p>
                                            @endif
                                        </div>
                                    </button>
                                </a>


                                {{-- MOD 2: ÖĞRENME MODU --}}
                                <a href="{{ route('quiz.start', ['quiz' => $quiz, 'mode' => 'learning']) }}" class="block">
                                    <button
                                        class="w-full p-3 bg-gray-700/40 hover:bg-gray-700/60 border border-gray-700 hover:border-blue-500/50 rounded-xl transition-all flex items-center gap-3 text-left group shadow-sm cursor-pointer">
                                        <div
                                            class="w-9 h-9 rounded-lg bg-gray-800 border border-gray-600 flex items-center justify-center flex-shrink-0 text-blue-400 group-hover:text-blue-300 transition-colors">
                                            <i class="fa-solid fa-brain text-sm"></i>
                                        </div>
                                        <div>
                                            <p
                                                class="font-bold text-sm text-gray-200 group-hover:text-white transition-colors">
                                                Öğrenme Modu</p>
                                            <p class="text-[9px] text-blue-400/80 mt-0.5 flex items-center gap-1">
                                                açıklamalı, süresiz ,sıralamasız
                                            </p>
                                        </div>
                                    </button>
                                </a>

                            </div>

                            {{-- KÜTÜPHANEYE EKLE BUTONU (Daha ince ve zarif) --}}
                            @if (Auth::check() && !Auth::user()->libraryQuizzes->contains($quiz->id) && $quiz->user_id !== Auth::id())
                                <form action="{{ route('library.add', $quiz) }}" method="POST" class="mt-1">
                                    @csrf
                                    <button type="submit"
                                        class="w-full py-2.5 bg-gray-700/20 hover:bg-gray-700/50 text-gray-400 hover:text-white border border-gray-700 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer">
                                        <i class="fa-regular fa-bookmark"></i> Kütüphaneye Ekle
                                    </button>
                                </form>
                            @endif

                        </div>

                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 h-full">
                <x-success_rank_sidebar :quiz="$quiz" :rankings="$rankings" :current_user_id="$current_user_id" />
            </div>

        </div>
    </div>

@endsection