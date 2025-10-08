
@props(['quiz','myQuiz' => false])
@use("App\Enums\Difficulty")
<?php

    $difficulty = $quiz->difficulty;
    $img = 'storage/' . $quiz->img_url
?>


<div class="max-w-xs bg-[#BFBDB0] text-[#1A1B1C] rounded-2xl shadow-sm  shadow-[#BFBDB0]/60 overflow-hidden h-130 flex flex-col hover:scale-105 hover:shadow-md transation-all duration-200 ">
        <img class="w-full h-32 object-cover" 
        @if($quiz->img_url)
            src="{{ asset($img)}}"
            alt="{{ $quiz->title }}"
        @else 
            src="{{ 'https://picsum.photos/400/200' }}"
            alt="Quiz Image"
        @endif
        >
        <div class="p-4 flex-1 flex flex-col justify-between">
            
            <div>
                <div class="flex items-center mb-2">
                    <img src="https://i.pravatar.cc/100" alt="User Avatar" class="w-10 h-10 rounded-full mr-3">
                    <span class="font-semibold text-sm">Created by {{ $quiz->user->name }}</span>
                    
                </div>  
                <h3 class="font-semibold text-lg font-mono mb-1">{{ $quiz->title }}</h3>
                <p class="text-sm font-semibold text-gray-700">{{ $quiz->subject }}</p>
            </div>
            <div class="flex row items-center justify-between">
                <p class="text-sm font-semibold font-mono text-gray-700">{{ $quiz->number_of_questions }} questions <i class="fa-solid fa-pencil"></i></p>
                <p class="text-sm font-semibold font-mono text-gray-700">{{ $quiz->duration_minutes }} minutes <i class="fa-regular fa-clock"></i></p>
                        
            </div>
            @if ($quiz->pivot->is_completed)
              
                    <div class="flex flex-col mt-4">
                        <p class="text-lg font-mono font-semibold">Last result</p>
                        <div class="flex flex-row items-center justify-between p-2 rounded-lg border-2 border-gray-600 mt-2 cursor-pointer hover:border-emerald-700">
                            <div class="flex flex-row items-center">
                                <img src="{{$quiz->user->avatar ?? 'https://i.pravatar.cc/100' }}" 
                                                alt="avatar" 
                                                class="w-8 h-8 rounded-full">
                                <span class="font-semibold ml-2">{{ Auth::user()->name }}</span>
                            </div>
                            <div class="flex flex-row items-center text-md font-semibold">
                                <p class= "text-green-700">{{ $quiz->pivot->score }}</p><i class="fa-regular fa-circle-check text-green-700 ml-1 mr-2"></i>
                                <p class="text-gray-800">{{ $quiz->pivot->time_spent }}m</p><i class="fa-regular text-gray-800 fa-clock ml-1"></i> 
                            </div>
                        </div>

                    </div>
            @else
                    <div class="mt-2">
                        <p class="text-gray-800 text-md">No attempts yet</p> 
                    </div>
            @endif

            
            <div class="flex justify-between items-center mt-3">
                
                @if( $quiz->results->count() <= 0)
                    <span class="bg-[#b5b690] text-white text-xs px-3 font-semibold py-2 rounded-full">new</span> 
                @else
                    @switch($quiz->difficulty)
                    @case(Difficulty::Easy)
                    <span class="bg-blue-400 text-white text-xs font-semibold px-3 py-2 rounded-full">{{ $quiz->difficulty }}</span>
                    @break
                    @case(Difficulty::Medium)    
                    <span class="bg-indigo-800 text-white text-xs font-semibold px-3 py-2 rounded-full">{{ $quiz->difficulty }}</span>
                    @break
                    @case(Difficulty::Hard)
                    <span class="bg-rose-700 text-white text-xs px-3 font-semibold py-2 rounded-full">{{ $quiz->difficulty }}</span>
                    @break
                    @case(Difficulty::Expert)
                    <span class="bg-[#d1a806] text-white text-xs px-3 font-semibold py-2 rounded-full">{{ $quiz->difficulty }}</span>
                    @break 
                    @default    
                    <span class="bg-[#b5b690] text-white text-xs px-3 font-semibold py-2 rounded-full">new</span>
                    @endswitch  
                    <span class="text-xs font-semibold text-gray-800">{{ $quiz->results->count()}} times solved</span>
                @endif            
            </div>
    
            <div>          
                
                    @if ($quiz->pivot->is_completed)
                        <div class="mt-4 flex justify-between space-x-2">                      
                            <a href="{{ route("quiz.show",$quiz) }}"
                                class="flex-1 text-center bg-[#417582] font-semibold hover:bg-[#2c606d] text-white p-2 rounded cursor-pointer">
                                Try Again
                            </a>
                            @if($myQuiz == True)
                                <form action="{{ route('quiz.delete', $quiz) }}" method="POST">
                                    @csrf
                                    @method("DELETE")
                                    <button  type="submit"
                                        class="W-14 border-2 border-rose-800 text-rose-800 duration-300  font-semibold  hover:bg-rose-800 hover:text-white p-2 rounded cursor-pointer">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('library.remove', $quiz->pivot->id) }}" method="POST">
                                    @csrf
                                    @method("DELETE")
                                    <button  type="submit"
                                        class="W-14 border-2 border-rose-800 text-rose-800 duration-300  font-semibold  hover:bg-rose-800 hover:text-white p-2 rounded cursor-pointer">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                            </form>
                            @endif
                        </div>
                    @else
                        <div class="mt-4 flex justify-between space-x-2">
                            <a  href="{{ route("quiz.show",$quiz) }}"
                                class="flex-1 text-center bg-[#41825e]  font-semibold  hover:bg-[#357652] text-white p-2 rounded cursor-pointer">
                                Start Quiz
                            </a>
                            @if($myQuiz == True)
                                <form action="{{ route('quiz.delete', $quiz) }}" method="POST">
                                    @csrf
                                    @method("DELETE")
                                    <button  type="submit"
                                        class="W-14 border-2 border-rose-800 text-rose-800 duration-300  font-semibold  hover:bg-rose-800 hover:text-white p-2 rounded cursor-pointer">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('library.remove', $quiz->pivot->id) }}" method="POST">
                                    @csrf
                                    @method("DELETE")
                                    <button  type="submit"
                                        class="W-14 border-2 border-rose-800 text-rose-800 duration-300  font-semibold  hover:bg-rose-800 hover:text-white p-2 rounded cursor-pointer">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            @endif  
                        </div>
                    @endif
                        
            </div>
        </div>
       
</div>
