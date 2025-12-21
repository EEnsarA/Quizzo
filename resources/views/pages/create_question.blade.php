@extends("layouts.app")

@props(['quiz'])


@section("content")

    <div class="max-w-3xl mx-auto mt-10 p-6 text-[#F2EDE4] shadow-md rounded-lg" x-data="questionCreate({ quizId: {{ $quiz->id }},number_of_questions: {{ $quiz->number_of_questions }},number_of_options : {{$quiz->number_of_options}} })">
            <div class="px-4 py-2 rounded-lg mb-4">
                <h1 class="text-3xl font-extrabold mb-6 text-center text-[#F2EDE4]">Quiz {{$quiz->title}}</h1>
                <div class="flex flex-row justify-center">
                    <ul class="list-disc font-bold text-[#F2EDE4] tracking-wider space-y-2 mr-20">
                        <li class=" ">{{ $quiz->number_of_questions }} questions <i class="fa-solid fa-pencil"></i></li>
                        <li class="">{{ $quiz->duration_minutes }} minutes <i class="fa-regular fa-clock"></i></li>
                    </ul>
                    <ul class=" list-disc font-bold tracking-wider space-y-2 text-[#F2EDE4]">
                    <li class=" ">{{ $quiz->number_of_options }} options <i class="fa-solid fa-circle-stop"></i></li>
                    <li class=" ">{{ $quiz->difficulty }}  <i class="fa-solid fa-gears"></i></li>
                    </ul>
                </div>
            </div>
            <!-- Pagination -->    
            <div class="w-full justify-center mb-6">
                <div class=" w-full h-8  overflow-y-auto space-x-2">
                    <template x-for="(q, idx) in questions" :key="idx">
                        <button type="button"
                            @click="goToQuestion(idx)"
                            class="w-8 h-8 rounded-full text-sm font-bold cursor-pointer"
                            :class="idx === current_q_index 
                                ? 'bg-green-600 text-white' 
                                : 'bg-gray-600 text-gray-200 hover:bg-gray-500'"
                            x-text="idx+1">
                        </button>
                    </template>
                </div>
            </div>

            <hr class="border-dashed">
        
        
            <div class="space-y-6">
                    <div>
                        <label class="block text-2xl font-extrabold tracking-wider mb-2">Q <span x-text="current_q_index + 1"></span></label>

                    </div>

                    <div>
                        <label for="title" class="block font-bold tracking-wider mb-2">Question Title</label>
                        <input type="text"
                            class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-green-300"
                            placeholder="Örn: 'Python Programlama Temelleri'"
                            x-model="questions[current_q_index].title">
                        <template x-if="hasError('title')">
                            <p class="text-red-500 text-sm mt-2" x-text="getError('title')"></p>
                        </template>   
                    </div>

                    <div>
                        <label for="content" class="block font-bold tracking-wider mb-2">Question Content</label>
                        <textarea rows="3"
                                class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-green-300"
                                placeholder="Bu quiz hakkında kısa bilgi..."
                                x-model="questions[current_q_index].content"
                                ></textarea>
                        <template x-if="hasError('content')">
                            <p class="text-red-500 text-sm mt-2" x-text="getError('content')"></p>
                        </template>   
                    </div>

                    <div>
                        <label for="img_url" class="block font-bold tracking-wider mb-2">Question Image</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file" 
                                class="flex flex-col items-center justify-center w-full h-32 
                                        border-2 border-gray-300 border-dashed rounded-lg cursor-pointer 
                                        bg-[#1A1B1C] hover:bg-[#242527]">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500">SVG, PNG, or JPG (MAX. 800x400px)</p>
                                </div>
                                <input id="dropzone-file" type="file" name="img_url" accept="image/*" class="hidden"
                                    @change="setFile($event)"
                                />
                            </label>
           
                        </div>
                    </div>
                    <div x-show="questions[current_q_index].fileName">
                        <label class="block font-bold tracking-wider mb-2">Image to be added</label>
                        <div class="flex flex-col mt-3 space-y-2 justify-center  border-2 border-dashed rounded-lg px-4 py-2">
                                    <img :src="questions[current_q_index].fileUrl" alt="preview" class="h-32 object-contain rounded-md">
                                    <p class="text-sm text-gray-300">
                                        Seçilen dosya: <span class="font-medium text-gray-200" x-text="questions[current_q_index].fileName"></span>
                                    </p>
                        </div>
                    </div>

                    <div>
                        <label for="point" class="block  font-bold tracking-wider mb-2">Question Point</label>
                        <input type="number" 
                            class="w-full px-4 py-2 border rounded-lg"
                            min="1" max="5"
                            x-model= "questions[current_q_index].point"
                            
                            >
                        <template x-if="hasError('points')">
                            <p class="text-red-500 text-sm mt-2" x-text="getError('points')"></p>
                        </template>  
                    </div>

                    {{-- ANSWERS --}}
                    <div class="mt-4">

                        
                        @for ($i = 0; $i < $quiz->number_of_options; $i++)
                            
                            <div>
                                <label class="block text-2xl font-extrabold tracking-wider mb-2">A <span>{{$i+1}}</span></label>
                            </div>

                            <label for="a_text" class="block font-bold tracking-wider mb-2">Answer Content</label>
                            <div class="flex flex-row space-x-3 mb-4">
                                <textarea name="questions[${current_q_index}][answers][{{$i}}][answer_content]" rows="2"
                                        class="flex-2 px-4 py-2 border rounded-lg focus:ring focus:ring-green-300"
                                        placeholder="Answer Content .."
                                        x-model= "questions[current_q_index].answers[{{$i}}].answer_content"
                                        ></textarea> 
                                <div class="flex flex-1 items-center justify-center ps-2 border rounded-lg">
                                    
                                    <input id="is_correct_{{$i+1}}" type="checkbox"
                                        name="questions[${current_q_index}][answers][{{$i}}][is_correct]"
                                        x-model= "questions[current_q_index].answers[{{$i}}].is_correct"
                                    
                                        class="ml-2  text-blue-600 bg-gray-100 border-gray-300 rounded-sm">
                                    <label for="is_correct_{{$i+1}}" class=" ml-2 tracking-wide font-bold text-gray-900 dark:text-gray-300">Is Correct ?</label>

                                </div>

                            </div>
                            <template x-if="hasError('answers.{{$i}}.answer_content')">
                                <p class="text-red-500 text-sm mt-2" x-text="getError('answers.{{$i}}.answer_content')"></p>
                            </template>  
                        @endfor

                    </div>

                         
                    <div class="flex justify-between mt-6">
                        <button type="button" 
                                @click="prevQuestion" 
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg cursor-pointer"
                                :disabled="current_q_index === 0">
                            Prev
                        </button>

                        <button type="button" 
                                @click="nextQuestion" 
                                class="px-4 py-2 bg-gray-700 text-white rounded-lg cursor-pointer"
                                :disabled="current_q_index === questions.length-1">
                            Next
                        </button>
                    </div>

                    <div class="mt-2 flex  justify-between space-x-4">

                        <button @click="submitForm('{{ route('questions.add') }}','{{ csrf_token() }}')" 
                            class="flex-2 py-3 rounded-lg text-white font-bold   tracking-wider transition-all duration-300 transform bg-[#41825e] hover:bg-[#357652] hover:scale-103 shadow-lg cursor-pointer">
                            Save Question
                        </button>
                        <button 
                            class="flex-1 w-52  border-2 py-3 rounded-lg  font-bold transition-all duration-300 transform border-[#417582] text-[#417582]  hover:bg-[#2c606d] hover:text-white hover:scale-105 shadow-lg cursor-pointer">
                            Next Question </i>
                        </button>
                    </div>
            </div>
    </>
@endsection