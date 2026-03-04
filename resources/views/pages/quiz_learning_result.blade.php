@extends("layouts.app")

@section("content")

@php
    $details = collect($result->details);
    $isOwnQuiz = Auth::check() && $result->quiz->user_id === Auth::id();
@endphp

<div class="min-h-screen p-4 md:p-6 lg:p-8 font-sans text-gray-200" 
     x-data="{ 
         isGenerating: false, 
         generateType: 'weakness', 
         questionCount: 3
     }">
    
    {{-- Üst Başlık ve Butonlar (Sadeleştirildi, Kartın üstüne alındı) --}}
    <div class="max-w-7xl mx-auto mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-white flex items-center gap-2 drop-shadow-md">
                <i class="fa-solid fa-brain text-blue-400 text-lg"></i> Öğrenme Modu Sonucu
            </h1>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest drop-shadow">Quiz: <span class="text-blue-400 font-bold">{{ $result->quiz->title }}</span></p>
        </div>
        
        <div class="flex gap-3 w-full sm:w-auto">
            @auth
                <a href="{{ route('library.show') }}" class="flex-1 sm:flex-none justify-center px-4 py-2 rounded-lg bg-gray-800/80 backdrop-blur-md text-gray-300 hover:text-white hover:bg-gray-700 text-xs font-bold border border-gray-700/50 transition-colors flex items-center shadow-sm">
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Kütüphane
                </a>
            @endauth
            <a href="{{ route('quiz.show', $result->quiz->slug) }}" class="flex-1 sm:flex-none justify-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-xs font-bold shadow-lg shadow-blue-900/30 transition-colors flex items-center">
                <i class="fa-solid fa-rotate-right mr-1.5"></i> Tekrar Çöz
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">

        {{-- SOL TARAF: ÖZET VE İNCELEME --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- 1. YENİ İHTİŞAMLI ÖZET KARTI (Sınav moduyla aynı bileşen kullanıldı) --}}
            <x-quiz_result_summary :result="$result" />

            {{-- 2. SORU İNCELEME LİSTESİ --}}
            <div>
                <div class="flex items-center justify-between border-b border-gray-700/50 pb-3 mb-4 mt-8">
                    <h2 class="text-sm font-bold text-gray-200 uppercase tracking-widest flex items-center drop-shadow-sm">
                        <i class="fa-solid fa-magnifying-glass mr-2 text-blue-400"></i> Detaylı İnceleme
                    </h2>
                    <div class="flex gap-3 text-[10px] font-bold text-gray-400 uppercase drop-shadow-sm">
                        <span class="flex items-center"><div class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></div> Doğru</span>
                        <span class="flex items-center"><div class="w-2 h-2 rounded-full bg-rose-500 mr-1.5"></div> Yanlış</span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @foreach($result->quiz->questions as $index => $question)
                        @php
                            $qDetail = $details->firstWhere('question_id', $question->id);
                            $givenAnswerId = $qDetail['given_answer'] ?? null;
                            $isCorrect = $qDetail['is_correct'] ?? false;
                            
                            $correctAnswer = $question->answers->where('is_correct', true)->first();
                            $givenAnswer = $givenAnswerId ? $question->answers->where('id', $givenAnswerId)->first() : null;
                            
                            $cardBorder = $isCorrect ? 'border-emerald-500/30' : ($givenAnswerId ? 'border-rose-500/30' : 'border-gray-700/50');
                            $statusIcon = $isCorrect ? 'fa-check text-emerald-400' : ($givenAnswerId ? 'fa-xmark text-rose-400' : 'fa-minus text-gray-500');
                            $statusColor = $isCorrect ? 'text-emerald-400' : ($givenAnswerId ? 'text-rose-400' : 'text-gray-500');
                        @endphp

                        <div class="bg-gray-800/90 backdrop-blur-sm rounded-xl border {{ $cardBorder }} overflow-hidden shadow-lg transition hover:bg-gray-800">
                            <div class="bg-gray-900/40 px-4 py-2.5 flex justify-between items-center border-b border-gray-700/30">
                                <span class="font-mono font-bold text-xs {{ $statusColor }}">Soru {{ $index + 1 }}</span>
                                <i class="fa-solid {{ $statusIcon }} text-sm"></i>
                            </div>

                            <div class="p-4 md:p-5">
                                <h4 class="text-base font-bold text-gray-100 leading-relaxed mb-4">
                                    {{ $question->question_text }}
                                </h4>

                                <div class="space-y-3">
                                    {{-- Doğru Şık --}}
                                    <div class="p-3 rounded-lg bg-emerald-900/10 border border-emerald-500/20 flex gap-3">
                                        <span class="text-emerald-500 mt-0.5"><i class="fa-solid fa-check-circle"></i></span>
                                        <div>
                                            <p class="text-[10px] text-emerald-500/80 font-bold uppercase tracking-widest mb-0.5">Doğru Cevap</p>
                                            <p class="text-xs font-medium text-emerald-100">{{ $correctAnswer->answer_text ?? 'Cevap bulunamadı' }}</p>
                                            
                                            @if($correctAnswer && $correctAnswer->explanation)
                                                <div class="mt-2 pt-2 border-t border-emerald-500/20 text-[11px] text-emerald-200/80 flex gap-2 leading-relaxed">
                                                    <i class="fa-solid fa-wand-magic-sparkles mt-0.5 text-blue-400"></i>
                                                    <p>{{ $correctAnswer->explanation }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Yanlış Şık --}}
                                    @if(!$isCorrect && $givenAnswer)
                                        <div class="p-3 rounded-lg bg-rose-900/10 border border-rose-500/20 flex gap-3">
                                            <span class="text-rose-500 mt-0.5"><i class="fa-solid fa-xmark-circle"></i></span>
                                            <div>
                                                <p class="text-[10px] text-rose-500/80 font-bold uppercase tracking-widest mb-0.5">Senin Cevabın</p>
                                                <p class="text-xs font-medium text-rose-200">{{ $givenAnswer->answer_text }}</p>
                                                
                                                @if($givenAnswer->explanation)
                                                    <div class="mt-2 pt-2 border-t border-rose-500/20 text-[11px] text-rose-200/80 flex gap-2 leading-relaxed">
                                                        <i class="fa-solid fa-circle-info mt-0.5 text-blue-400"></i>
                                                        <p>{{ $givenAnswer->explanation }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif(!$isCorrect && !$givenAnswer)
                                        <div class="p-3 rounded-lg bg-gray-700/30 border border-gray-600/50 flex gap-3">
                                            <span class="text-gray-500 mt-0.5"><i class="fa-solid fa-minus-circle"></i></span>
                                            <div>
                                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-0.5">Senin Cevabın</p>
                                                <p class="text-xs text-gray-400 italic">Boş bıraktın</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- SAĞ TARAF: YAPAY ZEKA SIDEBAR'I --}}
        <div class="lg:col-span-4">
            <div class="sticky top-6 space-y-6">
                
                @if($isOwnQuiz)
                    <div class="bg-gray-800/90 backdrop-blur-md border border-blue-500/30 rounded-2xl shadow-xl overflow-hidden">
                        
                        {{-- AI Form Başlığı --}}
                        <div class="bg-blue-900/20 px-5 py-4 border-b border-blue-500/20 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-blue-100">AI Soru Üretici</h3>
                                <p class="text-[9px] text-blue-300/70 uppercase tracking-widest mt-0.5">Mevcut Quizi Geliştir</p>
                            </div>
                        </div>

                        <div class="p-5">
                            {{-- Sekmeler --}}
                            <div class="flex p-1 bg-gray-900/50 rounded-lg mb-5 border border-gray-700/50">
                                <button @click="generateType = 'weakness'" 
                                        :class="generateType === 'weakness' ? 'bg-gray-700 text-white shadow-sm' : 'text-gray-400 hover:text-gray-300'"
                                        class="flex-1 py-2 text-[10px] font-bold uppercase tracking-wide rounded-md transition-all flex flex-col items-center gap-1">
                                    <i class="fa-solid fa-chart-line text-sm mb-0.5"></i> Yanlışlarımdan
                                </button>
                                <button @click="generateType = 'new_topics'" 
                                        :class="generateType === 'new_topics' ? 'bg-gray-700 text-white shadow-sm' : 'text-gray-400 hover:text-gray-300'"
                                        class="flex-1 py-2 text-[10px] font-bold uppercase tracking-wide rounded-md transition-all flex flex-col items-center gap-1">
                                    <i class="fa-solid fa-lightbulb text-sm mb-0.5"></i> Sorulmayanlardan
                                </button>
                            </div>

                            <form @submit.prevent="/* İleride axios post buraya gelecek */" class="space-y-4">
                                
                                {{-- Soru Sayısı --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1.5 flex items-center gap-2">
                                        <i class="fa-solid fa-layer-group text-blue-400"></i> Eklenecek Soru Sayısı
                                    </label>
                                    <select x-model="questionCount" class="w-full bg-gray-900/80 border border-gray-700 text-sm text-gray-200 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                        <option value="5">5 Soru Ekle</option>
                                        <option value="10">10 Soru Ekle</option>
                                        <option value="15">15 Soru Ekle</option>
                                        <option value="20">20 Soru Ekle</option>
                                    </select>
                                </div>

                                {{-- Ekstra Talimat (Prompt) --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1.5 flex items-center gap-2">
                                        <i class="fa-solid fa-comment-dots text-blue-400"></i> Özel Talimat (Opsiyonel)
                                    </label>
                                    <textarea rows="3" placeholder="Örn: Soruları daha zor yap, uzun paragraflı olsun..." class="w-full bg-gray-900/80 border border-gray-700 text-xs text-gray-200 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none"></textarea>
                                </div>

                                {{-- Dosya Yükleme Alanı --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1.5 flex items-center gap-2">
                                        <i class="fa-solid fa-file-pdf text-blue-400"></i> Kaynak Belge (Opsiyonel)
                                    </label>
                                    <div class="relative w-full">
                                        <input type="file" accept=".pdf,.doc,.docx,.txt" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="w-full bg-gray-900/80 border border-dashed border-gray-600 rounded-lg p-4 text-center group transition-colors hover:border-blue-500">
                                            <i class="fa-solid fa-cloud-arrow-up text-gray-500 group-hover:text-blue-400 transition-colors mb-1 text-lg"></i>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">Yeni Dosya Yükle</p>
                                        </div>
                                    </div>
                                    <p class="text-[9px] text-gray-500 mt-1 italic">Boş bırakırsanız mevcut orijinal belge kullanılır.</p>
                                </div>

                                {{-- Submit Butonu --}}
                                <div class="pt-2">
                                    <button type="submit" 
                                            @click="isGenerating = true"
                                            :disabled="isGenerating"
                                            class="w-full py-3 bg-gray-200 hover:bg-white text-gray-900 disabled:bg-gray-600 disabled:text-gray-400 rounded-lg font-bold text-xs shadow-lg transition-all flex justify-center items-center gap-2">
                                        
                                        <i x-show="!isGenerating" class="fa-solid fa-bolt text-blue-600"></i>
                                        <i x-show="isGenerating" class="fa-solid fa-circle-notch fa-spin" style="display: none;"></i>
                                        
                                        <span x-show="!isGenerating">Soruları Üret ve Ekle</span>
                                        <span x-show="isGenerating" style="display: none;">AI Hazırlıyor...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-800/90 backdrop-blur-md border border-gray-700/50 rounded-2xl shadow-xl p-5 text-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto mb-3 text-xl">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                        <h3 class="text-sm font-bold text-gray-200 mb-1">Pratik Tamamlandı</h3>
                        <p class="text-[10px] text-gray-400">Bu quizi hazırlayan kullanıcıya teşekkür edebilirsin.</p>
                    </div>
                @endif
                
            </div>
        </div>

    </div>
</div>
@endsection