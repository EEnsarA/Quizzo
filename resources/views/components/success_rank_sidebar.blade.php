@props(['quiz', 'rankings', 'current_user_id'])

<div class="w-full bg-gray-800 text-gray-100 rounded-2xl shadow-xl overflow-hidden border border-gray-700 flex flex-col min-h-[600px] max-h-[600px]">
    
   
    <div class="p-5 border-b border-gray-700 bg-gray-900/50">
        <h2 class="text-xl font-bold flex items-center text-emerald-400">
            <i class="fa-solid fa-trophy mr-2 text-yellow-500"></i> Liderlik Tablosu
        </h2>
        <p class="text-xs text-gray-400 mt-1">Bu quizdeki en iyi performanslar.</p>
    </div>

   
    @if(!$rankings->isEmpty())
    <div class="px-5 py-3 bg-gray-800/80">
        <form method="GET" action="{{ route('quiz.show', $quiz->slug) }}" id="filtersForm" class="flex flex-wrap gap-2">
            
           
            <label class="cursor-pointer relative">
                <input type="checkbox" name="filters[]" value="multiple_attempts" 
                       class="peer sr-only"
                       {{ in_array('multiple_attempts', request()->filters ?? []) ? 'checked' : '' }}
                       onchange="document.getElementById('filtersForm').submit()">
                <div class="px-3 py-1 text-xs font-semibold rounded-full border border-gray-600 text-gray-400 transition-all peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-500 hover:bg-gray-700">
                    <i class="fa-solid fa-layer-group mr-1"></i> Çoklu Deneme
                </div>
            </label>

            <label class="cursor-pointer relative">
                <input type="checkbox" name="filters[]" value="best_time" 
                       class="peer sr-only"
                       {{ in_array('best_time', request()->filters ?? []) ? 'checked' : '' }}
                       onchange="document.getElementById('filtersForm').submit()">
                <div class="px-3 py-1 text-xs font-semibold rounded-full border border-gray-600 text-gray-400 transition-all peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-500 hover:bg-gray-700">
                    <i class="fa-solid fa-stopwatch mr-1"></i> En İyi Süre
                </div>
            </label>

        </form>
    </div>
    @endif

  
    <div class="flex-1 overflow-y-auto p-2 space-y-2 custom-scrollbar" style="max-height: 500px;">
        
        @if($rankings->isEmpty())
            <div class="flex flex-col items-center justify-center h-40 text-gray-500">
                <i class="fa-regular fa-clipboard text-3xl mb-2"></i>
                <span class="text-sm">Henüz kimse çözmedi.</span>
                <span class="text-xs">İlk şampiyon sen ol!</span>
            </div>
        @else
            @foreach($rankings as $i => $r)
                @php
                  
                    $rankClass = 'bg-gray-700/50 border-gray-600'; 
                    $icon = '';
                    
                    if ($i == 0) { 
                        $rankClass = 'bg-gradient-to-r from-yellow-900/20 to-gray-800 border-yellow-600/50'; 
                        $icon = '<i class="fa-solid fa-crown text-yellow-400 text-lg"></i>';
                    } elseif ($i == 1) { 
                        $rankClass = 'bg-gradient-to-r from-gray-500/20 to-gray-800 border-gray-400/50'; 
                        $icon = '<i class="fa-solid fa-medal text-gray-300 text-lg"></i>';
                    } elseif ($i == 2) { 
                        $rankClass = 'bg-gradient-to-r from-orange-900/20 to-gray-800 border-orange-600/50'; 
                        $icon = '<i class="fa-solid fa-medal text-orange-400 text-lg"></i>';
                    }

                 
                    $isMe = ($r->user_id == $current_user_id || $r->session_id == $current_user_id);
                    if ($isMe) {
                        $rankClass .= ' ring-2 ring-emerald-500 bg-emerald-900/10';
                    }
                @endphp

                <div class="flex items-center justify-between p-3 rounded-xl border {{ $rankClass }} transition hover:bg-gray-700">
                    
              
                    <div class="flex items-center gap-3">
                        <div class="w-8 text-center font-bold text-gray-400">
                            @if($icon) {!! $icon !!} @else #{{ $i + 1 }} @endif
                        </div>
                        
                        <div class="flex items-center gap-3">
                        
                            @if(!$r->user && $r->session_id)
                                <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center text-white">
                                    <i class="fa-solid fa-user-secret"></i>
                                </div>
                            @else
                                <img src="{{ $r->user->avatar_url ? asset('storage/'.$r->user->avatar_url) : 'https://ui-avatars.com/api/?name='.$r->user->name.'&background=random' }}" 
                                     alt="Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-gray-600">
                            @endif
                            
                  
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-200 {{ $isMe ? 'text-emerald-400' : '' }}">
                                    {{ $r->user?->name ?? 'Guest-' . substr($r->session_id, 0, 4) }}
                                </span>
                                <span class="text-[10px] text-gray-500">{{ $r->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

               
                    <div class="text-right">
                        <div class="font-mono font-bold text-emerald-400 text-lg leading-none">
                            {{ $r->net }} <span class="text-xs">Net</span>
                        </div>
                        <div class="text-xs text-gray-400 font-mono mt-1">
                            {{ floor($r->time_spent/60) }}d {{$r->time_spent % 60}}s
                        </div>
                    </div>

                </div>
            @endforeach
        @endif
    </div>
</div>