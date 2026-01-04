@props(['quiz', 'myQuiz' => false])
@use("App\Enums\Difficulty")

@php
    $imgUrl = $quiz->img_url ? asset('storage/' . $quiz->img_url) : null;
    $userAvatar = $quiz->user->avatar_url ? asset('storage/' . $quiz->user->avatar_url) : null;
    
    $currentQuestionCount = $quiz->questions_count ?? 0;
    $isDraft = $myQuiz && ($currentQuestionCount < $quiz->number_of_questions);
    $isCompleted = $quiz->pivot->is_completed ?? false;
@endphp

<div class="group relative flex flex-col bg-[#252526] border border-[#3e3e42] rounded-xl overflow-hidden h-full transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 hover:shadow-lg hover:shadow-indigo-900/20">
    
 
    <div class="relative h-36 overflow-hidden">
        
        
        @if($quiz->img_url != null)
            <img src="{{ $imgUrl }}" alt="{{ $quiz->title }}" 
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 {{ $isDraft ? 'opacity-50 grayscale-[0.5]' : '' }}">
        @else
            <div class="w-full h-full bg-gradient-to-br from-[#1e1e1e] to-[#2d2d30] flex flex-col items-center justify-center transition-transform duration-500 group-hover:scale-110">
                <i class="fa-solid fa-book-open text-4xl text-[#3e3e42] group-hover:text-indigo-500 transition-colors"></i>  
            </div>
        @endif

     
        @if($isDraft)
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPgo8cmVjdCB3aWR0aD0iNCIgaGVpZ2h0PSI0IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDUiLz4KPC9zdmc+')] opacity-30"></div>
            <div class="absolute inset-0 border-2 border-yellow-500/50 rounded-t-xl pointer-events-none"></div>
        @endif
        
      
        <div class="absolute top-2 right-2 flex flex-col gap-1 items-end">
            
         
            @if(!$isDraft)
                @switch($quiz->difficulty)
                    @case('easy') <span class="bg-green-600/90 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow backdrop-blur-sm border border-green-500/50">KOLAY</span> @break
                    @case('medium') <span class="bg-blue-600/90 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow backdrop-blur-sm border border-blue-500/50">ORTA</span> @break
                    @case('hard') <span class="bg-orange-600/90 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow backdrop-blur-sm border border-orange-500/50">ZOR</span> @break
                    @case('expert') <span class="bg-red-600/90 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow backdrop-blur-sm border border-red-500/50">UZMAN</span> @break
                @endswitch
            @endif

          
            @if($isDraft)
                <span class="bg-yellow-500 text-black text-[10px] font-black px-2 py-1 rounded shadow-lg flex items-center gap-1 animate-pulse">
                    <i class="fa-solid fa-pen-ruler"></i> TASLAK
                </span>
            @endif
        </div>
    </div>

 
    <div class="p-4 flex flex-col flex-1 justify-between bg-[#252526]">
        
        <div>
            <div class="flex items-center mb-2 opacity-70">
                @if($userAvatar)
                    <img src="{{ $userAvatar }}" class="w-5 h-5 rounded-full object-cover ring-1 ring-gray-500 mr-2">
                @else
                    <i class="fa-solid fa-circle-user text-gray-500 text-lg mr-2"></i>
                @endif
                <span class="text-[10px] text-gray-400 font-medium truncate">{{ $quiz->user->name }}</span>
            </div>

         
            <h3 class="font-bold text-gray-200 text-md leading-tight mb-1 line-clamp-1 group-hover:text-indigo-400 transition-colors" title="{{ $quiz->title }}">
                {{ $quiz->title }}
            </h3>
            
    
            <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wide mb-3 flex items-center gap-1">
                <i class="fa-solid fa-tag text-indigo-500"></i> {{ $quiz->subject }}
            </p>

            <div class="flex items-center space-x-3 text-[10px] text-gray-400 font-mono mb-4 border-b border-[#3e3e42] pb-3">
                <span class="flex items-center bg-[#1e1e1e] px-2 py-1 rounded border border-[#3e3e42]" title="Soru Sayısı">
                    <i class="fa-solid fa-list-ol mr-1.5 text-gray-500"></i> 
                    @if($isDraft) 
                        <span class="text-yellow-500 font-bold">{{ $currentQuestionCount }}</span><span class="text-gray-600">/{{ $quiz->number_of_questions }}</span>
                    @else
                        {{ $quiz->number_of_questions }}
                    @endif
                </span>
                <span class="flex items-center bg-[#1e1e1e] px-2 py-1 rounded border border-[#3e3e42]" title="Süre">
                    <i class="fa-regular fa-clock mr-1.5 text-gray-500"></i> {{ $quiz->duration_minutes }} dk
                </span>
            </div>
        </div>

     
        <div class="mt-auto">

            @if($isDraft)
               
                <div class="grid grid-cols-2 gap-2">
                   
                    <a href="{{ route('quiz.add.questions', $quiz->id) }}" 
                       class="flex items-center justify-center bg-yellow-600/20 hover:bg-yellow-600 border border-yellow-600/50 hover:border-yellow-500 text-yellow-500 hover:text-black text-xs font-bold py-2 rounded transition-all group/btn">
                        <i class="fa-solid fa-pencil mr-1.5 group-hover/btn:rotate-12 transition-transform"></i> Tamamla
                    </a>

                    <form action="{{ route('quiz.delete', $quiz) }}" method="POST" onsubmit="return confirm('Bu taslağı silmek istediğine emin misin?');">
                        @csrf @method("DELETE")
                        <button type="submit" class="w-full flex items-center justify-center bg-red-600/10 hover:bg-red-600 border border-red-600/30 hover:border-red-500 text-red-500 hover:text-white text-xs font-bold py-2 rounded transition-all">
                            <i class="fa-regular fa-trash-can mr-1.5"></i> Sil
                        </button>
                    </form>
                </div>

            @elseif($isCompleted)
                <div class="bg-green-900/20 p-2 rounded border border-green-500/20 mb-3 flex justify-between items-center px-3">
                    <span class="text-green-400 text-xs font-bold flex items-center">
                        <i class="fa-solid fa-trophy mr-1.5 text-yellow-500"></i> {{ $quiz->pivot->score }} Puan
                    </span>
                    <span class="text-gray-500 text-[10px] font-mono">{{ $quiz->pivot->time_spent }}dk</span>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('quiz.show', $quiz) }}" class="flex-1 bg-[#1e1e1e] hover:bg-[#333] text-gray-300 hover:text-white text-xs font-bold py-2 rounded border border-[#3e3e42] transition text-center">
                        Tekrar Dene
                    </a>
                    
                    <form action="{{ $myQuiz ? route('quiz.delete', $quiz) : route('library.remove', $quiz->pivot->id ?? 0) }}" method="POST" onsubmit="return confirm('Silmek istiyor musun?');">
                        @csrf @method("DELETE")
                        <button type="submit" class="w-8 h-full bg-[#1e1e1e] hover:bg-red-900/30 text-gray-500 hover:text-red-500 border border-[#3e3e42] hover:border-red-500/30 rounded flex items-center justify-center transition">
                            <i class="fa-solid {{ $myQuiz ? 'fa-trash' : 'fa-xmark' }}"></i>
                        </button>
                    </form>
                </div>

            @else
              
                <div class="flex gap-2">
                    <a href="{{ route('quiz.show', $quiz) }}" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-2 rounded shadow-lg shadow-indigo-900/20 text-center transition hover:scale-[1.02]">
                        <i class="fa-solid fa-play mr-1"></i> Başla
                    </a>

              
                    <form action="{{ $myQuiz ? route('quiz.delete', $quiz) : route('library.remove', $quiz->pivot->id ?? 0) }}" method="POST" onsubmit="return confirm('Silmek istiyor musun?');">
                        @csrf @method("DELETE")
                        <button type="submit" class="w-8 h-full bg-[#1e1e1e] hover:bg-red-900/30 text-gray-500 hover:text-red-500 border border-[#3e3e42] hover:border-red-500/30 rounded flex items-center justify-center transition">
                            <i class="fa-solid {{ $myQuiz ? 'fa-trash' : 'fa-xmark' }}"></i>
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>