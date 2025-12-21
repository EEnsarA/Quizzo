@extends("layouts.app")
@props(['result','rankings'])

@section("content")

<div class="min-h-screen bg-gray-900 text-gray-100 p-4 md:p-8 font-sans">
    
  
    <div class="max-w-7xl mx-auto mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fa-solid fa-chart-pie text-blue-500"></i> Sınav Sonucu
            </h1>
            <p class="text-gray-400 mt-1">Quiz: <span class="text-blue-400">{{ $result->quiz->title }}</span></p>
        </div>
        
        <div class="flex gap-3">
             <a href="{{ route('quiz.show', $result->quiz->slug) }}" class="px-5 py-2.5 rounded-lg bg-gray-800 text-gray-300 hover:bg-gray-700 font-semibold border border-gray-700 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> Quize Dön
            </a>
            <a href="{{ route('quiz.start', $result->quiz) }}" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-500 font-semibold shadow-lg shadow-blue-900/50 transition">
                <i class="fa-solid fa-rotate-right mr-2"></i> Tekrar Çöz
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
    
        <div class="lg:col-span-2 space-y-8">
            
        
            <x-quiz_result_summary :result="$result" />

            <div class="flex items-center justify-between border-b border-gray-700 pb-4">
                <h2 class="text-xl font-bold text-gray-200">
                    <i class="fa-solid fa-list-check mr-2 text-emerald-500"></i> Cevap Anahtarı
                </h2>
                <div class="flex gap-3 text-xs font-semibold">
                    <span class="flex items-center"><div class="w-3 h-3 rounded-full bg-emerald-500 mr-1.5"></div> Doğru</span>
                    <span class="flex items-center"><div class="w-3 h-3 rounded-full bg-rose-500 mr-1.5"></div> Yanlış</span>
                    <span class="flex items-center"><div class="w-3 h-3 rounded-full bg-gray-500 mr-1.5"></div> Boş</span>
                </div>
            </div>

        
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

                <div class="bg-gray-800 rounded-2xl border {{ $cardBorder }} overflow-hidden transition hover:bg-gray-800/80">
                    
                  
                    <div class="bg-gray-900/50 px-6 py-4 flex justify-between items-center border-b border-gray-700/50">
                        <span class="font-mono font-bold text-lg {{ $statusColor }}">
                            Soru {{ $index+1 }}
                        </span>
                        <div class="flex items-center gap-2 text-sm font-semibold {{ $statusColor }}">
                            <span>{{ $statusText }}</span> {!! $statusIcon !!}
                        </div>
                    </div>

                    <div class="p-6">
                    
                        @if ($question->img_url)
                            <div class="mb-6 rounded-xl overflow-hidden border border-gray-700">
                                <img src="{{ asset($img) }}" class="w-full h-auto max-h-64 object-contain bg-gray-900">
                            </div>
                        @endif

            
                        <h3 class="text-xl font-bold text-gray-100 mb-6 leading-relaxed">
                            {{ $question->question_text }}
                        </h3>

                 
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($question->answers as $ansIndex => $ans)
                                @php
                            
                                    $optionClass = 'bg-gray-700/30 border-gray-600 text-gray-400';
                                    $icon = '';

                                    if ($ans->id == $detail['correct_answer']) {
                                        $optionClass = 'bg-emerald-900/30 border-emerald-500 text-white ring-1 ring-emerald-500';
                                        $icon = '<i class="fa-solid fa-check text-emerald-500 ml-auto"></i>';
                                    }
                             
                                    elseif ($ans->id == $detail['given_answer'] && !$detail['is_correct']) {
                                        $optionClass = 'bg-rose-900/30 border-rose-500 text-rose-200 ring-1 ring-rose-500';
                                        $icon = '<i class="fa-solid fa-xmark text-rose-500 ml-auto"></i>';
                                    }
                                @endphp

                                <div class="p-4 rounded-xl border {{ $optionClass }} flex items-center gap-3 transition">
                                    <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-800/50 font-bold text-sm">
                                        {{ chr(65 + $ansIndex) }}
                                    </span>
                                    <span class="font-medium text-sm">{{ $ans->answer_text }}</span>
                                    {!! $icon !!}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

     
        <div>
           
            <x-success_rank_sidebar :quiz="$result->quiz" :rankings="$rankings" :current_user_id="$result->user_id ?? $result->session_id"/>
        </div>

    </div>
</div>

@endsection