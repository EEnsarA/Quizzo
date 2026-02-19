@extends("layouts.app")

@section("content")

    <div class="flex flex-col h-[calc(100vh-theme(spacing.16))] bg-[#1e1e1e] text-[#cccccc] font-sans overflow-hidden" {{--
        Alpine Başlatma --}} x-data="quizCreate({
                        errors: {{ Js::from($errors->getMessages()) }},
                        token: '{{ csrf_token() }}',
                        allCategories: {{ Js::from($categories) }},
                        selectedCategories: {{ isset($selectedCategories) ? Js::from($selectedCategories) : '[]' }},
                        negativeMarkingEnabled: {{ (isset($quiz) && $quiz->wrong_to_correct_ratio > 0) ? 'true' : 'false' }}
                    })">

        <form id="quiz-create-form" action="{{ isset($quiz) ? route('quiz.update', $quiz->id) : route('quiz.add') }}"
            method="POST" enctype="multipart/form-data" class="flex flex-col h-full">
            @csrf

            {{-- HEADER --}}
            <header
                class="h-16 bg-[#252526] border-b border-[#3e3e42] flex items-center justify-between px-6 shadow-xl z-20 flex-shrink-0 relative">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-700 rounded-lg flex items-center justify-center shadow-lg shadow-green-900/20">
                        <i class="fa-solid fa-layer-group text-white text-lg"></i>
                    </div>
                    <div class="flex flex-col justify-center">
                        {{-- Başlık Dinamik Oldu --}}
                        <span class="text-[10px] font-bold text-green-500 uppercase tracking-widest mb-0.5">
                            {{ isset($quiz) ? 'Quiz Düzenleyici' : 'Quiz Oluşturucu' }}
                        </span>
                        {{-- Input Value Dinamik Oldu --}}
                        <input type="text" name="title" id="title"
                            class="bg-transparent border-b border-transparent hover:border-gray-500 focus:border-green-500 text-white font-bold text-sm focus:outline-none focus:ring-0 w-64 transition-all placeholder-gray-500 py-1"
                            placeholder="Quiz Adı Giriniz" value="{{ old('title', $quiz->title ?? 'Yeni Quiz') }}" required>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @if(!isset($quiz))
                        @auth
                            <button type="button" @click="submitQuiz('{{ route('quiz.ai_generate') }}')"
                                class="relative group px-5 py-2.5 rounded-lg font-bold text-xs text-white overflow-hidden transition-all shadow-lg hover:scale-105 active:scale-95 cursor-pointer">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 animate-gradient-xy opacity-90 group-hover:opacity-100">
                                </div>
                                <div
                                    class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-500 blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200">
                                </div>
                                <div class="relative flex items-center gap-2">
                                    <i class="fa-solid fa-wand-magic-sparkles text-yellow-300 group-hover:animate-pulse"></i>
                                    AI ile Otomatik Üret
                                </div>
                            </button>
                        @else
                            <button type="button" @click="$dispatch('notify', { message: 'AI için giriş yapın', type: 'error' })"
                                class="px-4 py-2 bg-[#2d2d30] border border-gray-600 text-gray-500 rounded-lg text-xs font-bold flex items-center gap-2 cursor-not-allowed">
                                <i class="fa-solid fa-lock"></i> AI ile Üret
                            </button>
                        @endauth
                        <div class="h-8 w-px bg-[#3e3e42]"></div>
                    @endif

                    {{-- Kaydet Butonu: submitQuiz(null) diyerek formun action'ını kullanır --}}
                    <button type="button" @click="submitQuiz(null)"
                        class="px-6 py-2.5 bg-[#2d2d30] hover:bg-emerald-600 border border-emerald-600/30 hover:border-emerald-500 text-white rounded-lg text-xs font-bold shadow-md transition-all flex items-center gap-2 group cursor-pointer">
                        <span class="group-hover:hidden text-emerald-500"><i class="fa-solid fa-check"></i></span>
                        <span class="hidden group-hover:inline"><i class="fa-solid fa-check"></i></span>
                        {{ isset($quiz) ? 'Güncelle ve İlerle' : 'Oluştur ve İlerle' }}
                    </button>
                </div>
            </header>

            {{-- MAIN CONTENT --}}
            <main class="flex-1 overflow-y-auto p-6 md:p-8 bg-[#3d3d3d]">
                <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">

                    {{-- SOL KOLON --}}
                    <div class="lg:col-span-8 space-y-6">
                        <div
                            class="bg-[#252526] border border-[#3e3e42] border-l-4 border-l-emerald-500 rounded-r-lg p-6 shadow-lg">
                            <h3 class="text-sm font-bold text-white uppercase mb-4 flex items-center gap-2">
                                <span class="bg-emerald-500/20 text-emerald-400 p-1.5 rounded"><i
                                        class="fa-solid fa-sliders"></i></span>
                                Quiz Detayları
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                {{-- Konu --}}
                                <div class="group">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-emerald-500 transition-colors">KONU
                                        (Kısa Başlık)</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-tag absolute left-3 top-3 text-gray-600 text-xs"></i>
                                        {{-- Value Dinamik --}}
                                        <input type="text" name="subject"
                                            class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-emerald-500 rounded-lg py-2.5 pl-8 text-sm text-white focus:outline-none focus:ring-0 transition-all placeholder-gray-600"
                                            placeholder="Örn: Tarih, Matematik"
                                            value="{{ old('subject', $quiz->subject ?? '') }}">
                                    </div>
                                    <template x-if="hasError('subject')">
                                        <p class="text-red-400 text-[10px] mt-1" x-text="getError('subject')"></p>
                                    </template>
                                </div>

                                {{-- Zorluk --}}
                                <div class="group">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-emerald-500 transition-colors">ZORLUK
                                        SEVİYESİ</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-gauge-high absolute left-3 top-3 text-gray-600 text-xs"></i>
                                        <select name="difficulty"
                                            class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-emerald-500 rounded-lg py-2.5 pl-8 text-sm text-white focus:outline-none focus:ring-0 appearance-none cursor-pointer transition-all">
                                            {{-- Selected Dinamik --}}
                                            <option value="" disabled {{ !isset($quiz) ? 'selected' : '' }}>Seçiniz...
                                            </option>
                                            <option value="easy" {{ (old('difficulty') ?? ($quiz->difficulty->value ?? '')) == 'easy' ? 'selected' : '' }}>Kolay (Easy)</option>
                                            <option value="medium" {{ (old('difficulty') ?? ($quiz->difficulty->value ?? '')) == 'medium' ? 'selected' : '' }}>Orta (Medium)</option>
                                            <option value="hard" {{ (old('difficulty') ?? ($quiz->difficulty->value ?? '')) == 'hard' ? 'selected' : '' }}>Zor (Hard)</option>
                                            <option value="expert" {{ (old('difficulty') ?? ($quiz->difficulty->value ?? '')) == 'expert' ? 'selected' : '' }}>Uzman (Expert)</option>
                                        </select>
                                        <div class="absolute right-3 top-3 pointer-events-none text-gray-500"><i
                                                class="fa-solid fa-chevron-down text-xs"></i></div>
                                    </div>
                                    <template x-if="hasError('difficulty')">
                                        <p class="text-red-400 text-[10px] mt-1" x-text="getError('difficulty')"></p>
                                    </template>
                                </div>

                                {{-- KATEGORİLER (Arama Özellikli) --}}
                                <div class="md:col-span-2 group">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-emerald-500 transition-colors">KATEGORİLER</label>

                                    <template x-for="catId in selectedCategories" :key="catId">
                                        <input type="hidden" name="categories[]" :value="catId">
                                    </template>

                                    <div
                                        class="bg-[#1e1e1e] border border-[#3e3e42] focus-within:border-emerald-500 rounded-lg p-3 transition-all">

                                        {{-- Seçili Olanlar --}}
                                        <div class="flex flex-wrap gap-2 mb-2" x-show="selectedCategories.length > 0">
                                            <template x-for="catId in selectedCategories" :key="catId">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-emerald-600/20 text-emerald-400 border border-emerald-500/30">
                                                    <span x-text="getCategoryName(catId)"></span>
                                                    <button type="button" @click="toggleCategory(catId)"
                                                        class="ml-2 hover:text-white focus:outline-none">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </span>
                                            </template>
                                        </div>

                                        {{-- Arama --}}
                                        <div class="relative mb-2">
                                            <i
                                                class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-600 text-xs"></i>
                                            <input type="text" x-model="categorySearch"
                                                class="w-full bg-[#252526] border border-[#3e3e42] rounded px-8 py-1.5 text-xs text-white focus:outline-none placeholder-gray-600"
                                                placeholder="Kategori ara... (Örn: Tarih)">
                                            <button type="button" x-show="categorySearch.length > 0"
                                                @click="categorySearch = ''"
                                                class="absolute right-3 top-2.5 text-gray-500 hover:text-white">
                                                <i class="fa-solid fa-times text-xs"></i>
                                            </button>
                                        </div>

                                        {{-- Liste --}}
                                        <div class="max-h-32 overflow-y-auto custom-scrollbar">
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                <template x-for="cat in filteredCategories" :key="cat.id">
                                                    <button type="button" @click="toggleCategory(cat.id)"
                                                        class="px-2 py-1.5 rounded text-[10px] font-medium transition-all border text-left truncate flex items-center justify-between group/btn"
                                                        :class="selectedCategories.includes(cat.id) 
                                                                        ? 'bg-emerald-900/30 border-emerald-500 text-emerald-400' 
                                                                        : 'bg-[#2d2d30] border-gray-700 text-gray-400 hover:border-gray-500 hover:text-gray-200'">
                                                        <span x-text="cat.name"></span>
                                                        <i x-show="selectedCategories.includes(cat.id)"
                                                            class="fa-solid fa-check text-xs"></i>
                                                        <i x-show="!selectedCategories.includes(cat.id)"
                                                            class="fa-solid fa-plus text-xs opacity-0 group-hover/btn:opacity-100 transition-opacity"></i>
                                                    </button>
                                                </template>
                                                <div x-show="filteredCategories.length === 0"
                                                    class="col-span-full text-center py-2 text-gray-600 text-xs italic">
                                                    "<span x-text="categorySearch"></span>" ile eşleşen kategori bulunamadı.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Açıklama --}}
                                <div class="md:col-span-2 group">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-emerald-500 transition-colors">AÇIKLAMA</label>
                                    <textarea name="description" rows="2"
                                        class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-emerald-500 rounded-lg p-3 text-sm text-white focus:outline-none focus:ring-0 transition-all placeholder-gray-600 resize-none"
                                        placeholder="Quiz hakkında öğrencilere gösterilecek kısa bilgi...">{{ old('description', $quiz->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- AI Bölümü (Sadece Yeni Oluştururken Göster) --}}
                        @if(!isset($quiz))
                            <div
                                class="bg-[#252526] border border-[#3e3e42] border-l-4 border-l-indigo-500 rounded-r-lg p-6 shadow-lg relative overflow-hidden group">
                                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                    <i class="fa-solid fa-robot text-9xl text-indigo-500 transform rotate-12"></i>
                                </div>
                                <h3 class="text-sm font-bold text-white uppercase mb-4 flex items-center gap-2 relative z-10">
                                    <span class="bg-indigo-500/20 text-indigo-400 p-1.5 rounded"><i
                                            class="fa-solid fa-microchip"></i></span>
                                    AI & Kaynak Materyaller
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                                    <div>
                                        <p class="text-xs text-gray-400 mb-3 leading-relaxed">
                                            Quiz sorularını otomatik üretmek için bir kaynak yükleyin. <span
                                                class="text-indigo-400">PDF, DOCX veya TXT</span> formatları desteklenir.
                                        </p>
                                        <div class="relative group">
                                            <label
                                                class="flex items-center gap-3 px-4 py-3 bg-[#1e1e1e] border border-dashed border-gray-600 hover:border-indigo-500 rounded-lg cursor-pointer transition-colors">
                                                <div
                                                    class="w-10 h-10 rounded bg-[#252526] flex items-center justify-center group-hover:bg-indigo-500/20 transition-colors flex-shrink-0">
                                                    <i
                                                        class="fa-solid fa-file-pdf text-gray-400 group-hover:text-indigo-400 text-lg"></i>
                                                </div>
                                                <div class="flex-1 overflow-hidden">
                                                    <p class="text-xs font-bold text-white truncate"
                                                        x-text="sourceFileName || 'Dosya Seçmek İçin Tıkla'"></p>
                                                    <p class="text-[10px] text-gray-500 truncate mt-0.5"
                                                        x-text="sourceFileName ? 'Değiştirmek için tıkla' : 'PDF, DOCX veya TXT'">
                                                    </p>
                                                </div>
                                                <input type="file" name="source_file" accept=".pdf,.docx,.txt" class="hidden"
                                                    @change="setSourceFile($event)">
                                            </label>
                                            <button type="button" x-show="sourceFileName"
                                                @click="sourceFileName = null; document.querySelector('input[name=source_file]').value = ''"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] shadow-sm hover:scale-110 transition z-10">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                            <div x-show="sourceFileName" x-transition
                                                class="mt-2 flex items-center justify-center gap-1.5 text-[10px] text-green-500 bg-green-500/10 px-2 py-1.5 rounded border border-green-500/20">
                                                <i class="fa-solid fa-circle-check"></i>
                                                <span>Döküman Analize Hazır</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="bg-indigo-500/5 border border-indigo-500/10 rounded-lg p-3 flex gap-3 items-start">
                                        <i class="fa-solid fa-circle-info text-indigo-400 mt-1"></i>
                                        <div>
                                            <h4 class="text-xs font-bold text-indigo-300 mb-1">Nasıl Çalışır?</h4>
                                            <p class="text-[10px] text-gray-400">
                                                Döküman yükleyip yukarıdaki <strong class="text-white">"AI ile Üret"</strong>
                                                butonuna basarsanız, sistem bu dosyayı analiz eder ve konuyla ilgili sorular
                                                çıkarır. Manuel oluşturacaksanız bu alanı boş bırakabilirsiniz.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- SAĞ KOLON --}}
                    <div class="lg:col-span-4 space-y-6">
                        <div
                            class="bg-[#252526] border border-[#3e3e42] border-l-4 border-l-orange-500 rounded-r-lg p-5 shadow-lg">
                            <h3 class="text-sm font-bold text-white uppercase mb-4 flex items-center gap-2">
                                <span class="bg-orange-500/20 text-orange-400 p-1.5 rounded"><i
                                        class="fa-solid fa-wrench"></i></span>
                                Ayarlar
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-gray-400">Soru Sayısı</label>
                                    <input type="number" name="number_of_questions"
                                        class="w-16 bg-[#1e1e1e] border border-[#3e3e42] focus:border-orange-500 rounded p-1.5 text-center text-sm text-white focus:outline-none focus:ring-0"
                                        min="4" max="50"
                                        value="{{ old('number_of_questions', $quiz->number_of_questions ?? 5) }}">
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-gray-400">Seçenek Sayısı</label>
                                    <input type="number" name="number_of_options"
                                        class="w-16 bg-[#1e1e1e] border border-[#3e3e42] focus:border-orange-500 rounded p-1.5 text-center text-sm text-white focus:outline-none focus:ring-0"
                                        min="2" max="5"
                                        value="{{ old('number_of_options', $quiz->number_of_options ?? 4) }}">
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-gray-400">Süre (Dk)</label>
                                    <input type="number" name="duration_minutes"
                                        class="w-16 bg-[#1e1e1e] border border-[#3e3e42] focus:border-orange-500 rounded p-1.5 text-center text-sm text-white focus:outline-none focus:ring-0"
                                        min="1" max="180"
                                        value="{{ old('duration_minutes', $quiz->duration_minutes ?? 15) }}">
                                </div>
                                <div class="pt-4 border-t border-[#3e3e42]">
                                    <label class="flex items-center gap-2 cursor-pointer mb-2">
                                        <input type="checkbox" x-model="negativeMarkingEnabled"
                                            class="w-4 h-4 rounded bg-[#1e1e1e] border-gray-600 text-orange-500 focus:ring-0">
                                        <span class="text-xs text-gray-300">Yanlış doğruyu götürsün</span>
                                    </label>
                                    <div x-show="negativeMarkingEnabled" x-transition
                                        class="flex items-center justify-between bg-[#1e1e1e] p-2 rounded border border-[#3e3e42]">
                                        <span class="text-[10px] text-gray-500">Kaç Yanlış?</span>
                                        <input type="number" name="wrong_to_correct_ratio"
                                            :disabled="!negativeMarkingEnabled"
                                            :value="!negativeMarkingEnabled ? '0' : '{{ old('wrong_to_correct_ratio', $quiz->wrong_to_correct_ratio ?? 4) }}'"
                                            class="w-12 bg-transparent border-none text-right text-xs text-white focus:ring-0 p-0"
                                            min="1" max="10">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-[#252526] border border-[#3e3e42] border-l-4 border-l-blue-500 rounded-r-lg p-5 shadow-lg h-52 flex flex-col">
                            <h3 class="text-sm font-bold text-white uppercase mb-2 flex items-center gap-2">
                                <span class="bg-blue-500/20 text-blue-400 p-1.5 rounded"><i
                                        class="fa-regular fa-image"></i></span>
                                Kapak Resmi
                            </h3>
                            <label for="dropzone-file"
                                class="flex-1 flex flex-col items-center justify-center border-2 border-[#3e3e42] border-dashed rounded-lg cursor-pointer bg-[#1e1e1e] hover:bg-[#2d2d30] hover:border-blue-500 transition-all relative overflow-hidden group">
                                <div x-show="!fileUrl" class="text-center p-2">
                                    <i
                                        class="fa-solid fa-cloud-arrow-up text-xl text-gray-500 group-hover:text-blue-500 transition-colors mb-1"></i>
                                    <p class="text-[10px] text-gray-400">Resim Yükle</p>
                                </div>
                                <img x-show="fileUrl" :src="fileUrl"
                                    class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-100 transition-opacity">
                                <input id="dropzone-file" type="file" name="img_url" accept="image/*" class="hidden"
                                    @change="fileName = $event.target.files[0]?.name; fileUrl = URL.createObjectURL($event.target.files[0])" />
                            </label>
                            {{-- Mevcut resmi yükle --}}
                            <div
                                x-init="fileUrl = '{{ isset($quiz) && $quiz->img_url ? asset('storage/' . $quiz->img_url) : '' }}'">
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </form>
        <div x-show="showTitleModal" style="display: none;"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm"
            x-transition.opacity>

            <div class="bg-[#252526] w-full max-w-md p-0 rounded-xl border border-gray-700 shadow-2xl transform transition-all overflow-hidden"
                @click.away="showTitleModal = false" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                <div class="bg-[#1e1e1e] p-4 border-b border-gray-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-pen-nib text-green-500"></i> Quiz Adını Belirle
                        </h3>
                        <p class="text-gray-400 text-[10px] mt-1">Devam etmeden önce quize benzersiz bir isim verin.</p>
                    </div>
                    <button @click="showTitleModal = false" class="text-gray-500 hover:text-white transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Quiz Adı <span
                            class="text-red-500">*</span></label>
                    <input type="text" x-model="tempTitle" @keydown.enter.prevent="saveTitleAndContinue()"
                        class="w-full bg-[#1e1e1e] border border-gray-600 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition placeholder-gray-600 font-bold"
                        placeholder="Örn: Vize Hazırlık Testi"
                        x-init="$watch('showTitleModal', value => { if(value) setTimeout(() => $el.focus(), 100) })">

                </div>
                <div class="bg-[#1e1e1e] p-4 border-t border-gray-700 flex justify-end gap-3">
                    <button type="button" @click="showTitleModal = false"
                        class="px-4 py-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition text-sm font-medium">
                        Vazgeç
                    </button>
                    <button type="button" @click="saveTitleAndContinue()"
                        class="px-6 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg transition text-sm font-bold shadow-lg shadow-green-900/20 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-right"></i> Kaydet ve Devam Et
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection