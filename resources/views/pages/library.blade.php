@extends("layouts.app")
@props(['myQuizzos','libraryQuizzos','examPapers'])

@section("content")

<div x-data="{ activeTab: 'quizzes' }" class="min-h-screen w-full p-4 md:p-8">
    <div class="max-w-7xl mx-auto text-[#F2EDE4]">
        
        {{-- 1. HEADER --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 pb-4 gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Kütüphanem</h1>
                <p class="text-gray-400 mt-2">Dijital ve basılı tüm içeriklerin tek bir yerde.</p>
            </div>
            
            {{-- Yeni Oluştur Butonları --}}
            <div class="flex gap-3">
                
                {{-- EXAM CREATOR (Indigo - Tab ile Uyumlu) --}}
                <a href="{{ route('exam.create') }}" class="flex items-center bg-[#2d2d30] border border-gray-600 hover:border-indigo-500 hover:text-indigo-400 text-gray-300 px-4 py-2 rounded-lg transition font-semibold text-sm group">
                    <i class="fa-solid fa-file-pen mr-2 text-gray-400 group-hover:text-indigo-400 transition-colors"></i> Exam Creator
                </a>

                {{-- YENİ QUIZ (Yeşil - Tab ile Uyumlu) --}}
                <a href="{{ route('quiz.create') }}" class="flex items-center bg-[#2d2d30] border border-gray-600 hover:border-green-500 hover:text-green-400 text-gray-300 px-4 py-2 rounded-lg transition font-semibold text-sm group">
                    <i class="fa-solid fa-plus mr-2 text-gray-400 group-hover:text-green-400 transition-colors"></i> Yeni Quiz
                </a>

            </div>
        </div>

        {{-- 2. SEKMELER (TABS) --}}
        <div class="mb-8 border-b border-gray-700">
            <nav class="flex gap-6">
                <button @click="activeTab = 'quizzes'" :class="activeTab === 'quizzes' ? 'border-green-500 text-green-400' : 'border-transparent text-gray-400 hover:text-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-layer-group"></i> Online Quizler
                    <span class="bg-gray-800 text-xs py-0.5 px-2 rounded-full ml-1">{{ $myQuizzos->count() + $libraryQuizzos->count() }}</span>
                </button>

                <button @click="activeTab = 'exams'" :class="activeTab === 'exams' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-file-contract"></i> Sınav Kağıtları
                    <span class="bg-gray-800 text-xs py-0.5 px-2 rounded-full ml-1">{{ $examPapers->count() }}</span>
                </button>
            </nav>
        </div>

        {{-- 3. İÇERİK ALANI --}}
        
        {{-- A) ONLINE QUIZLER --}}
        <div x-show="activeTab === 'quizzes'" x-transition.opacity.duration.300ms>
            
            {{-- BÖLÜM 1: OLUŞTURDUKLARIM --}}
            <div class="mb-12">
                <h2 class="text-xl font-semibold mb-6 flex items-center text-gray-300">
                    <i class="fa-solid fa-pen-nib text-green-500 mr-3"></i> Oluşturduklarım
                </h2>
                @if($myQuizzos->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($myQuizzos as $quiz)
                            <x-quiz_library_card :quiz="$quiz" :myQuiz="true" />
                        @endforeach
                    </div>
                @else
                    {{-- 1. EMPTY STATE ENTEGRASYONU --}}
                    <x-empty_state 
                        icon="fa-solid fa-wand-magic-sparkles" 
                        color="text-yellow-500"
                        title="Kendi quizini oluşturmadın" 
                        desc="Bilgini test etmek veya başkalarıyla paylaşmak için hemen bir quiz oluştur."
                        btnText="İlk Quizini Oluştur"
                        btnLink="{{ route('quiz.create') }}"
                        btnClass="bg-white text-gray-900 hover:bg-gray-200"
                    />
                @endif
            </div>

            {{-- BÖLÜM 2: KAYITLI QUİZLER --}}
            <div>
                <h2 class="text-xl font-semibold mb-6 flex items-center text-gray-300">
                    <i class="fa-solid fa-bookmark text-blue-500 mr-3"></i> Kayıtlı Quizler
                </h2>
                @if($libraryQuizzos->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($libraryQuizzos as $quiz)
                            <x-quiz_library_card :quiz="$quiz"/>
                        @endforeach
                    </div>
                @else
                    {{-- 2. EMPTY STATE ENTEGRASYONU --}}
                    <x-empty_state 
                        icon="fa-regular fa-folder-open"
                        color="text-gray-400"
                        title="Henüz kayıtlı bir quiz yok"
                        desc="Başkalarının hazırladığı quizleri kütüphanene ekleyerek burada görebilirsin."
                        btnText="Quizleri Keşfet"
                        btnLink="{{ route('home') }}"
                        btnIcon="fa-solid fa-compass"
                        btnClass="text-blue-400 hover:text-blue-300 hover:bg-blue-900/20 bg-transparent border border-blue-900/50"
                    />
                @endif
            </div>
        </div>

        {{-- B) SINAV KAĞITLARI (EXAM CREATOR) --}}
        <div x-show="activeTab === 'exams'" x-transition.opacity.duration.300ms style="display: none;">
            
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-xl font-semibold flex items-center text-gray-300">
                    <i class="fa-solid fa-file-contract text-indigo-500 mr-3"></i> Tasarımlarım
                </h2>
                <span class="text-xs text-gray-500 bg-gray-800 px-2 py-1 rounded">Baskıya Hazır Format</span>
            </div>

            @if($examPapers->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($examPapers as $paper)
                        <x-exam_card :paper="$paper" />
                    @endforeach
                </div>
            @else
                {{-- 3. EMPTY STATE ENTEGRASYONU --}}
                <x-empty_state 
                    icon="fa-solid fa-file-circle-plus"
                    color="text-indigo-400"
                    title="Henüz sınav kağıdı tasarlamadın"
                    desc="Exam Creator ile sürükle-bırak yöntemini kullanarak, AI destekli ve baskıya hazır profesyonel sınav kağıtları oluşturabilirsin."
                    btnText="Tasarıma Başla"
                    btnLink="{{ route('exam.create') }}"
                    btnClass="bg-indigo-600 hover:bg-indigo-500 text-white"
                />
            @endif
        </div>

    </div>
</div>

@endsection