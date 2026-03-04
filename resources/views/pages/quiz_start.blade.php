@extends("layouts.app")
@props(['quiz', 'quiz_result', 'is_new_attempt'])
@section("content")

    <div x-data="quizPlayer({
                durationMinutes: {{ $quiz->duration_minutes }},
                startTime: {{ now()->timestamp }},
                isNew: {{ $is_new_attempt ? 'true' : 'false' }}, 
                checkUrl: '{{ route('quiz.check', ['quiz' => $quiz->id, 'quiz_result' => $quiz_result->id]) }}',
                token: '{{ csrf_token() }}'
            })" x-cloak x-init="init()" class="min-h-screen p-4 md:p-8 font-sans flex justify-center items-start">

        <div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">

            {{-- SOL TARAF: SORU VE ŞIKLAR --}}
            <div class="lg:col-span-8 flex flex-col h-full space-y-6">

                {{-- Başlık --}}
                <div
                    class="bg-gray-800 px-5 py-4 border border-gray-700 rounded-xl flex justify-between items-center shadow-sm">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('quiz.show', $quiz) }}" class="text-gray-400 hover:text-white transition">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <h1 class="text-base font-bold text-gray-200">{{ $quiz->title }}</h1>
                    </div>
                    <span
                        class="text-[10px] font-bold text-rose-400 bg-rose-900/20 border border-rose-500/30 px-2.5 py-1 rounded-md uppercase tracking-widest flex items-center gap-1.5">
                        <i class="fa-solid fa-flag-checkered"></i> Sınav Modu
                    </span>
                </div>

                {{-- Sorular Döngüsü --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-8 shadow-xl relative min-h-[400px]">
                    @foreach ($quiz->questions as $index => $question)
                        <div x-show="active === {{ $index }}" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">

                            {{-- Soru Üst Bilgi --}}
                            <div class="flex justify-between items-center mb-5 border-b border-gray-700 pb-4">
                                <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest">
                                    Soru {{ $index + 1 }} <span class="text-gray-600">/ {{ $quiz->questions->count() }}</span>
                                </h2>
                                @if ($question->points)
                                    <span
                                        class="bg-gray-900 border border-gray-600 text-gray-300 text-xs px-2.5 py-1 rounded-md font-bold">{{ $question->points }}
                                        Puan</span>
                                @endif
                            </div>

                            {{-- Soru Metni --}}
                            <h1 class="text-xl md:text-2xl font-bold leading-relaxed text-white mb-6">
                                {{ $question->question_text }}
                            </h1>

                            {{-- Görsel --}}
                            @if ($question->img_url)
                                <div class="mb-6 rounded-lg overflow-hidden border border-gray-700">
                                    <img src="{{ asset('storage/' . $question->img_url) }}"
                                        class="w-full h-auto max-h-72 object-contain bg-gray-900">
                                </div>
                            @endif

                            {{-- Şıklar --}}
                            <div class="grid grid-cols-1 gap-3 mt-2">
                                @foreach ($question->answers as $ansIndex => $answer)
                                    <button @click="toggle({{ $question->id }}, {{ $answer->id }})"
                                        class="w-full p-3 rounded-lg border transition-all duration-200 flex items-center text-left group"
                                        :class="answers[{{ $question->id }}] == {{ $answer->id }} ? 'bg-blue-900/20 border-blue-500' : 'bg-[#1e1e1e] border-gray-700 hover:border-gray-500'">

                                        <span
                                            class="w-8 h-8 rounded-md flex items-center justify-center font-bold mr-3 flex-shrink-0 transition-colors text-sm"
                                            :class="answers[{{ $question->id }}] == {{ $answer->id }} ? 'bg-blue-500 text-white border-blue-400' : 'bg-gray-800 border-gray-600 text-gray-400 group-hover:text-white'">
                                            {{ chr(65 + $ansIndex) }}
                                        </span>

                                        <span class="text-sm font-medium flex-1"
                                            :class="answers[{{ $question->id }}] == {{ $answer->id }} ? 'text-blue-100' : 'text-gray-300'">
                                            {{ $answer->answer_text }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>

                        </div>
                    @endforeach

                    {{-- Önceki / Sonraki Navigasyonu --}}
                    <div class="mt-8 flex justify-between items-center pt-5 border-t border-gray-700">
                        <button @click="prev()" :disabled="active === 0"
                            class="px-5 py-2.5 rounded-lg text-sm font-bold transition-colors flex items-center gap-2"
                            :class="active === 0 ? 'text-gray-600 bg-gray-800' : 'text-gray-300 bg-gray-700 hover:bg-gray-600'">
                            <i class="fa-solid fa-arrow-left"></i> Önceki
                        </button>

                        <button @click="next({{ $quiz->questions->count() - 1 }})"
                            x-show="active !== {{ $quiz->questions->count() - 1 }}"
                            class="px-5 py-2.5 rounded-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 transition-colors flex items-center gap-2">
                            Sonraki <i class="fa-solid fa-arrow-right"></i>
                        </button>

                        {{-- Son Soruda Gözükecek --}}
                        <div x-show="active === {{ $quiz->questions->count() - 1 }}" style="display:none;">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                                <i class="fa-solid fa-flag-checkered"></i> Son Soru
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- SAĞ TARAF: SIDEBAR --}}
            <div class="lg:col-span-4 self-start">
                <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-xl p-5 sticky top-6">

                    {{-- 🕒 SAYAÇ (Sadece Sınav Moduna Özel) --}}
                    <div
                        class="bg-gray-900 rounded-xl p-4 mb-5 border border-gray-700 text-center shadow-inner relative overflow-hidden">
                        <div class="absolute inset-0 bg-blue-500/5 animate-pulse"></div>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest mb-1 relative z-10">Kalan Süre</p>
                        <div
                            class="text-3xl font-mono font-bold text-white tracking-wider flex justify-center items-center gap-2 relative z-10">
                            <i class="fa-regular fa-clock text-blue-500 text-xl"></i>
                            <span x-text="countdownText.replace('Time: ', '')"></span>
                        </div>
                    </div>

                    {{-- Soru Gezgini --}}
                    <div class="mb-4">
                        <h3
                            class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 border-b border-gray-700 pb-2">
                            Soru Gezgini
                        </h3>
                        <div class="grid grid-cols-5 sm:grid-cols-6 lg:grid-cols-4 xl:grid-cols-5 gap-2">
                            @foreach ($quiz->questions as $index => $question)
                                <button @click="active = {{ $index }}; window.scrollTo({top: 0});"
                                    class="h-9 w-full rounded-md font-bold text-xs transition-all border-2 flex items-center justify-center relative"
                                    :class="{
                                                'border-blue-500 bg-blue-500 text-white shadow-md scale-105 z-10': active == {{ $index }}, // Aktif Soru
                                                'border-emerald-500/50 bg-emerald-500/10 text-emerald-400': answers[{{ $question->id }}] && active != {{ $index }}, // Çözüldü
                                                'border-gray-700 bg-gray-800 text-gray-500 hover:border-gray-500 hover:text-gray-300': !answers[{{ $question->id }}] && active != {{ $index }} // Boş
                                            }">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Renk Anlamları --}}
                    <div
                        class="flex flex-col gap-2 mt-4 pt-4 border-t border-gray-700 text-[10px] text-gray-400 font-medium">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-[3px]"></div> Şu anki soru
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-emerald-500/20 border border-emerald-500/50 rounded-[3px]"></div>
                            Cevaplandı
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-gray-800 border border-gray-700 rounded-[3px]"></div> Boş
                        </div>
                    </div>

                    {{-- Sınavı Bitir Butonu --}}
                    <div class="mt-6 pt-4 border-t border-gray-700">
                        <button @click="if(confirm('Sınavı bitirmek istediğine emin misin?')) submitQuiz()"
                            class="w-full py-3 rounded-lg text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 transition-colors shadow-lg shadow-green-900/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-flag-checkered"></i> Sınavı Bitir
                        </button>
                        <p class="text-center text-[10px] text-gray-500 mt-2">
                            Bitirdikten sonra cevapları değiştiremezsin.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection