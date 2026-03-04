@extends("layouts.app")

@section("content")

    <div x-data="learningPlayer({
                id: {{ $quiz->id }},
                total: {{ $quiz->questions->count() }},
                checkUrl: '{{ route('quiz.check', ['quiz' => $quiz, 'quiz_result' => $quiz_result->id]) }}',
                token: '{{ csrf_token() }}'
            })" x-cloak class="min-h-screen p-4 md:p-8 font-sans flex justify-center items-start">

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
                        class="text-[10px] font-bold text-blue-400 bg-blue-900/20 border border-blue-500/30 px-2.5 py-1 rounded-md uppercase tracking-widest flex items-center gap-1.5">
                        <i class="fa-solid fa-brain"></i> Öğrenme Modu
                    </span>
                </div>

                {{-- Sorular Döngüsü --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-8 shadow-xl relative min-h-[400px]">
                    @foreach ($quiz->questions as $index => $question)
                        @php
                            // Bu sorunun doğru şıkkının ID'sini buluyoruz ki yanlış yaparsa onu da otomatik açalım
                            $correctOptionId = $question->answers->where('is_correct', true)->first()->id ?? 0;
                        @endphp

                        <div x-show="active === {{ $index }}" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">

                            <div class="flex justify-between items-center mb-5 border-b border-gray-700 pb-4">
                                <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest">
                                    Soru {{ $index + 1 }} <span class="text-gray-600">/ {{ $quiz->questions->count() }}</span>
                                </h2>
                            </div>

                            <h1 class="text-xl md:text-2xl font-bold leading-relaxed text-white mb-6">
                                {{ $question->question_text }}
                            </h1>

                            @if ($question->img_url)
                                <div class="mb-6 rounded-lg overflow-hidden border border-gray-700">
                                    <img src="{{ asset('storage/' . $question->img_url) }}"
                                        class="w-full h-auto max-h-72 object-contain bg-gray-900">
                                </div>
                            @endif

                            {{-- Şıklar --}}
                            <div class="grid grid-cols-1 gap-3 mt-2">
                                @foreach ($question->answers as $ansIndex => $answer)
                                    <div class="relative w-full">

                                        {{-- Şık Butonu --}}
                                        <button
                                            @click="selectAnswer({{ $index }}, {{ $question->id }}, {{ $answer->id }}, {{ $answer->is_correct ? 'true' : 'false' }}, {{ $correctOptionId }})"
                                            class="w-full p-3 rounded-lg border transition-all duration-200 flex items-center text-left group"
                                            :class="getOptionClass({{ $index }}, {{ $answer->id }}, {{ $answer->is_correct ? 'true' : 'false' }})">

                                            <span
                                                class="w-8 h-8 rounded-md flex items-center justify-center font-bold mr-3 flex-shrink-0 transition-colors text-sm"
                                                :class="getIconClass({{ $index }}, {{ $answer->id }}, {{ $answer->is_correct ? 'true' : 'false' }})">
                                                {{ chr(65 + $ansIndex) }}
                                            </span>

                                            <span class="text-sm font-medium flex-1"
                                                :class="isExplanationOpen({{ $index }}, {{ $answer->id }}) ? 'text-white' : 'text-gray-300'">
                                                {{ $answer->answer_text }}
                                            </span>

                                            {{-- Doğru/Yanlış Çekiği --}}
                                            <i x-show="showStatusIcon({{ $index }}, {{ $answer->id }}, {{ $answer->is_correct ? 'true' : 'false' }})"
                                                class="fa-solid ml-2"
                                                :class="{{ $answer->is_correct ? 'true' : 'false' }} ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-rose-400'"
                                                style="display: none;"></i>
                                        </button>

                                        {{-- AI Açıklama Kutusu (Tıklandığında Toggle olur) --}}
                                        <div x-show="isExplanationOpen({{ $index }}, {{ $answer->id }})"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 -translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0" class="mt-2 pl-11"
                                            style="display: none;">

                                            {{-- Renk mantığı: Doğru şıksa yeşil, seçilen yanlışsa kırmızı, merak edilen diğer
                                            yanlışsa gri/mavi --}}
                                            <div class="p-3 rounded-lg border flex gap-3"
                                                :class="{
                                                                                                                                     'bg-emerald-900/10 border-emerald-500/30': {{ $answer->is_correct ? 'true' : 'false' }},
                                                                                                                                     'bg-rose-900/10 border-rose-500/30': !{{ $answer->is_correct ? 'true' : 'false' }} && firstAnswers[{{ $index }}]?.id === {{ $answer->id }},
                                                                                                                                     'bg-gray-800 border-gray-700': !{{ $answer->is_correct ? 'true' : 'false' }} && firstAnswers[{{ $index }}]?.id !== {{ $answer->id }}
                                                                                                                                 }">

                                                <i class="fa-solid fa-wand-magic-sparkles mt-0.5 text-sm"
                                                    :class="{
                                                                                                                                       'text-emerald-400': {{ $answer->is_correct ? 'true' : 'false' }},
                                                                                                                                       'text-rose-400': !{{ $answer->is_correct ? 'true' : 'false' }} && firstAnswers[{{ $index }}]?.id === {{ $answer->id }},
                                                                                                                                       'text-indigo-400': !{{ $answer->is_correct ? 'true' : 'false' }} && firstAnswers[{{ $index }}]?.id !== {{ $answer->id }}
                                                                                                                                   }"></i>

                                                <div>
                                                    <p class="text-xs text-gray-300 leading-relaxed">
                                                        {{ $answer->explanation ?? 'Bu şık için açıklama bulunmuyor.' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
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

                        {{-- İstersen Sonraki butonunu sadece cevap verdikten sonra gösterebilirsin:
                        x-show="firstAnswers[active]" --}}
                        <button @click="next()" x-show="active !== {{ $quiz->questions->count() - 1 }}"
                            class="px-5 py-2.5 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors flex items-center gap-2">
                            Sonraki <i class="fa-solid fa-arrow-right"></i>
                        </button>

                        <button @click="finish()" x-show="active === {{ $quiz->questions->count() - 1 }}"
                            style="display:none;"
                            class="px-5 py-2.5 rounded-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors flex items-center gap-2 shadow-lg shadow-emerald-900/20">
                            <i class="fa-solid fa-flag-checkered"></i> Çalışmayı Bitir
                        </button>
                    </div>

                </div>
            </div>

            {{-- SAĞ TARAF: SIDEBAR --}}
            <div class="lg:col-span-4 self-start">
                <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-xl p-5 sticky top-6">

                    <div class="mb-4">
                        <h3
                            class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 border-b border-gray-700 pb-2">
                            Soru Gezgini</h3>
                        <div class="grid grid-cols-5 sm:grid-cols-6 lg:grid-cols-4 xl:grid-cols-5 gap-2">
                            @foreach ($quiz->questions as $index => $question)
                                <button @click="jump({{ $index }})"
                                    class="h-9 w-full rounded-md font-bold text-xs transition-all border-2 flex items-center justify-center relative"
                                    :class="getSidebarClass({{ $index }})">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div
                        class="flex flex-col gap-2 mt-4 pt-4 border-t border-gray-700 text-[10px] text-gray-400 font-medium">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-indigo-500 rounded-[3px]"></div> Şu anki soru
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-emerald-500/20 border border-emerald-500/50 rounded-[3px]"></div> Doğru
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-rose-500/20 border border-rose-500/50 rounded-[3px]"></div> Yanlış
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-700">
                        <button @click="finish()"
                            class="w-full py-2.5 rounded-lg text-xs font-bold text-white bg-gray-700 hover:bg-emerald-600 transition-colors shadow flex items-center justify-center gap-2">
                            <i class="fa-solid fa-flag-checkered"></i> Çalışmayı Bitir
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection