@extends("layouts.app")

@props(["papers", "quizzes", "categories", "activeTab" => 'papers'])

@section('content')


    <div class="min-h-screen w-full text-[#F2EDE4]" x-data="libraryHandler({ activeTab: '{{ $activeTab }}' })"
        @trigger-preview.window="openPreview($event.detail.id)" @trigger-download.window="downloadPdf($event.detail.id)">

        <div
            class="relative pt-16 pb-12 px-4 md:px-12 text-center md:text-left overflow-hidden bg-gradient-to-b from-gray-900 via-[#1e1e1e] to-transparent">

            <div
                class="absolute top-0 right-0 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl opacity-20 animate-blob pointer-events-none">
            </div>
            <div
                class="absolute bottom-0 left-0 w-80 h-80 bg-purple-600/10 rounded-full blur-3xl opacity-20 animate-blob animation-delay-2000 pointer-events-none">
            </div>

            <div class="relative z-10 max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-12">


                <div class="w-full md:w-3/5 space-y-6">
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">
                        Bilgini Test Et, <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-green-400">Sınırlarını
                            Zorla.</span>
                    </h1>
                    <p class="text-gray-400 text-lg md:text-xl max-w-2xl">
                        Binlerce hazır içerik arasından seçim yap. İster baskıya hazır sınav kağıdı indir, ister online
                        sınav çöz.
                    </p>

                    <form action="{{ route('home') }}" method="GET" class="relative w-full max-w-xl group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i
                                class="fa-solid fa-magnifying-glass text-gray-500 text-lg group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="block w-full p-4 pl-12 text-sm text-white border border-gray-700 rounded-xl bg-[#252526] focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-500 shadow-lg transition-all"
                            placeholder="Konu, başlık veya içerik ara... (Örn: Matematik Vizesi)">

                        <input type="hidden" name="tab" x-model="activeTab">



                        <button type="submit"
                            class="absolute right-2.5 bottom-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm px-6 py-2 transition shadow-lg shadow-blue-900/20">
                            Ara
                        </button>
                    </form>

                    <div class="flex flex-wrap gap-2 mt-4 text-sm text-gray-500">
                        <span class="font-semibold text-gray-400">Popüler:</span>
                        @foreach($categories->take(5) as $cat)

                            <a href="{{ route('home', ['category' => $cat->id, 'tab' => $activeTab]) }}"
                                class="cursor-pointer hover:text-white hover:underline transition {{ request('category') == $cat->id ? 'text-blue-400 font-bold' : '' }}">
                                #{{ $cat->name }}
                            </a>
                        @endforeach


                        @if(request('category') || request('q'))
                            <a href="{{ route('home') }}"
                                class="text-red-400 hover:text-red-300 ml-2 text-xs flex items-center gap-1">
                                <i class="fa-solid fa-xmark"></i> Temizle
                            </a>
                        @endif
                    </div>
                </div>


                <div class="w-full md:w-2/5 flex flex-col gap-4">

                    <a href="{{ route('exam.create') }}"
                        class="group relative p-6 bg-[#252526] hover:bg-[#2d2d30] rounded-2xl border border-gray-700 hover:border-gray-500 transition-all duration-300 hover:shadow-lg hover:shadow-gray-500/10 cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-white group-hover:text-gray-300 transition">Sınav Kağıdı
                                    Hazırla</h3>
                                <p class="text-sm text-gray-400 mt-1">Sürükle-bırak editör ile tasarla, PDF çıktısı al.</p>
                            </div>
                            <div
                                class="w-12 h-12 bg-gray-800 rounded-lg flex items-center justify-center text-gray-300 group-hover:scale-110 transition border border-gray-700">
                                <i class="fa-regular fa-file-lines text-2xl"></i>
                            </div>
                        </div>
                    </a>


                    <a href="{{ route('quiz.create') }}"
                        class="group relative p-6 bg-[#252526] hover:bg-[#2d2d30] rounded-2xl border border-gray-700 hover:border-green-500 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/10 cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-white group-hover:text-green-400 transition">Online Sınav
                                    Hazırla</h3>
                                <p class="text-sm text-gray-400 mt-1">Link paylaş, süre sınırını koy, sonuçları gör.</p>
                            </div>
                            <div
                                class="w-12 h-12 bg-green-900/30 rounded-lg flex items-center justify-center text-green-400 group-hover:scale-110 transition border border-green-900/50">
                                <i class="fa-solid fa-wifi text-2xl"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>


        <div class="max-w-7xl mx-auto px-4 md:px-12 pb-20 mt-8">

            <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-8 gap-4 border-b border-gray-800 pb-4">


                <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-3 min-w-max">
                        <i class="fa-regular"
                            :class="activeTab === 'papers' ? 'fa-file-lines text-gray-400' : 'fa-circle-play text-green-500'"></i>
                        <span x-text="activeTab === 'papers' ? 'Sınav Kağıtları' : 'Online Sınavlar'"></span>
                    </h2>


                    <div class="bg-[#252526] p-1 rounded-lg flex border border-gray-700 w-fit">

                        <button @click="setTab('papers')"
                            :class="activeTab === 'papers' ? 'bg-[#3e3e42] text-white shadow-sm' : 'text-gray-400 hover:text-white'"
                            class="px-4 py-1.5 rounded-md text-sm font-medium transition-all flex items-center gap-2">
                            <i class="fa-regular fa-file-lines"></i> <span class="hidden sm:inline">Kağıtlar</span>
                        </button>

                        <button @click="setTab('quizzes')"
                            :class="activeTab === 'quizzes' ? 'bg-[#3e3e42] text-white shadow-sm' : 'text-gray-400 hover:text-white'"
                            class="px-4 py-1.5 rounded-md text-sm font-medium transition-all flex items-center gap-2">
                            <i class="fa-solid fa-wifi"></i> <span class="hidden sm:inline">Online</span>
                        </button>
                    </div>
                </div>

                <form x-ref="filterForm" action="{{ route('home') }}" method="GET"
                    class="flex flex-wrap items-center gap-3">

                    <input type="hidden" name="tab" x-model="activeTab">


                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif

                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-filter text-gray-500 text-xs"></i>
                        </div>
                        {{-- @change="submitFilters()" --}}
                        <select name="category" @change="submitFilters()"
                            class="bg-[#252526] border border-gray-700 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-8 p-2.5 cursor-pointer hover:bg-[#2d2d30] transition min-w-[140px]">
                            <option value="">Tüm Kategoriler</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-sort text-gray-500 text-xs"></i>
                        </div>
                        {{-- @change="submitFilters()" --}}
                        <select name="sort" @change="submitFilters()"
                            class="bg-[#252526] border border-gray-700 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-8 p-2.5 cursor-pointer hover:bg-[#2d2d30] transition min-w-[130px]">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>En Yeniler</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>En Popüler</option>
                        </select>
                    </div>
                </form>
            </div>

            {{-- A) PAPER LISTESİ --}}
            <div x-show="activeTab === 'papers'" x-transition.opacity.duration.300ms>
                @if($papers->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($papers as $paper)
                            <x-exam_card_public :paper="$paper" />
                        @endforeach
                    </div>
                    <div class="mt-12">
                        {{ $papers->appends(array_merge(request()->query(), ['tab' => 'papers']))->links() }}
                    </div>
                @else
                    <div class="text-center py-24">
                        <div
                            class="bg-[#252526] w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                            <i class="fa-regular fa-folder-open text-3xl text-gray-500"></i>
                        </div>
                        <h3 class="text-lg text-white font-medium">Bu kriterlere uygun sınav kağıdı yok.</h3>
                        <p class="text-gray-500 text-sm mt-1">Filtreleri değiştirmeyi deneyebilirsin.</p>
                    </div>
                @endif
            </div>

            <div x-show="activeTab === 'quizzes'" x-transition.opacity.duration.300ms style="display: none;">
                @if($quizzes->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($quizzes as $quiz)
                            <x-quiz_card :quiz="$quiz" />
                        @endforeach
                    </div>
                    <div class="mt-12">
                        {{ $quizzes->appends(array_merge(request()->query(), ['tab' => 'quizzes']))->links() }}
                    </div>
                @else
                    <div class="text-center py-24">
                        <div
                            class="bg-[#252526] w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                            <i class="fa-solid fa-link-slash text-3xl text-gray-500"></i>
                        </div>
                        <h3 class="text-lg text-white font-medium">Bu kriterlere uygun online sınav yok.</h3>
                    </div>
                @endif
            </div>

        </div>
        <x-pdf_preview_modal />
    </div>
@endsection