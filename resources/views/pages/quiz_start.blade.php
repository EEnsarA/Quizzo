@extends("layouts.app")
@props(['quiz','quiz_result','is_new_attempt'])
@section("content")

<div x-data="quizPlayer({
        durationMinutes: {{ $quiz->duration_minutes }},
        startTime: {{ now()->timestamp }},
        isNew: true, 
        checkUrl: '{{ route('quiz.check', ['quiz' => $quiz->id, 'quiz_result' => $quiz_result->id]) }}',
        token: '{{ csrf_token() }}'
    })" x-cloak x-init="init()" 
     class="min-h-screen p-4 md:p-8 font-sans flex justify-center items-start">

   
    <div class="w-full max-w-7xl bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-700">

        
        <div class="bg-gray-800 px-6 py-4 border-b border-gray-700 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition"><i class="fa-solid fa-arrow-left"></i></a>
                <h1 class="text-lg font-bold text-gray-200">{{ $quiz->title }}</h1>
            </div>
            <span class="text-xs font-mono text-gray-400 bg-gray-900 px-2 py-1 rounded">Online Sınav</span>
        </div>

       
        <div class="p-6 md:p-8 grid grid-cols-1 lg:grid-cols-12 gap-8">

         
            <div class="lg:col-span-8 flex flex-col h-full">
                @foreach ($quiz->questions as $index => $question)
                    <template x-if="active == {{$index}}">
                        <div class="flex-1 flex flex-col relative transition-all duration-300"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            
                    
                            <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                                <h2 class="text-lg font-mono font-bold text-blue-400">
                                    Soru {{ $index + 1 }} <span class="text-gray-500 text-sm">/ {{ $quiz->questions->count() }}</span>
                                </h2>
                                @if ($question->points)
                                    <span class="bg-gray-900 border border-gray-700 text-gray-300 text-xs px-2 py-1 rounded">{{ $question->points }} Puan</span>
                                @endif
                            </div>

                      
                            <h1 class="text-2xl md:text-3xl font-bold leading-tight text-white mb-6">{{ $question->question_text }}</h1>

                      
                            @if ($question->img_url)
                                <div class="mb-8 rounded-xl overflow-hidden shadow-lg border border-gray-700">
                                    <img src="{{ asset('storage/' . $question->img_url) }}" class="w-full h-auto max-h-96 object-contain bg-gray-900">
                                </div>
                            @endif

                      
                            <div class="grid grid-cols-1 gap-4 mt-2">
                                @foreach ($question->answers as $ansIndex => $answer)
                                    <button @click="toggle({{ $question->id }}, {{ $answer->id }})"
                                            class="group relative w-full text-left p-5 rounded-xl border transition-all duration-200 flex items-center"
                                            :class="answers[{{ $question->id }}] == {{ $answer->id }} ? 'bg-blue-600/20 border-blue-500' : 'bg-gray-700/50 border-gray-700 hover:bg-gray-700'">
                                        <span class="w-10 h-10 flex items-center justify-center rounded-lg font-bold mr-4 transition-colors"
                                              :class="answers[{{ $question->id }}] == {{ $answer->id }} ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-400'">
                                            {{ chr(65 + $ansIndex) }}
                                        </span>
                                        <span class="text-lg font-medium" :class="answers[{{ $question->id }}] == {{ $answer->id }} ? 'text-white' : 'text-gray-300'">{{ $answer->answer_text }}</span>
                                    </button>
                                @endforeach
                            </div>

                          
                            <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-700">
                                <button @click="prev()" :disabled="active == 0" class="px-6 py-3 rounded-xl font-bold transition-colors" :class="active == 0 ? 'text-gray-600' : 'text-gray-300 bg-gray-700 hover:bg-gray-600'">
                                    <i class="fa-solid fa-arrow-left"></i> Önceki
                                </button>
                                <button @click="next({{ $quiz->questions->count() - 1 }})" x-show="active != {{ $quiz->questions->count() - 1 }}" class="px-6 py-3 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-500">
                                    Sonraki <i class="fa-solid fa-arrow-right"></i>
                                </button>
                                <div x-show="active == {{ $quiz->questions->count() - 1 }}"><span class="text-sm text-gray-500">Son Soru</span></div>
                            </div>
                        </div>
                    </template>
                @endforeach
            </div>

            
            <div class="lg:col-span-4 self-start">
                 <div class="sticky top-0">
                    <x-quiz_sidebar :quiz="$quiz" :quiz_result="$quiz_result"/>
                 </div>
            </div>

        </div>
    </div>
</div>
@endsection