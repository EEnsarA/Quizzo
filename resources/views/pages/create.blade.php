@extends("layouts.app")

@section("content")

<div class="min-h-screen w-full flex items-start justify-center p-4 md:p-8 pt-10 bg-gray-900">
   
    <div class="w-full max-w-4xl bg-gray-800 rounded-2xl shadow-xl border border-gray-700 overflow-hidden"
         x-data="quizCreate({ errors: {{ Js::from($errors->getMessages()) }} })">

    
        <div class="bg-gray-800 border-b border-gray-700 p-6 md:p-8 text-center">
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Yeni Quiz Oluştur</h1>
            <p class="text-gray-400 mt-2 text-sm">Bilgini test etmek için harika bir quiz hazırla.</p>
        </div>


        <div class="p-6 md:p-10">
            <form action="{{ route("quiz.add") }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

  
                <div class="space-y-6">
       
                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Quiz Başlığı</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-heading text-gray-500"></i>
                            </div>
                  
                            <input type="text" name="title" id="title" 
                                   class="w-full pl-10 pr-4 py-3 bg-gray-900 border border-gray-600 rounded-xl focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white placeholder-gray-500 transition-colors"
                                   placeholder="Örn: 'Python Programlama Temelleri'"
                                   value="{{ old('title') }}" required>
                        </div>
                        <template x-if="hasError('title')">
                            <p class="text-red-400 text-xs mt-1 font-semibold" x-text="getError('title')"></p>
                        </template>
                    </div>

          
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          
                        <div>
                            <label for="subject" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Konu</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-tag text-gray-500"></i>
                                </div>
                                <input type="text" name="subject" id="subject" 
                                       class="w-full pl-10 pr-4 py-3 bg-gray-900 border border-gray-600 rounded-xl focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white placeholder-gray-500 transition-colors"
                                       placeholder="Örn: Tarih, Yazılım"
                                       value="{{ old('subject') }}">
                            </div>
                            <template x-if="hasError('subject')">
                                <p class="text-red-400 text-xs mt-1 font-semibold" x-text="getError('subject')"></p>
                            </template>
                        </div>

                
                        <div>
                            <label for="difficulty" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Zorluk Seviyesi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-layer-group text-gray-500"></i>
                                </div>
                                <select name="difficulty" id="difficulty" class="w-full pl-10 pr-4 py-3 bg-gray-900 border border-gray-600 rounded-xl focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white cursor-pointer appearance-none">
                                    <option value="" disabled selected>Seviye Seçiniz</option>
                                    <option value="easy">Kolay (Easy)</option>
                                    <option value="medium">Orta (Medium)</option>
                                    <option value="hard">Zor (Hard)</option>
                                    <option value="expert">Uzman (Expert)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-gray-500 text-xs"></i>
                                </div>
                            </div>
                            <template x-if="hasError('difficulty')">
                                <p class="text-red-400 text-xs mt-1 font-semibold" x-text="getError('difficulty')"></p>
                            </template>
                        </div>
                    </div>

          
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Açıklama</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full p-4 bg-gray-900 border border-gray-600 rounded-xl focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white placeholder-gray-500 transition-colors resize-none"
                                  placeholder="Bu quiz hakkında kısa bilgi ver...">{{ old('description') }}</textarea>
                        <template x-if="hasError('description')">
                            <p class="text-red-400 text-xs mt-1 font-semibold" x-text="getError('description')"></p>
                        </template>
                    </div>
                </div>

                <hr class="border-gray-700">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                   
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Kapak Görseli</label>
                        
                        <div class="relative group">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-600 border-dashed rounded-xl cursor-pointer bg-gray-900 hover:bg-gray-800 hover:border-emerald-500 transition-all">
                                
                            
                                <div x-show="!fileUrl" class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-500 mb-3 group-hover:text-emerald-500 transition-colors"></i>
                                    <p class="mb-1 text-sm text-gray-400"><span class="font-bold text-emerald-500">Yüklemek için tıkla</span></p>
                                    <p class="text-xs text-gray-500">PNG, JPG (Max. 800x400)</p>
                                </div>

                          
                                <img x-show="fileUrl" :src="fileUrl" class="absolute inset-0 w-full h-full object-cover rounded-xl opacity-80 group-hover:opacity-100 transition-opacity">
                                
                                <input id="dropzone-file" type="file" name="img_url" accept="image/*" class="hidden"
                                    @change="fileName = $event.target.files[0]?.name; fileUrl = URL.createObjectURL($event.target.files[0])" />
                            </label>
                    
                            <div x-show="fileName" class="mt-2 flex items-center justify-between bg-gray-900 p-2 rounded border border-gray-600">
                                <span class="text-xs text-gray-300 truncate w-32" x-text="fileName"></span>
                                <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                            </div>
                        </div>
                        <template x-if="hasError('img_url')">
                            <p class="text-red-400 text-xs mt-1 font-semibold" x-text="getError('img_url')"></p>
                        </template>
                    </div>

          
                    <div class="lg:col-span-2 space-y-6">
                        
                        <div class="grid grid-cols-2 gap-6">
                      
                            <div>
                                <label for="number_of_questions" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Soru Sayısı</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-list-ol text-gray-500"></i>
                                    </div>
                                    <input type="number" name="number_of_questions" id="number_of_questions" 
                                           class="w-full pl-10 pr-4 py-3 bg-gray-900 border border-gray-600 rounded-xl focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white"
                                           min="4" max="20" value="{{ old('number_of_questions', 5) }}">
                                </div>
                                <template x-if="hasError('number_of_questions')">
                                    <p class="text-red-400 text-xs mt-1 font-semibold" x-text="getError('number_of_questions')"></p>
                                </template>
                            </div>

                            <div>
                                <label for="number_of_options" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Seçenek Sayısı</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-list-ul text-gray-500"></i>
                                    </div>
                                    <input type="number" name="number_of_options" id="number_of_options" 
                                           class="w-full pl-10 pr-4 py-3 bg-gray-900 border border-gray-600 rounded-xl focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white"
                                           min="2" max="5" value="{{ old('number_of_options', 4) }}">
                                </div>
                                <template x-if="hasError('number_of_options')">
                                    <p class="text-red-400 text-xs mt-1 font-semibold" x-text="getError('number_of_options')"></p>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label for="duration_minutes" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Süre (Dakika)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-regular fa-clock text-gray-500"></i>
                                </div>
                                <input type="number" name="duration_minutes" id="duration_minutes"
                                       class="w-full pl-10 pr-4 py-3 bg-gray-900 border border-gray-600 rounded-xl focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white"
                                       min="1" max="120" value="{{ old('duration_minutes', 5) }}">
                            </div>
                            <template x-if="hasError('duration_minutes')">
                                <p class="text-red-400 text-xs mt-1 font-semibold" x-text="getError('duration_minutes')"></p>
                            </template>
                        </div>

                  
                        <div class="bg-gray-900 p-4 rounded-xl border border-gray-600">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <input id="negative-marking" type="checkbox" x-model="negativeMarkingEnabled" class="w-5 h-5 text-emerald-600 bg-gray-700 border-gray-500 rounded focus:ring-emerald-600 focus:ring-2">
                                    <label for="negative-marking" class="ml-3 text-sm font-bold text-gray-300 cursor-pointer">Yanlış doğruyu götürsün mü?</label>
                                </div>
                            </div>
                         
                            <div x-show="negativeMarkingEnabled" x-transition class="mt-2">
                                <label for="wrong_to_correct_ratio" class="block text-xs text-gray-500 mb-1">Kaç yanlış 1 doğruyu götürsün?</label>
                                <input type="number" name="wrong_to_correct_ratio" id="wrong_to_correct_ratio"
                                       class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                       min="1" max="10" 
                                       :disabled="!negativeMarkingEnabled"
                                       :value="!negativeMarkingEnabled ? '0' : '{{ old('wrong_to_correct_ratio', 4) }}'">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- AKSİYON BUTONLARI --}}
                <div class="pt-6 flex flex-col sm:flex-row gap-4">
                    <button type="submit" 
                            class="flex-1 py-4 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-900/20 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check"></i> Quizi Oluştur
                    </button>
                    
                    <button type="submit" 
                            formaction="{{ route('quiz.ai_generate') }}" formmethod="POST"
                            class="sm:w-64 py-4 bg-gray-700 hover:bg-gray-600 text-blue-400 border border-blue-900/30 font-bold rounded-xl shadow-lg transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2 group">
                        <i class="fa-solid fa-wand-magic-sparkles group-hover:animate-pulse"></i> AI ile Üret
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

@endsection