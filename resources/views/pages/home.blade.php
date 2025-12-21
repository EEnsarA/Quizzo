@extends("layouts.app")
@props(["quizzes"])
@section('content')

<div class="min-h-screen w-full">
    

    <div class="relative bg-gradient-to-b from-gray-900 via-gray-800 to-[#0f1011] pt-12 pb-16 px-4 md:px-12 text-center md:text-left overflow-hidden">
        
       
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute top-0 right-40 w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>

        <div class="relative z-10 max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-12">
            
       
            <div class="w-full md:w-3/5 space-y-6">
                <h1 class="text-4xl md:text-6xl font-extrabold text-[#F2EDE4] leading-tight">
                    Bilgini Test Et, <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-green-400">Sınırlarını Zorla.</span>
                </h1>
                <p class="text-gray-400 text-lg md:text-xl max-w-2xl">
                    Binlerce quiz arasından seçim yap, kendi sınavını oluştur veya PDF çıktıları alarak öğrencilerini test et.
                </p>

             
                <div class="relative w-full max-w-xl">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-lg"></i>
                    </div>
                    <input type="text" 
                           class="block w-full p-4 pl-12 text-sm text-gray-100 border border-gray-700 rounded-xl bg-gray-800 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500 shadow-lg" 
                           placeholder="Konu, başlık veya yazar ara... (Örn: Osmanlı Tarihi)">
                    <button type="button" class="absolute right-2.5 bottom-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm px-6 py-2 transition">
                        Ara
                    </button>
                </div>

             
                <div class="flex flex-wrap gap-2 mt-4 text-sm text-gray-400">
                    <span>Popüler:</span>
                    <span class="cursor-pointer hover:text-white hover:underline">#Tarih</span>
                    <span class="cursor-pointer hover:text-white hover:underline">#Yazılım</span>
                    <span class="cursor-pointer hover:text-white hover:underline">#İngilizce</span>
                    <span class="cursor-pointer hover:text-white hover:underline">#Matematik</span>
                </div>
            </div>

          
            <div class="w-full md:w-2/5 flex flex-col gap-4">
                
            
                <a href="{{ route('exam.create') }}" class="group relative p-6 bg-gray-800 rounded-2xl border border-gray-700 hover:border-purple-500 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/20 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white group-hover:text-purple-400 transition">Sınav Kağıdı Hazırla</h3>
                            <p class="text-sm text-gray-400 mt-1">Soruları seç, PDF'e dönüştür, çıktı al.</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-900/50 rounded-lg flex items-center justify-center text-purple-400 group-hover:scale-110 transition">
                            <i class="fa-solid fa-file-pdf text-2xl"></i>
                        </div>
                    </div>
                </a>

        
                <a href="{{ route('quiz.create') }}" class="group relative p-6 bg-gray-800 rounded-2xl border border-gray-700 hover:border-green-500 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/20 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white group-hover:text-green-400 transition">Online Quiz Oluştur</h3>
                            <p class="text-sm text-gray-400 mt-1">İnteraktif sorular, süre sınırı ve anlık sonuçlar.</p>
                        </div>
                        <div class="w-12 h-12 bg-green-900/50 rounded-lg flex items-center justify-center text-green-400 group-hover:scale-110 transition">
                            <i class="fa-solid fa-laptop-code text-2xl"></i>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    </div>

  
    <div class="max-w-7xl mx-auto px-4 md:px-12 py-12">
        
   
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 border-b border-gray-800 pb-4">
            <div>
                <h2 class="text-3xl font-bold text-[#F2EDE4] flex items-center">
                    <i class="fa-solid fa-fire text-orange-500 mr-3"></i> Çokça Çözülenler
                </h2>
                <p class="text-gray-500 mt-1 text-sm">Bu hafta topluluk tarafından en çok ilgi gören quizler.</p>
            </div>

       
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <span class="text-gray-400 text-sm">Sırala:</span>
                <select class="bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    <option>En Popüler</option>
                    <option>En Yeni</option>
                    <option>En Çok Beğenilen</option>
                </select>
            </div>
        </div>

   
        @if($quizzes->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($quizzes as $quiz)
                 
                    <x-quiz_card :quiz="$quiz"/>
                @endforeach
            </div>
        @else
   
            <div class="text-center py-20">
                <i class="fa-solid fa-ghost text-6xl text-gray-700 mb-4"></i>
                <h3 class="text-xl text-gray-400">Henüz hiç popüler quiz yok.</h3>
            </div>
        @endif

        <div class="mt-12 flex justify-center">
      
        </div>

    </div>
</div>
@endsection