 @extends("layouts.app")


 @props(['quiz','rankings','current_user_id'])
 @section("content")
 @use("App\Enums\Difficulty")

    @switch($quiz->difficulty)
        @case(Difficulty::Easy)
            @php $diffColor = "bg-blue-400"; @endphp
            @break
        @case(Difficulty::Medium)   
            @php $diffColor = "bg-indigo-800";  @endphp    
            @break  
        @case(Difficulty::Hard)
            @php $diffColor = "bg-rose-700"; @endphp
            @break
        @case(Difficulty::Expert)
             @php $diffColor = "bg-[#d1a806]";  @endphp
            @break 
        @default   
           @php $diffColor = "bg-emerald-600"; @endphp
    @endswitch 
     

<div class="w-full grid grid-cols-1 md:grid-cols-3 gap-12 p-8">
    

    <div class=" w-full md:col-span-2 bg-[#BFBDB0] text-[#1A1B1C] rounded-2xl shadow-sm  shadow-[#BFBDB0]/60 overflow-hidden h-200 flex flex-col  hover:shadow-md  cursor-pointer">
        <!-- Quiz image -->
        <img class="w-full h-64 object-cover" src="{{ $quiz->img_url ?? 'https://picsum.photos/400/200' }}" alt="Quiz Image">

        <!-- İçerik -->
        <div class="p-4 flex-1 flex flex-col justify-between">
            <!-- Üst kısım -->
            <div class="mt-4">   
                <h3 class="text-3xl font-bold">{{ $quiz->title }}</h3>
                <p class="text-2xl font-semibold text-gray-700 mt-2">{{ $quiz->subject }}</p>
                <div class="mt-2">
                    <p class="text-sm font-semibold text-gray-700">{{ $quiz->description }}</p>
                </div>

            </div>

            <div class="p-4">
                <ul class="list-disc text-md font-semibold font-mono text-gray-800 space-y-2">
                    <li>{{ $quiz->number_of_questions }} questions <i class="fa-solid fa-pencil"></i></li>
                    <li>{{ $quiz->number_of_options }} options <i class="fa-solid fa-circle-stop"></i></li>
                    <li>{{ $quiz->duration_minutes }} minutes <i class="fa-regular fa-clock"></i></li>
                    <li><span class='{{$diffColor}} text-white text-xs font-semibold px-3 py-1 rounded-full'>{{ $quiz->difficulty }}</span> difficulty <i class="fa-solid fa-skull"></i></li>
                    <li>{{ $quiz->wrong_to_correct_ratio }}  wrong answers cancel out 1 correct answer <i class="fa-regular fa-circle-xmark"></i></li>
                    <li>{{ $quiz->solvers()->wherePivot("is_completed",true)->count()}} times solved <i class="fa-regular fa-circle-check"></i></li>
                </ul>
            </div>

            <div class="flex row p-2">
                <a href="{{ route("quiz.start",$quiz) }}">
                <button 
                    class="w-32 mt-2 bg-[#41825e] transition-all duration-300 transform font-semibold hover:scale-105 hover:bg-[#357652] text-white p-2 rounded cursor-pointer">
                    Start Quiz
                </button>
                </a>
                 @if (Auth::check())
                    <form action="{{ route('library.add', $quiz) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-20 ml-4 mt-2 bg-[#417582] transition-all duration-300 transform font-semibold hover:scale-105 hover:bg-[#2c606d] text-white p-2 rounded cursor-pointer">
                            Add <i class="fa-solid fa-plus"></i>
                        </button>
                    </form>
                @endif
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <!-- Avatar -->
                    <img src="https://i.pravatar.cc/100" alt="User Avatar" class="w-10 h-10 rounded-full mr-3">
                    <span class="font-semibold text-sm">Created by {{ $quiz->user->name }}</span>
                </div>  
        </div>
        </div>
    </div>
        <x-success_rank_sidebar :quiz="$quiz" :rankings="$rankings" :current_user_id="$current_user_id"/>
</div>

 @endsection