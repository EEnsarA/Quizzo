@extends("layouts.app")
@props(['myQuizzos','libraryQuizzos'])
@section("content")

<div class="min-h-screen w-full p-4 md:p-8">
    <div class="max-w-7xl mx-auto text-[#F2EDE4]">
        
      
        <div class="flex items-center justify-between mb-8 border-b border-gray-700 pb-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Kütüphanem</h1>
                <p class="text-gray-400 mt-2">Çözdüğün ve oluşturduğun tüm quizler burada.</p>
            </div>
          
            <a href="{{ route('quiz.create') }}" class="hidden md:flex items-center bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition font-semibold">
                <i class="fa-solid fa-plus mr-2"></i> Yeni Quiz
            </a>
        </div>

      
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-6 flex items-center">
                <i class="fa-solid fa-bookmark text-blue-500 mr-3"></i> Kayıtlı Quizler
            </h2>

            @if($libraryQuizzos->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($libraryQuizzos as $quiz)
                        <x-quiz_library_card :quiz="$quiz"/>
                    @endforeach
                </div>
            @else
              
                <div class="flex flex-col items-center justify-center bg-gray-800/50 rounded-2xl p-12 border-2 border-dashed border-gray-700 text-center">
                    <div class="bg-gray-700 p-4 rounded-full mb-4">
                        <i class="fa-regular fa-folder-open text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-300">Henüz kayıtlı bir quiz yok</h3>
                    <p class="text-gray-500 mt-2 max-w-md">Başkalarının hazırladığı quizleri kütüphanene ekleyerek burada görebilirsin.</p>
                    <a href="{{ route('home') }}" class="mt-6 text-blue-400 hover:text-blue-300 font-semibold transition">
                        Quizleri Keşfet <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @endif
        </div>

  
        <div>
            <h2 class="text-2xl font-semibold mb-6 flex items-center">
                <i class="fa-solid fa-pen-nib text-green-500 mr-3"></i> Oluşturduklarım
            </h2>

            @if($myQuizzos->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($myQuizzos as $quiz)
                        <x-quiz_library_card :quiz="$quiz" :myQuiz="true" />
                    @endforeach
                </div>
            @else
            
                <div class="flex flex-col items-center justify-center bg-gray-800/50 rounded-2xl p-12 border-2 border-dashed border-gray-700 text-center">
                    <div class="bg-gray-700 p-4 rounded-full mb-4">
                        <i class="fa-solid fa-wand-magic-sparkles text-4xl text-yellow-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-300">Kendi quizini oluşturmadın</h3>
                    <p class="text-gray-500 mt-2 max-w-md">Bilgini test etmek veya başkalarıyla paylaşmak için hemen bir quiz oluştur.</p>
                    <a href="{{ route('quiz.create') }}" class="mt-6 bg-white text-gray-900 hover:bg-gray-200 px-6 py-3 rounded-lg font-bold transition">
                        İlk Quizini Oluştur
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>

@endsection