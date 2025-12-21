@props(['quiz'])

@php
  
    $imgUrl = $quiz->img_url ? asset('storage/' . $quiz->img_url) : 'https://picsum.photos/seed/'.$quiz->id.'/400/200';
    $userAvatar = $quiz->user->avatar_url ? asset('storage/' . $quiz->user->avatar_url) : null;
    $solvedCount = $quiz->results->count();
@endphp

<div class="group relative flex flex-col bg-white rounded-2xl shadow-lg overflow-hidden h-full transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
   
    <div class="relative h-40 overflow-hidden">
        <img src="{{ $imgUrl }}" alt="{{ $quiz->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
        
        <div class="absolute top-2 right-2">
            @switch($quiz->difficulty)
                @case('easy') <span class="bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow">KOLAY</span> @break
                @case('medium') <span class="bg-blue-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow">ORTA</span> @break
                @case('hard') <span class="bg-orange-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow">ZOR</span> @break
                @case('expert') <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow">UZMAN</span> @break
            @endswitch
        </div>

      
        @if($solvedCount > 0)
            <div class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-sm text-white text-[10px] px-2 py-1 rounded flex items-center">
                <i class="fa-solid fa-users mr-1"></i> {{ $solvedCount }} kez çözüldü
            </div>
        @else
            <div class="absolute bottom-2 left-2 bg-yellow-500 text-white text-[10px] px-2 py-1 rounded shadow font-bold">
                YENİ
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
                <span class="text-xs text-gray-500 font-medium truncate">Created by {{ $quiz->user->name }}</span>
            </div>

            <h3 class="font-bold text-gray-900 text-lg leading-tight mb-1 line-clamp-2 h-14" title="{{ $quiz->title }}">
                {{ $quiz->title }}
            </h3>
            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide mb-3">{{ $quiz->subject }}</p>

           
            <div class="flex items-center space-x-4 text-xs text-gray-500 font-mono mb-4 border-b border-gray-100 pb-2">
                <span class="flex items-center" title="Soru Sayısı">
                    <i class="fa-solid fa-list-ol mr-1.5 text-gray-400"></i> {{ $quiz->number_of_questions }} Soru
                </span>
                <span class="flex items-center" title="Süre">
                    <i class="fa-regular fa-clock mr-1.5 text-gray-400"></i> {{ $quiz->duration_minutes }} dk
                </span>
            </div>
        </div>

  
        <div class="flex items-center gap-2 mt-auto">
            
         
            <a href="{{ route('quiz.show', $quiz) }}" 
               class="flex-1 text-center py-2 px-3 rounded-lg text-sm font-bold text-white bg-gray-800 hover:bg-gray-700 transition shadow-md flex items-center justify-center group-hover:bg-blue-600">
                <i class="fa-solid fa-play mr-2 text-xs"></i> Başla
            </a>

           
            @if (Auth::check())
                <form action="{{ route('library.add', $quiz) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 text-gray-500 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition" 
                            title="Kütüphaneme Ekle">
                        <i class="fa-solid fa-bookmark"></i>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 text-gray-400 hover:text-gray-600">
                     <i class="fa-regular fa-bookmark"></i>
                </a>
            @endif

        </div>

    </div>
</div>