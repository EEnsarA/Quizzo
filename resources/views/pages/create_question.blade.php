@extends("layouts.app")

@props(['quiz'])

@section("content")

<div class="flex flex-col h-[calc(100vh-theme(spacing.16))] bg-[#1e1e1e] text-[#cccccc] font-sans overflow-hidden"
     x-data="questionCreate({ 
        quizId: {{ $quiz->id }},
        number_of_questions: {{ $quiz->number_of_questions }},
        number_of_options : {{ $quiz->number_of_options }} 
     })">

   
    <header class="h-14 bg-[#252526] border-b border-[#3e3e42] flex items-center justify-between px-4 shadow-md z-10 flex-shrink-0">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-gray-400">
                <i class="fa-solid fa-clipboard-question text-emerald-500"></i>
                <span class="text-xs font-bold uppercase tracking-wider">Soru Editörü</span>
            </div>
            <div class="h-6 w-px bg-[#3e3e42]"></div>
            <h1 class="text-white font-bold text-sm truncate max-w-md" title="{{$quiz->title}}">{{$quiz->title}}</h1>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-xs text-gray-500 font-mono mr-2">
                <span x-text="current_q_index + 1"></span> / {{ $quiz->number_of_questions }}
            </div>
            <button @click="submitForm('{{ route('questions.add') }}','{{ csrf_token() }}')" 
                    class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-xs font-bold shadow-lg transition-colors flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Kaydet & Bitir
            </button>
        </div>
    </header>

   
    <div class="flex-1 flex overflow-hidden">

     
        <aside class="w-72 bg-[#252526] border-r border-[#3e3e42] flex flex-col z-20">
            
           
            <div class="p-4 border-b border-[#3e3e42] flex justify-between items-center shadow-sm z-10 bg-[#252526]">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">SORU LİSTESİ</h3>
                <span class="text-[10px] text-gray-400 bg-[#1e1e1e] px-2 py-0.5 rounded border border-[#3e3e42] font-mono" x-text="questions.length"></span>
            </div>
            
         
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                
            
                <div class="p-3">
                    <div class="grid grid-cols-4 gap-2">
                        <template x-for="(q, idx) in questions" :key="idx">
                            <button type="button"
                                    @click="goToQuestion(idx)"
                                    class="aspect-square rounded flex items-center justify-center text-xs font-bold transition-all border relative"
                                    :class="idx === current_q_index 
                                        ? 'bg-emerald-600 border-emerald-500 text-white shadow-[0_0_10px_rgba(16,185,129,0.3)]' 
                                        : (q.title ? 'bg-[#3e3e42] border-gray-600 text-gray-300 hover:bg-gray-600' : 'bg-[#1e1e1e] border-[#3e3e42] text-gray-600 hover:border-gray-500')">
                                <span x-text="idx+1"></span>
                            
                                <div x-show="q.title" class="absolute top-0.5 right-0.5 w-1.5 h-1.5 bg-green-500 rounded-full"></div>
                            </button>
                        </template>
                    </div>
                </div>

           
                <div class="px-3 pb-4">
                    <div class="border-t border-[#3e3e42] my-3"></div> 
                    
                    <h3 class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-book"></i> Kaynak Döküman
                    </h3>
                    
                    <div class="relative group">
                        <label class="flex items-center gap-3 px-3 py-3 bg-[#1e1e1e] border border-dashed border-gray-600 hover:border-indigo-500 rounded-lg cursor-pointer transition-colors">
                  
                            <div class="w-9 h-9 rounded bg-[#252526] flex items-center justify-center group-hover:bg-indigo-500/20 transition-colors flex-shrink-0">
                                <i class="fa-solid fa-file-pdf text-gray-400 group-hover:text-indigo-400"></i>
                            </div>
                            
                            <div class="flex-1 overflow-hidden">
                                <p class="text-xs font-bold text-white truncate" 
                                   x-text="sourceFileName || 'Dosya Seçmek İçin Tıkla'">
                                </p>
                                <p class="text-[10px] text-gray-500 truncate mt-0.5" 
                                   x-text="sourceFileName ? 'Değiştirmek için tıkla' : 'PDF, DOCX veya TXT'">
                                </p>
                            </div>
    
                            <input type="file" @change="sourceFileName = $event.target.files[0]?.name;" class="hidden">
                        </label>
                        
                    
                        <div x-show="sourceFileName" class="mt-2 flex items-center gap-1.5 text-[10px] text-green-500 bg-green-500/10 px-2 py-1.5 rounded border border-green-500/20 justify-center">
                            <i class="fa-solid fa-circle-check"></i> 
                            <span>Döküman Analize Hazır</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="p-4 border-t border-[#3e3e42] grid grid-cols-2 gap-2 bg-[#252526] z-10 shadow-[0_-5px_15px_rgba(0,0,0,0.3)]">
                <button type="button" @click="prevQuestion" :disabled="current_q_index === 0" 
                        class="px-3 py-2 bg-[#333] hover:bg-[#444] disabled:opacity-50 disabled:cursor-not-allowed text-white rounded text-xs font-bold flex items-center justify-center gap-1 transition">
                    <i class="fa-solid fa-chevron-left"></i> Önceki
                </button>
                
                <button type="button" @click="nextQuestion" :disabled="current_q_index === questions.length-1" 
                        class="px-3 py-2 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded text-xs font-bold flex items-center justify-center gap-1 transition">
                    Sonraki <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </aside>

     
        <main class="flex-1 overflow-y-auto bg-[#1e1e1e] p-6 md:p-10 relative">
            <div class="max-w-4xl mx-auto space-y-6 pb-20">
                
           
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-4">
                        <div class="text-4xl font-black text-[#3e3e42] select-none">Q<span x-text="current_q_index + 1"></span></div>
                    </div>
                    
              
                    <button type="button"
                            @click="generateSingleQuestionAI()" 
                            class="group relative px-4 py-1.5 bg-indigo-600/10 hover:bg-indigo-600 border border-indigo-500/50 hover:border-indigo-500 text-indigo-400 hover:text-white rounded-full text-xs font-bold transition-all flex items-center gap-2 overflow-hidden">
                        
                   
                        <div class="absolute inset-0 translate-x-[-100%] group-hover:translate-x-[100%] bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-1000"></div>
                        
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>Bu Soruyu AI ile Doldur</span>
                    </button>
                </div>
                <div class="h-px bg-[#3e3e42] w-full mb-6"></div>

                <div class="bg-[#252526] border border-[#3e3e42] border-l-4 border-l-blue-500 rounded-r-lg p-6 shadow-lg relative">
                    
                    <div x-show="aiLoading" class="absolute inset-0 bg-[#252526]/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center rounded-r-lg">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500 mb-2"></div>
                        <span class="text-xs font-bold text-indigo-400 animate-pulse">Soru Üretiliyor...</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <div class="md:col-span-8 space-y-4">
                            <div class="group">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase">Soru Başlığı</label>
                                <input type="text"
                                    class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-blue-500 rounded-lg p-3 text-sm text-white focus:outline-none focus:ring-0 transition-all placeholder-gray-600 font-bold"
                                    placeholder="Örn: Python Değişkenler"
                                    x-model="questions[current_q_index].title">
                                <template x-if="hasError('title')"><p class="text-red-400 text-[10px] mt-1" x-text="getError('title')"></p></template>
                            </div>

                            <div class="group">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase">Soru Metni</label>
                                <textarea rows="4"
                                        class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-blue-500 rounded-lg p-3 text-sm text-white focus:outline-none focus:ring-0 transition-all placeholder-gray-600 resize-none leading-relaxed"
                                        placeholder="Sorunuzu buraya detaylıca yazın..."
                                        x-model="questions[current_q_index].content"></textarea>
                                <template x-if="hasError('content')"><p class="text-red-400 text-[10px] mt-1" x-text="getError('content')"></p></template>
                            </div>
                        </div>

                        <div class="md:col-span-4 space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase">Puan Değeri</label>
                                <div class="relative">
                                    <i class="fa-solid fa-star absolute left-3 top-3 text-yellow-500 text-xs"></i>
                                    <input type="number" class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-yellow-500 rounded-lg p-2.5 pl-8 text-sm text-white focus:outline-none focus:ring-0" min="1" max="100" x-model="questions[current_q_index].point">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase">Görsel (Opsiyonel)</label>
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-[#3e3e42] border-dashed rounded-lg cursor-pointer bg-[#1e1e1e] hover:bg-[#2d2d30] hover:border-blue-500 transition-all relative group overflow-hidden">
                                    <div x-show="!questions[current_q_index].fileUrl" class="text-center">
                                        <i class="fa-regular fa-image text-2xl text-gray-600 group-hover:text-blue-500 transition-colors mb-2"></i>
                                        <p class="text-[10px] text-gray-500">Resim Seç</p>
                                    </div>
                                    <img x-show="questions[current_q_index].fileUrl" :src="questions[current_q_index].fileUrl" class="absolute inset-0 w-full h-full object-cover">
                                    <div x-show="questions[current_q_index].fileUrl" class="absolute inset-0 bg-black/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-[10px] text-white font-bold"><i class="fa-solid fa-pen"></i> Değiştir</span>
                                    </div>
                                    <input type="file" name="img_url" accept="image/*" class="hidden" @change="setFile($event)">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-[#252526] border border-[#3e3e42] border-l-4 border-l-emerald-500 rounded-r-lg p-6 shadow-lg">
                    <h3 class="text-sm font-bold text-white uppercase mb-4 flex items-center gap-2">
                        <span class="bg-emerald-500/20 text-emerald-400 p-1.5 rounded"><i class="fa-solid fa-list-check"></i></span>
                        Seçenekler
                    </h3>
                    <div class="space-y-3">
                        @for ($i = 0; $i < $quiz->number_of_options; $i++)
                            <div class="flex items-start gap-3 group">
                                <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center bg-[#1e1e1e] border border-[#3e3e42] rounded-lg text-lg font-bold text-gray-500 group-focus-within:text-emerald-500 group-focus-within:border-emerald-500 transition-colors select-none">
                                    {{ chr(65 + $i) }}
                                </div>
                                <div class="flex-1">
                                    <textarea rows="1" class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-emerald-500 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-all placeholder-gray-600 resize-none h-10 min-h-[40px] leading-relaxed overflow-hidden" placeholder="Seçenek metni..." x-model="questions[current_q_index].answers[{{$i}}].answer_content"></textarea>
                                </div>
                                <div class="flex-shrink-0 pt-0.5">
                                    <label class="cursor-pointer select-none">
                                        <input type="checkbox" class="hidden peer" x-model="questions[current_q_index].answers[{{$i}}].is_correct">
                                        <div class="w-10 h-10 rounded-lg border border-[#3e3e42] bg-[#1e1e1e] hover:bg-[#2d2d30] text-gray-600 peer-checked:bg-emerald-600 peer-checked:border-emerald-500 peer-checked:text-white flex items-center justify-center transition-all shadow-sm">
                                            <i class="fa-solid fa-check text-sm" x-show="questions[current_q_index].answers[{{$i}}].is_correct"></i>
                                            <i class="fa-solid fa-check text-sm opacity-20" x-show="!questions[current_q_index].answers[{{$i}}].is_correct"></i>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>
@endsection