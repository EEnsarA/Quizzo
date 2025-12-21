@props(['quiz','quiz_result'])

<div class="bg-gray-800 border border-gray-700 rounded-3xl shadow-xl p-6 sticky top-6">
    
   
    <div class="bg-gray-900 rounded-2xl p-4 mb-6 border border-gray-700 text-center">
        <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Kalan Süre</p>
        <div class="text-4xl font-mono font-bold text-white tracking-wider flex justify-center items-center gap-2">
            <i class="fa-regular fa-clock text-blue-500 animate-pulse text-2xl"></i>
            <span x-text="countdownText"></span>
        </div>
    </div>

   
    <div class="mb-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-bold text-gray-300 uppercase">Soru Gezgini</h3>
            <span class="text-xs text-gray-500">{{ $quiz->questions->count() }} Soru</span>
        </div>


        <div class="grid grid-cols-5 gap-2">
            @foreach ($quiz->questions as $index => $question)
                <button 
                    @click="active = {{$index}}"
                    class="h-10 w-full rounded-lg font-bold text-sm transition-all border-2 flex items-center justify-center relative"
                    :class="{
                        'border-blue-500 bg-blue-500 text-white scale-110 shadow-lg z-10': active == {{ $index }}, // Aktif Soru
                        'border-emerald-600 bg-emerald-900/30 text-emerald-400': answers[{{ $question->id }}] && active != {{ $index }}, // Cevaplanmış Soru (Aktif değil)
                        'border-gray-700 bg-gray-700 text-gray-400 hover:border-gray-500': !answers[{{ $question->id }}] && active != {{ $index }} // Boş Soru
                    }"
                >
                    {{ $index + 1 }}
                    
          
                    <span x-show="answers[{{ $question->id }}]" class="absolute top-1 right-1 w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
                </button>
            @endforeach
        </div>
        
 
        <div class="flex gap-4 mt-3 justify-center text-[10px] text-gray-400">
            <div class="flex items-center gap-1"><div class="w-2 h-2 bg-blue-500 rounded-full"></div> Şu an</div>
            <div class="flex items-center gap-1"><div class="w-2 h-2 bg-emerald-900 border border-emerald-600 rounded"></div> Çözüldü</div>
            <div class="flex items-center gap-1"><div class="w-2 h-2 bg-gray-700 border border-gray-600 rounded"></div> Boş</div>
        </div>
    </div>

  
    <div class="pt-6 border-t border-gray-700">
        <button 
            @click="submitQuiz('{{ route('quiz.check',['quiz' => $quiz , 'quiz_result' => $quiz_result->id]) }}','{{ csrf_token() }}')"
            class="w-full py-4 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white rounded-xl font-bold text-lg shadow-lg shadow-green-900/30 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2"
            onclick="return confirm('Sınavı bitirmek istediğine emin misin?');"
        >
            <i class="fa-solid fa-flag-checkered"></i> Sınavı Bitir
        </button>
        <p class="text-center text-xs text-gray-500 mt-3">
            Bitirdikten sonra cevaplarını değiştiremezsin.
        </p>
    </div>

</div>