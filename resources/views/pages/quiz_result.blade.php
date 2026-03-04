@extends("layouts.app")
@props(['result', 'rankings'])

@section("content")

    <div class="min-h-screen  text-gray-100 p-4 md:p-6 lg:p-8 font-sans">

        {{-- Üst Başlık ve Butonlar --}}
        <div class="max-w-7xl mx-auto mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-blue-500 text-lg"></i> Sınav Sonucu
                </h1>
                <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest">Quiz: <span
                        class="text-blue-400">{{ $result->quiz->title }}</span></p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('quiz.show', $result->quiz->slug) }}"
                    class="px-4 py-2 rounded-lg bg-gray-800 text-gray-300 hover:text-white hover:bg-gray-700 text-xs font-bold border border-gray-700 transition-colors flex items-center shadow-sm">
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Tekrar Çöz
                </a>
            </div>
        </div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">

            <div class="lg:col-span-8 space-y-6">

                {{-- Özet Kartı Bileşeni --}}
                <x-quiz_result_summary :result="$result" />

                {{-- Cevap Anahtarı Başlığı --}}
                <div class="flex items-center justify-between border-b border-gray-700 pb-3 mt-8">
                    <h2 class="text-sm font-bold text-gray-200 uppercase tracking-widest flex items-center">
                        <i class="fa-solid fa-list-check mr-2 text-white"></i> Cevap Anahtarı
                    </h2>
                    <div class="flex gap-3 text-[10px] font-bold text-gray-400 uppercase">
                        <span class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></div> Doğru
                        </span>
                        <span class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-rose-500 mr-1.5"></div> Yanlış
                        </span>
                        <span class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-gray-500 mr-1.5"></div> Boş
                        </span>
                    </div>
                </div>

                {{-- Soru İnceleme Listesi --}}
                <div class="space-y-4">
                    @foreach($result->details as $index => $detail)
                        @php
                            $question = $result->quiz->questions->firstWhere('id', $detail['question_id']);
                            $img = 'storage/' . $question->img_url;

                            $cardBorder = 'border-gray-700';
                            $statusIcon = '<i class="fa-regular fa-circle text-gray-500"></i>';
                            $statusText = 'Boş Bırakıldı';
                            $statusColor = 'text-gray-400';

                            if ($detail['is_correct']) {
                                $cardBorder = 'border-emerald-500/50 shadow-[0_0_15px_rgba(16,185,129,0.1)]';
                                $statusIcon = '<i class="fa-solid fa-check-circle text-emerald-500"></i>';
                                $statusText = 'Doğru';
                                $statusColor = 'text-emerald-400';
                            } elseif ($detail['given_answer'] != null) {
                                $cardBorder = 'border-rose-500/50 shadow-[0_0_15px_rgba(244,63,94,0.1)]';
                                $statusIcon = '<i class="fa-solid fa-times-circle text-rose-500"></i>';
                                $statusText = 'Yanlış';
                                $statusColor = 'text-rose-400';
                            }
                        @endphp

                        <div
                            class="bg-gray-800 rounded-xl border {{ $cardBorder }} overflow-hidden transition hover:bg-gray-800/80">

                            {{-- Soru Üst Barı --}}
                            <div
                                class="bg-gray-900/50 px-4 py-2.5 flex justify-between items-center border-b border-gray-700/50">
                                <span class="font-mono font-bold text-xs {{ $statusColor }}">
                                    Soru {{ $index + 1 }}
                                </span>
                                <div
                                    class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wide {{ $statusColor }}">
                                    <span>{{ $statusText }}</span> {!! $statusIcon !!}
                                </div>
                            </div>

                            <div class="p-4 md:p-5">
                                @if ($question->img_url)
                                    <div class="mb-4 rounded-lg overflow-hidden border border-gray-700">
                                        <img src="{{ asset($img) }}" class="w-full h-auto max-h-48 object-contain bg-gray-900">
                                    </div>
                                @endif

                                <h3 class="text-base font-bold text-gray-100 mb-4 leading-relaxed">
                                    {{ $question->question_text }}
                                </h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($question->answers as $ansIndex => $ans)
                                        @php
                                            $optionClass = 'bg-gray-700/30 border-gray-600 text-gray-400';
                                            $icon = '';

                                            if ($ans->id == $detail['correct_answer']) {
                                                $optionClass = 'bg-emerald-900/30 border-emerald-500 text-white ring-1 ring-emerald-500';
                                                $icon = '<i class="fa-solid fa-check text-emerald-500 ml-auto"></i>';
                                            } elseif ($ans->id == $detail['given_answer'] && !$detail['is_correct']) {
                                                $optionClass = 'bg-rose-900/30 border-rose-500 text-rose-200 ring-1 ring-rose-500';
                                                $icon = '<i class="fa-solid fa-xmark text-rose-500 ml-auto"></i>';
                                            }
                                        @endphp

                                        <div class="p-2.5 rounded-lg border {{ $optionClass }} flex items-center gap-3">
                                            <span
                                                class="w-6 h-6 flex items-center justify-center rounded border border-inherit bg-gray-800/50 font-bold text-[10px]">
                                                {{ chr(65 + $ansIndex) }}
                                            </span>
                                            <span class="font-medium text-xs">{{ $ans->answer_text }}</span>
                                            {!! $icon !!}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>


            {{-- Sıralama Sidebar'ı ve Uyarı --}}
            <div class="lg:col-span-4 space-y-4">

                {{-- Sıralamaya Etki Etmedi Uyarısı --}}
                @if(isset($showRankWarning) && $showRankWarning)
                    <div class="bg-orange-900/10 border border-orange-500/30 p-4 rounded-xl flex items-start gap-3 shadow-sm">
                        <i class="fa-solid fa-circle-info text-orange-400 mt-0.5 text-sm"></i>
                        <p class="text-[11px] text-gray-300 leading-relaxed">
                            Bu sınavı daha önce çözdüğünüz (veya size ait olduğu) için bu skorunuz <strong
                                class="text-orange-400">sıralama tablosuna yansıtılmamıştır</strong>.
                        </p>
                    </div>
                @endif

                <div class="sticky top-6">
                    <x-success_rank_sidebar :quiz="$result->quiz" :rankings="$rankings"
                        :current_user_id="$result->user_id ?? $result->session_id" />
                </div>

            </div>

        </div>
    </div>

@endsection