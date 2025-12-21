@props(['quiz', 'myQuiz' => false])
@use("App\Enums\Difficulty")

@php

    $imgUrl = $quiz->img_url ? asset('storage/' . $quiz->img_url) : 'https://picsum.photos/seed/'.$quiz->id.'/400/200';
    $userAvatar = $quiz->user->avatar_url ? asset('storage/' . $quiz->user->avatar_url) : null;
    
  
    $currentQuestionCount = $quiz->questions_count ?? 0;
    $isDraft = $myQuiz && ($currentQuestionCount < $quiz->number_of_questions);

    $isCompleted = $quiz->pivot->is_completed ?? false;
@endphp

<div class="group relative flex flex-col bg-white rounded-2xl shadow-lg overflow-hidden h-full transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
    

    <div class="relative h-32 overflow-hidden">
        <img src="{{ $imgUrl }}" alt="{{ $quiz->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
        
   
        <div class="absolute top-2 right-2">
            @switch($quiz->difficulty)
                @case('easy') <span class="bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow">KOLAY</span> @break
                @case('medium') <span class="bg-blue-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow">ORTA</span> @break
                @case('hard') <span class="bg-orange-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow">ZOR</span> @break
                @case('expert') <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow">UZMAN</span> @break
            @endswitch
        </div>

 
        @if($isDraft)
            <div class="absolute inset-0 bg-black/60 flex items-center justify-center backdrop-blur-[2px]">
                <span class="text-yellow-400 font-bold text-lg uppercase tracking-widest border-2 border-yellow-400 px-3 py-1 -rotate-12">
                    TASLAK
                </span>
            </div>
        @endif
    </div>

    <div class="p-4 flex flex-col flex-1 justify-between">
        
        <div>
       
            <div class="flex items-center mb-2">
                @if($userAvatar)
                    <img src="{{ $userAvatar }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-gray-100 mr-2">
                @else
                    <i class="fa-solid fa-circle-user text-gray-400 text-xl mr-2"></i>
                @endif
                <span class="text-xs text-gray-500 font-medium truncate">{{ $quiz->user->name }}</span>
            </div>

            <h3 class="font-bold text-gray-900 text-lg leading-tight mb-1 line-clamp-1" title="{{ $quiz->title }}">
                {{ $quiz->title }}
            </h3>
            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide mb-3">{{ $quiz->subject }}</p>

          
            <div class="flex items-center space-x-4 text-xs text-gray-500 font-mono mb-4 border-b border-gray-100 pb-2">
                <span class="flex items-center" title="Soru Sayısı">
                    <i class="fa-solid fa-list-ol mr-1.5 text-gray-400"></i> 
                    @if($isDraft) 
                        <span class="text-yellow-600 font-bold">{{ $currentQuestionCount }}/{{ $quiz->number_of_questions }}</span>
                    @else
                        {{ $quiz->number_of_questions }}
                    @endif
                </span>
                <span class="flex items-center" title="Süre">
                    <i class="fa-regular fa-clock mr-1.5 text-gray-400"></i> {{ $quiz->duration_minutes }} dk
                </span>
            </div>
        </div>

   
        <div class="mt-auto">

            @if($isDraft)
          
                <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100 mb-3 text-center">
                    <p class="text-xs text-yellow-700 font-semibold mb-2">Kurulum tamamlanmadı!</p>
                    <a href="{{ route('quiz.add.questions', $quiz->id) }}" 
                       class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-2 rounded transition shadow-sm">
                        <i class="fa-solid fa-tools mr-1"></i> Tamamla
                    </a>
                </div>

            @elseif($isCompleted)
        
                <div class="bg-green-50 p-2 rounded-lg border border-green-100 mb-3">
                    <div class="flex justify-between items-center text-sm px-1">
                        <span class="text-green-800 font-bold flex items-center">
                            <i class="fa-solid fa-trophy mr-1.5 text-yellow-500"></i> {{ $quiz->pivot->score }} Puan
                        </span>
                        <span class="text-gray-500 text-xs">
                            {{ $quiz->pivot->time_spent }}dk
                        </span>
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-2">
                
                @if(!$isDraft)
                    <a href="{{ route('quiz.show', $quiz) }}" 
                       class="flex-1 text-center py-2 px-3 rounded-lg text-sm font-bold text-white transition shadow-md
                       {{ $isCompleted ? 'bg-gray-700 hover:bg-gray-800' : 'bg-blue-600 hover:bg-blue-700' }}">
                        {{ $isCompleted ? 'Tekrar Dene' : 'Başla' }}
                    </a>
                @endif

   
                <form action="{{ $myQuiz ? route('quiz.delete', $quiz) : route('library.remove', $quiz->pivot->id ?? 0) }}" 
                      method="POST" 
                      class="{{ $isDraft ? 'w-full' : '' }}" 
                      onsubmit="return confirm('Bu quizi silmek istediğine emin misin?');">
                    @csrf
                    @method("DELETE")
                    
                    @if($isDraft)
                         <button type="submit" class="w-full text-center py-2 px-3 rounded-lg text-sm font-bold border border-red-200 text-red-600 hover:bg-red-50 transition">
                            <i class="fa-regular fa-trash-can mr-1"></i> Taslağı Sil
                        </button>
                    @else
                        <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-lg border border-red-200 text-red-500 hover:bg-red-500 hover:text-white transition" title="Kaldır">
                            <i class="fa-solid {{ $myQuiz ? 'fa-trash' : 'fa-xmark' }}"></i>
                        </button>
                    @endif
                </form>

            </div>

        </div>

    </div>
</div>