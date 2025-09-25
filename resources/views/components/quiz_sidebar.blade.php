@props(['quiz','quiz_result'])

<div class="w-full h-180 bg-[#BFBDB0] text-[#1A1B1C] rounded-2xl shadow-sm shadow-[#BFBDB0]/60 p-4 flex flex-col justify-between transation-all duration-200 hover:scale-105">
        <div class="mt-4 flex-1 flex flex-col">
                <h2 class="text-xl font-bold font-mono text-gray-800 mb-4">Questions</h2>   
                    <div class="flex flex-col items-center mt-2 ">
                        <button class="p-1 mb-1 text-gray-700 hover:text-gray-900 transition-colors cursor-pointer">
                            <i class="fa-solid fa-chevron-up"></i>
                        </button>
                         <div class="w-full h-80 overflow-y-auto space-y-2 pr-2">   
                            @foreach ($quiz->questions as $index => $question)
                                <button
                                    @click="active = {{$index}}" 
                            
                                    class="block w-full text-left font-semibold p-3 rounded-xl hover:bg-gray-300 transition-colors cursor-pointer"
                                    :class="active == {{ $index }}  ? 'bg-[#41825e] text-white' : 'bg-gray-200 text-gray-800'"
                                    >
                                    Question {{ $index + 1 }}  
                                </button>
                            @endforeach
                        </div>
                        <button class="p-1 mt-1 text-gray-700 hover:text-gray-900 transition-colors cursor-pointer">
                            <i class="fa-solid fa-chevron-down"></i>    
                        </button>
                    </div>
        </div>

                        <div class="mt-4 flex justify-between space-x-2">
                            <button
                                @click="prev()"
                                class="flex-1 bg-gray-700 text-white font-semibold p-2   rounded-lg hover:bg-gray-600 transition-colors cursor-pointer">
                                Previous
                            </button>
                            <button
                                @click="next({{ $quiz->questions->count() - 1 }})"
                                class="flex-1 bg-[#417582] text-white font-semibold p-2 rounded-lg hover:bg-[#2c606d] transition-colors cursor-pointer">
                                Next
                            </button>
                        </div>
                        <div class="mt-8 flex ">
                            <button
                                @click="submitQuiz('{{ route('quiz.check',['quiz' => $quiz , 'quiz_result' => $quiz_result->id]) }}','{{ csrf_token() }}')"
                                class="flex-1 bg-[#41825e] text-white font-semibold p-2   rounded-lg hover:bg-[#357652] transition-colors cursor-pointer">
                                Finish Quiz
                            </button>
                        </div>
        
</div>