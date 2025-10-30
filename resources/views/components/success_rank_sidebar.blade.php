
@props(['quiz', 'rankings', 'current_user_id'])

<div class="w-full bg-gray-200 text-[#1A1B1C]  rounded-2xl shadow-sm shadow-gray-400/60 p-4 flex flex-col justify-between transation-all duration-200 hover:scale-105">
    <div class="mt-4  flex flex-col">
        <h2 class="text-2xl font-extrabold  text-[#2d626f]">Success Chart</h2>

        <!-- Question list container with scroll and buttons -->
        <ul class="space-y-2 mt-4">
                @if($rankings->isEmpty())
                    <h2 class="mt-4 font-semibold text-md">Henüz çözüm yok.</h2>
                @else
                <div class="flex space-x-4 mb-4 mt-2">
                    <form method="GET" action="{{ route('quiz.show', $quiz->slug) }}" class="space-x-4 mb-4" id="filtersForm">
                        <label>
                            <input type="checkbox" name="filters[]" value="multiple_attempts"
                                {{ in_array('multiple_attempts', request()->filters ?? []) ? 'checked' : '' }}
                                onchange="document.getElementById('filtersForm').submit()">
                            Multiple Attempts
                        </label>

                        <label>
                            <input type="checkbox" name="filters[]" value="best_time"
                                {{ in_array('best_time', request()->filters ?? []) ? 'checked' : '' }}
                                onchange="document.getElementById('filtersForm').submit()">
                            Best Time
                        </label>
                    </form>
                </div>    
                @endif
                <div class="w-full h-90 overflow-y-auto space-y-4 pr-2">
                    @foreach($rankings as $i => $r)
                    <li class="flex items-center  bg-gray-100 p-3 rounded-lg hover:bg-gray-400 cursor-pointer
                    @if($r->user_id == $current_user_id || $r->session_id == $current_user_id) border-2 border-emerald-700 @endif

                    ">
                        <div class="flex row text-lg items-center">
                            @if($i == 0)
                                <span class="font-bold text-lg ">#{{ $i + 1 }}</span>
                                <i class="fa-solid fa-trophy text-[#FFD700] ml-2"></i>
                            @elseif($i == 1)
                                <span class="font-bold text-lg ">#{{ $i + 1 }}</span>
                                <i class="fa-solid fa-award text-[#C0C0C0] ml-2"></i>
                            @elseif($i == 2)
                                <span class="font-bold text-lg ">#{{ $i + 1 }}</span>
                                <i class="fa-solid fa-medal text-[#cd7f32] ml-2"></i>
                            @else
                                <span class="font-bold text-lg ">#{{ $i + 1 }}</span>
                            @endif

                        </div>
                        <div class="ml-2 w-full flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                @if(!$r->user && $r->session_id)
                                    <i class="fa-solid fa-circle-user text-[28px]"></i>    
                                    <span class="font-semibold">{{'Guest' . substr($r->session_id, 0, 4) }}</span>
                                @else
                                    <img src="{{$r->user?->avatar_url  ?? 'https://i.pravatar.cc/100' }}" 
                                        alt="avatar" 
                                        class="w-8 h-8 rounded-full">
                                    <span class="font-semibold">{{ $r->user?->name ?? 'Guest' . substr($r->session_id, 0, 4) }}</span>
                                @endif
                            </div>
                            <div class="flex row text-md font-semibold text-gray-800">
                                <div>
                                    <span class="text-green-700">{{ $r->net }}</span><i class="fa-regular fa-circle-check text-green-700 ml-1"></i> 
                                </div>
                                <div class="ml-4">
                                    <span class="text-gray-800">{{ floor($r->time_spent/60) }}m {{$r->time_spent % 60}}s</span><i class="fa-regular text-gray-800 fa-clock ml-1"></i>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </div>
        </ul>
    </div>
    
</div>
