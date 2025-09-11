
@props(['quiz'])
@use("App\Enums\Difficulty")
<?php

    $difficulty = $quiz->difficulty;
    
?>

<div class="max-w-xs bg-[#BFBDB0] text-[#1A1B1C] rounded-2xl shadow-sm  shadow-[#BFBDB0]/60 overflow-hidden h-120 flex flex-col hover:scale-105 hover:shadow-md transation-all duration-200 cursor-pointer">
    <!-- Quiz image -->
    <img class="w-full h-32 object-cover" src="{{ $quiz->img_url ?? 'https://picsum.photos/400/200' }}" alt="Quiz Image">

    <!-- İçerik -->
    <div class="p-4 flex-1 flex flex-col justify-between">
        <!-- Üst kısım -->
        <div>
            <div class="flex items-center mb-2">
                <!-- Avatar -->
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

        <!-- Alt kısım -->
        <div class="flex justify-between items-center mt-3">
            <!-- Badge -->
            @switch($quiz->difficulty)
                @case(Difficulty::Easy)
                    <span class="bg-blue-400 text-white text-xs font-semibold px-3 py-1 rounded-full">{{ $quiz->difficulty }}</span>
                    @break
                @case(Difficulty::Medium)    
                    <span class="bg-indigo-800 text-white text-xs font-semibold px-3 py-1 rounded-full">{{ $quiz->difficulty }}</span>
                    @break
                @case(Difficulty::Hard)
                    <span class="bg-rose-700 text-white text-xs px-3 font-semibold py-1 rounded-full">{{ $quiz->difficulty }}</span>
                    @break
                @case(Difficulty::Expert)
                    <span class="bg-[#d1a806] text-white text-xs px-3 font-semibold py-1 rounded-full">{{ $quiz->difficulty }}</span>
                    @break 
                @default    
                   <span class="bg-emerald-600 text-white text-xs px-3 font-semibold py-1 rounded-full">new</span>
            @endswitch          
            <!-- Kaç kişi çözdü -->
            <span class="text-xs text-gray-800">{{ $quiz->solvers()->wherePivot("is_completed",true)->count()}} times solved</span>
        </div>
        <div>
            <a href="">
                <button 
                    class="w-full mt-2 bg-[#417582] transition-all duration-300 transform font-semibold hover:scale-105 hover:bg-[#2c606d] text-white p-2 rounded cursor-pointer">
                     Add Library
                </button>
            </a>
            <a href="{{ route("quiz.show",$quiz) }}">
            <button 
                class="w-full mt-2 bg-[#41825e] transition-all duration-300 transform font-semibold hover:scale-105 hover:bg-[#357652] text-white p-2 rounded cursor-pointer">
                 Start Quiz
            </button>
            </a>
        </div>
    </div>
</div>