@props(['result'])

<div class="bg-gray-800 rounded-3xl border border-gray-700 shadow-xl overflow-hidden relative">
    
   
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

    <div class="p-6 md:p-8 flex flex-col md:flex-row items-center gap-8 relative z-10">
        

        <div class="flex flex-col items-center text-center md:items-start md:text-left min-w-[180px]">
            <div class="relative mb-3">
                @if($result->user?->avatar_url)
                    <img src="{{ asset('storage/'. $result->user->avatar_url) }}" class="w-20 h-20 rounded-full border-4 border-gray-700 shadow-lg">
                @else
                    <div class="w-20 h-20 rounded-full bg-gray-700 flex items-center justify-center text-4xl text-gray-400 border-4 border-gray-600">
                        <i class="fa-solid fa-user"></i>
                    </div>
                @endif
                <div class="absolute bottom-0 right-0 bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded-full border-2 border-gray-800">
                    #{{ $result->attempt_number }}
                </div>
            </div>
            
            <h2 class="text-xl font-bold text-white">
                {{ $result->user?->name ?? 'Guest-' . substr($result->session_id,0,4) }}
            </h2>
            <p class="text-xs text-gray-400 mt-1">
                {{ $result->created_at->format('d M Y, H:i') }}
            </p>
        </div>

       
        <div class="flex-1 w-full grid grid-cols-2 md:grid-cols-4 gap-4">
            
      
            <div class="bg-emerald-900/20 border border-emerald-500/30 p-4 rounded-2xl text-center">
                <div class="text-2xl font-bold text-emerald-400 mb-1">{{ $result->correct_count }}</div>
                <div class="text-xs font-bold text-emerald-600 uppercase tracking-wide">Doğru</div>
            </div>

       
            <div class="bg-rose-900/20 border border-rose-500/30 p-4 rounded-2xl text-center">
                <div class="text-2xl font-bold text-rose-400 mb-1">{{ $result->wrong_count }}</div>
                <div class="text-xs font-bold text-rose-600 uppercase tracking-wide">Yanlış</div>
            </div>

    
            <div class="bg-gray-700/30 border border-gray-600/30 p-4 rounded-2xl text-center">
                <div class="text-2xl font-bold text-gray-300 mb-1">{{ $result->empty_count }}</div>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wide">Boş</div>
            </div>

        
            <div class="bg-blue-900/20 border border-blue-500/30 p-4 rounded-2xl text-center md:col-span-1 col-span-2">
                <div class="text-3xl font-extrabold text-blue-400 mb-1">{{ $result->net }}</div>
                <div class="text-xs font-bold text-blue-600 uppercase tracking-wide">NET</div>
            </div>
            
        </div>

    </div>

  
    <div class="bg-gray-900/50 px-6 py-3 flex justify-between items-center border-t border-gray-700/50 text-sm">
        <div class="flex items-center text-gray-400">
            <i class="fa-regular fa-clock mr-2 text-blue-400"></i>
            Toplam Süre: <span class="text-white font-mono ml-1">{{ floor($result->time_spent/60) }}dk {{ $result->time_spent % 60 }}sn</span>
        </div>
        

    </div>
</div>