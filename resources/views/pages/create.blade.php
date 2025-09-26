
@extends("layouts.app")

@section("content")


<div class="max-w-3xl mx-auto mt-10 p-6 text-[#F2EDE4]  shadow-md rounded-lg"
 x-data="quizCreate({ errors: {{ Js::from($errors->getMessages()) }} })">

    <h1 class="text-3xl font-extrabold mb-6 text-center text-[#F2EDE4]">Yeni Quiz Oluştur</h1>

    <form action="{{ route("quiz.add") }}" method="POST" enctype="multipart/form-data" class="space-y-6" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="title" class="block font-bold tracking-wider mb-2">Quiz Başlığı</label>
            <input type="text" name="title" id="title" 
                   class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-green-300"
                   placeholder="Örn: 'Python Programlama Temelleri'"
                   value="{{ old('title') }}" required>
            <template x-if="hasError('title')">
                <p class="text-red-500 text-sm mt-2" x-text="getError('title')"></p>
            </template>   
   
        </div>


        <div>
            <label for="subject" class="block font-bold tracking-wider mb-2">Konu</label>
            <input type="text" name="subject" id="subject" 
                   class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-green-300"
                   placeholder="Örn: Tarih, Matematik, Teknoloji"
                   value="{{ old('subject') }}">
            <template x-if="hasError('subject')">
                <p class="text-red-500 text-sm mt-2" x-text="getError('subject')"></p>
            </template>   
        </div>

     
        <div>
            <label for="description" class="block font-bold tracking-wider mb-2">Açıklama</label>
            <textarea name="description" id="description" rows="3"
                      class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-green-300"
                      placeholder="Bu quiz hakkında kısa bilgi...">{{ old('description') }}</textarea>
            <template x-if="hasError('description')">
                <p class="text-red-500 text-sm mt-2" x-text="getError('description')"></p>
            </template>   
        </div>

       
        <div>
            <label for="img_url" class="block font-bold tracking-wider mb-2">Quiz Görseli</label>
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
                        @change= "fileName = $event.target.files[0]?.name; fileUrl = URL.createObjectURL($event.target.files[0])"
                    />
                </label>
                 <template x-if="hasError('img_url')">
                    <p class="text-red-500 text-sm mt-2" x-text="getError('img_url')"></p>
                </template>  
            </div>
        </div>
        <div x-show="fileName">
            <label class="block font-bold tracking-wider mb-2">Eklenecek Görsel</label>
            <div class="flex flex-col mt-3 space-y-2 justify-center  border-2 border-dashed rounded-lg px-4 py-2">
                        <img :src="fileUrl" alt="preview" class="h-32 object-contain rounded-md">
                        <p class="text-sm text-gray-300">
                            Seçilen dosya: <span class="font-medium text-gray-200" x-text="fileName"></span>
                        </p>
            </div>
        </div>     
       
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label for="number_of_questions" class="block  font-bold tracking-wider mb-2">Soru Sayısı</label>
                <input type="number" name="number_of_questions" id="number_of_questions" 
                       class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-green-300"
                       min="4" max="20" value="{{ old('number_of_questions', 5) }}">
                <template x-if="hasError('number_of_questions')">
                    <p class="text-red-500 text-sm mt-2" x-text="getError('number_of_questions')"></p>
                </template>  
            </div>
            <div>
                <label for="number_of_options" class="block  font-bold tracking-wider mb-2">Seçenek Sayısı</label>
                <input type="number" name="number_of_options" id="number_of_options" 
                       class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-green-300"
                       min="2" max="6" value="{{ old('number_of_options', 4) }}">
                <template x-if="hasError('number_of_options')">
                    <p class="text-red-500 text-sm mt-2" x-text="getError('number_of_options')"></p>
                </template>  
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label for="difficulty" class="block  font-bold tracking-wider mb-2">Zorluk Seviyesi</label>
                <select name="difficulty" id="difficulty" class="w-full py-2 px-1 border font-semibold rounded-lg bg-[#1A1B1C] focus:ring-blue-500 cursor-pointer">
                    <option selected>Choose a difficulty</option>
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                    <option value="expert">Expert</option>
                </select>
                <template x-if="hasError('difficulty')">
                    <p class="text-red-500 text-sm mt-2" x-text="getError('difficulty')"></p>
                </template>  
            </div>
            <div>
                <label for="duration_minutes" class="block  font-bold tracking-wider mb-2">Süre (dk)</label>
                <input type="number" name="duration_minutes" id="duration_minutes"
                       class="w-full px-4 py-2 border rounded-lg"
                       min="1" max="120" value="{{ old('duration_minutes', 5) }}">
                <template x-if="hasError('duration_minutes')">
                    <p class="text-red-500 text-sm mt-2" x-text="getError('duration_minutes')"></p>
                </template>  
            </div>
        </div>
        <div class="grid grid-cols-3 gap-6">
           
            <div class="flex items-center ps-2 border rounded-lg">
                <input id="negative-marking" type="checkbox"
                    x-model="negativeMarkingEnabled"
                    class="ml-2  text-blue-600 bg-gray-100 border-gray-300 rounded-sm">
                <label for="negative_marking" class=" ml-4 tracking-wide font-bold text-gray-900 dark:text-gray-300">Enable negative marking?</label>

            </div>
          
            <div class="col-span-2">
                <label for="wrong_to_correct_ratio" class="block  font-bold tracking-wider mb-2">Wrong-to-Correct Ratio</label>
                <input type="number" name="wrong_to_correct_ratio" id="wrong_to_correct_ratio"
                       class="w-full px-4 py-2 border rounded-lg"
                       min="0" max="10" value="{{ old('wrong_to_correct_ratio',0) }}"
                       
                       :disabled="!negativeMarkingEnabled"
                       x-bind:class="!negativeMarkingEnabled ?  'cursor-not-allowed' : ' '"
                       x-bind:value="!negativeMarkingEnabled ? '0' : '{{ old('wrong_to_correct_ratio',4) }}'">
                <template x-if="hasError('wrong_to_correct_ratio')">
                    <p class="text-red-500 text-sm mt-2" x-text="getError('wrong_to_correct_ratio')"></p>
                </template>  
            </div>
        </div>

        <div class="mt-2 flex  justify-between space-x-4">

            <button type="submit" 
                class="flex-1 py-3 rounded-lg text-white font-bold   tracking-wider transition-all duration-300 transform bg-[#41825e] hover:bg-[#357652] hover:scale-103 shadow-lg cursor-pointer">
                Create Quiz 
            </button>
            <button type="submit" 
                    class="w-52  border-2 py-3 rounded-lg  font-bold transition-all duration-300 transform border-[#417582] text-[#417582]  hover:bg-[#2c606d] hover:text-white hover:scale-105 shadow-lg cursor-pointer">
                Generate Quiz With AI <i class="fa-solid fa-gears"></i>
            </button>
        </div>
    </form>
</div>
@endsection
