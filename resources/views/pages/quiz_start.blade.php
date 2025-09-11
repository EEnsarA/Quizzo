 @extends("layouts.app")

 @section("content")
 @use("App\Enums\Difficulty")

     

<div x-data="quizPlayer()" x-cloak x-init="init()" class="w-full grid grid-cols-1 md:grid-cols-3 gap-12 p-8">

    <div class="w-full md:col-span-2 bg-[#BFBDB0] text-[#1A1B1C] rounded-2xl shadow-sm  shadow-[#BFBDB0]/60 overflow-hidden h-full flex flex-col  hover:shadow-md p-4">
        @foreach ($quiz->questions as $index => $question)
            <template x-if="active == {{$index}}"> 
                <div class="w-full bg-[#BFBDB0] text-[#1A1B1C] rounded-2xl shadow-sm shadow-[#BFBDB0]/60 overflow-hidden p-8 mb-4">
                    <div class="mb-4">
                        @if ($question->question_title)
                            <div class="text-xl font-bold text-gray-700">Q{{ $index + 1 }}. {{ $question->question_title }}</div>
                        @else
                             <div class="text-xl font-bold text-gray-700">Q{{ $index + 1 }}. Question Title</div>    
                        @endif
                    </div>
                    @if ($question->question_image)
                        <div class="mb-6 rounded-lg overflow-hidden">
                            <img src="{{ asset('storage/' . $question->question_image) }}" alt="Question image" class="w-full h-auto object-cover">
                        </div>
                    @else 
                        <div class="mb-6 rounded-lg overflow-hidden">
                            <img src="{{ 'https://picsum.photos/400/200' }}" alt="Question image" class="w-full h-auto object-cover">
                        </div>
                    @endif
                    <h2 class="text-3xl font-semibold mb-6">

                        <span class="block mt-2"> {{ $question->question_text }}</span>
                    </h2>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($question->answers as $ansIndex => $answer)
                            <li>
                            <button 
                                @click="toggle({{ $question->id }}, {{ $answer->id }})"
                                class="w-full text-left p-4 rounded-xl border-2 transition-all duration-200 cursor-pointer"
                                :class="answers[{{ $question->id }}] == {{ $answer->id }}
                                ?'bg-[#41825e] text-white border-[#41825e]'
                                :'bg-gray-100 text-gray-800 border-gray-300 hover:bg-gray-200 hover:border-gray-400'"
                                >
                                <span class="font-bold">{{ chr(65 + $ansIndex) }})</span> {{ $answer->answer_text }}
                            </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </template>
        @endforeach
        <div class="mt-8  p-4 ml-4 space-x-4">
            <button 
                @click="prev()" 
                class="w-16 bg-gray-700 text-white font-semibold p-1 rounded-xl  hover:bg-gray-600 transition-colors cursor-pointer">
                 <i class="fa-solid fa-angles-left mr-1"></i> 
            </button>
            <button 
                @click="next({{ $quiz->questions->count() - 1 }})" 
                class=" w-16 bg-[#35525a] text-white font-semibold p-1 rounded-xl hover:bg-[#345861] transition-colors cursor-pointer">
                 <i class="fa-solid fa-angles-right ml-1"></i>
            </button>
        </div>
    </div>
    <div>
        <x-quiz_sidebar :quiz="$quiz"/>
    </div>

</div>

 @endsection