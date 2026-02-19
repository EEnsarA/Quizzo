@props(['quiz', 'myQuiz' => false])

@php
    $imgUrl = $quiz->img_url ? asset('storage/' . $quiz->img_url) : null;
    $userAvatar = ($quiz->user && $quiz->user->avatar_url) ? asset('storage/' . $quiz->user->avatar_url) : null;
@endphp

{{-- RENK GÜNCELLEMESİ: bg-[#2d2d30] yaptık (daha açık gri) --}}
<div class="group relative flex flex-col bg-[#2d2d30] border border-gray-700/50 rounded-xl shadow-lg overflow-hidden h-full transition-all duration-300 hover:-translate-y-1 hover:shadow-blue-900/10 hover:border-blue-500/30">
    
    {{-- RESİM ALANI --}}
    <div class="relative h-40 overflow-hidden">
        @if($imgUrl)
            <img src="{{ $imgUrl }}" alt="{{ $quiz->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
        @else
            <div class="w-full h-full bg-gradient-to-br from-gray-700 to-gray-800 flex flex-col items-center justify-center transition-transform duration-500 group-hover:scale-110">
                <i class="fa-solid fa-layer-group text-4xl text-gray-500 mb-2 group-hover:text-gray-400 transition-colors"></i>  
            </div>
        @endif
        
        {{-- Zorluk Badge --}}
        <div class="absolute top-2 right-2">
            @switch($quiz->difficulty->value)
                @case('easy') <span class="bg-emerald-500/90 text-white text-[10px] font-bold px-2 py-1 rounded shadow backdrop-blur-sm">KOLAY</span> @break
                @case('medium') <span class="bg-blue-500/90 text-white text-[10px] font-bold px-2 py-1 rounded shadow backdrop-blur-sm">ORTA</span> @break
                @case('hard') <span class="bg-orange-500/90 text-white text-[10px] font-bold px-2 py-1 rounded shadow backdrop-blur-sm">ZOR</span> @break
                @case('expert') <span class="bg-red-600/90 text-white text-[10px] font-bold px-2 py-1 rounded shadow backdrop-blur-sm">UZMAN</span> @break
            @endswitch
        </div>
    </div>

    {{-- İÇERİK ALANI --}}
    <div class="p-4 flex flex-col flex-1 justify-between">
        <div>
            <h3 class="text-white font-bold text-lg mb-1 truncate leading-tight group-hover:text-blue-400 transition-colors" title="{{ $quiz->title }}">
                {{ $quiz->title }}
            </h3>
            
            <div class="flex items-center gap-2 mb-3">
                @if($userAvatar)
                    <img src="{{ $userAvatar }}" class="w-5 h-5 rounded-full object-cover ring-1 ring-gray-500">
                @else
                    <i class="fa-regular fa-circle-user text-gray-400 text-sm"></i>
                @endif
                <span class="text-xs text-gray-400 font-medium truncate">{{ $quiz->user->name }}</span>
            </div>

            <div class="flex items-center gap-3 text-xs text-gray-500 mb-4 border-t border-gray-700 pt-2">
                <span class="flex items-center gap-1"><i class="fa-regular fa-circle-question"></i> {{ $quiz->questions_count ?? 0 }} Soru</span>
                <span class="flex items-center gap-1"><i class="fa-regular fa-clock"></i> {{ $quiz->duration_minutes }} Dk</span>
            </div>
        </div>

        {{-- BUTONLAR --}}
        <div class="mt-auto flex gap-2">
            @if($myQuiz)
                {{-- DÜZENLE BUTONU (Sadece Kendi Quizimse) --}}
                <a href="{{ route('quiz.edit', $quiz) }}" class="flex-1 bg-blue-600/10 hover:bg-blue-600 text-blue-400 hover:text-white border border-blue-600/30 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i> Düzenle
                </a>
                
                {{-- Silme Butonu (Form ile) --}}
                <form action="{{ route('quiz.delete', $quiz->id) }}" method="POST" onsubmit="return confirm('Silmek istediğine emin misin?')" class="w-10">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full h-full bg-red-600/10 hover:bg-red-600 text-red-400 hover:text-white border border-red-600/30 rounded-lg flex items-center justify-center transition">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            @else
                <a href="{{ route('quiz.start', $quiz->id) }}" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-900/20">
                    <i class="fa-solid fa-play"></i> Başla
                </a>
            @endif
        </div>
    </div>
</div>