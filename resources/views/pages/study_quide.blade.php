@extends("layouts.app")

@section("content")

    <div class="flex flex-col h-[calc(100vh-theme(spacing.16))] bg-[#1e1e1e] text-[#cccccc] font-sans overflow-hidden"
        x-data="studyGuideCreate({
             token: '{{ csrf_token() }}',
             allCategories: {{ Js::from($categories) }},
             selectedCategories: [] ,

             })">

        <form id="study-guide-form" @submit.prevent="submitGuide" class="flex flex-col h-full">
            @csrf

            {{-- HEADER --}}
            <header
                class="h-16 bg-[#252526] border-b border-[#3e3e42] flex items-center justify-between px-6 shadow-xl z-20 flex-shrink-0 relative">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fa-regular fa-file-lines text-xl"></i>
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-0.5">
                            AI ile Üret
                        </span>
                        <h1 class="text-white font-bold text-sm">Çalışma Kağıdı & Özet Çıkarıcı</h1>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Kaydet Butonu --}}
                    <button type="submit" :disabled="isLoading"
                        class="px-6 py-2.5 bg-[#2d2d30] hover:bg-blue-600 border border-blue-600/30 hover:border-blue-500 text-white rounded-lg text-xs font-bold shadow-md transition-all flex items-center gap-2 group cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isLoading" class="text-blue-400 group-hover:text-white transition-colors"><i
                                class="fa-solid fa-bolt"></i></span>
                        <span x-show="isLoading" style="display: none;"><i
                                class="fa-solid fa-spinner animate-spin"></i></span>
                        <span x-text="isLoading ? 'AI Analiz Ediyor...' : 'Oluştur ve İlerle'"></span>
                    </button>
                </div>
            </header>

            {{-- MAIN CONTENT --}}
            <main class="flex-1 overflow-y-auto p-6 md:p-8 bg-[#3d3d3d]">
                <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">

                    {{-- SOL KOLON: İçerik Yükleme --}}
                    <div class="lg:col-span-8 space-y-6">
                        <div
                            class="bg-[#252526] border border-[#3e3e42] border-l-4 border-l-blue-500 rounded-r-lg p-6 shadow-lg relative overflow-hidden group/card">

                            {{-- Arka plan silüeti --}}
                            <div
                                class="absolute top-0 right-0 p-4 opacity-5 group-hover/card:opacity-10 transition-opacity pointer-events-none">
                                <i class="fa-solid fa-microchip text-9xl text-blue-500 transform rotate-12"></i>
                            </div>

                            <h3 class="text-sm font-bold text-white uppercase mb-4 flex items-center gap-2 relative z-10">
                                <span class="bg-indigo-500/20 text-blue-400 p-1.5 rounded"><i
                                        class="fa-solid fa-file-import"></i></span>
                                Kaynak Materyal
                            </h3>

                            <div class="space-y-6 relative z-10">
                                {{-- Dosya Yükleme Alanı --}}
                                <div class="group">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-indigo-500 transition-colors">DOSYA
                                        YÜKLE</label>
                                    <label for="file-upload"
                                        class="flex flex-col items-center justify-center border-2 border-[#3e3e42] border-dashed rounded-lg cursor-pointer bg-[#1e1e1e] hover:bg-[#2d2d30] hover:border-indigo-500 transition-all p-8 relative overflow-hidden group/drop">

                                        <div x-show="!fileName" class="text-center p-2">
                                            <i
                                                class="fa-solid fa-cloud-arrow-up text-3xl text-gray-500 group-hover/drop:text-indigo-500 transition-colors mb-2"></i>
                                            <p class="text-sm text-gray-300 font-bold mb-1">Tıkla veya Sürükle</p>
                                            <p class="text-[10px] text-gray-500">PDF veya TXT (Maks 10MB)</p>
                                        </div>

                                        {{-- Dosya seçilince gösterilecek alan --}}
                                        <div x-show="fileName" style="display: none;" class="text-center p-2">
                                            <i class="fa-solid fa-file-pdf text-4xl text-indigo-500 mb-2"></i>
                                            <p class="text-sm text-indigo-400 font-bold truncate max-w-[250px]"
                                                x-text="fileName"></p>
                                            <p class="text-[10px] text-gray-500 mt-1">Değiştirmek için tekrar tıkla</p>
                                        </div>

                                        <input id="file-upload" name="document" type="file" accept=".pdf,.txt"
                                            class="hidden" @change="setFile($event)" />
                                    </label>
                                </div>

                                {{-- Ayırıcı --}}
                                <div class="flex items-center">
                                    <div class="flex-grow border-t border-[#3e3e42]"></div>
                                    <span class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">VEYA
                                        METİN YAPIŞTIR</span>
                                    <div class="flex-grow border-t border-[#3e3e42]"></div>
                                </div>

                                {{-- Manuel Metin Girişi --}}
                                <div class="group">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-indigo-500 transition-colors">MANUEL
                                        İÇERİK</label>
                                    <textarea name="text_content" rows="6"
                                        class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-indigo-500 rounded-lg p-3 text-sm text-white focus:outline-none focus:ring-0 transition-all placeholder-gray-600 resize-none custom-scrollbar"
                                        placeholder="Özetlenecek ders notunu, makaleyi veya uzun metni buraya yapıştırın..."></textarea>
                                </div>

                                {{-- KATEGORİLER (Arama Özellikli) --}}
                                <div class="group mt-4 border-t border-[#3e3e42] pt-4">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-blue-500 transition-colors">KATEGORİLER</label>

                                    <template x-for="catId in selectedCategories" :key="catId">
                                        <input type="hidden" name="categories[]" :value="catId">
                                    </template>

                                    <div
                                        class="bg-[#1e1e1e] border border-[#3e3e42] focus-within:border-blue-500 rounded-lg p-3 transition-all">
                                        {{-- Seçili Olanlar --}}
                                        <div class="flex flex-wrap gap-2 mb-2" x-show="selectedCategories.length > 0">
                                            <template x-for="catId in selectedCategories" :key="catId">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-blue-600/20 text-blue-400 border border-blue-500/30">
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
                                                                ? 'bg-blue-900/30 border-blue-500 text-blue-400' 
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
                            </div>
                        </div>
                    </div>

                    {{-- SAĞ KOLON: Ayarlar --}}
                    <div class="lg:col-span-4 space-y-6">
                        <div
                            class="bg-[#252526] border border-[#3e3e42] border-l-4 border-l-blue-500 rounded-r-lg p-5 shadow-lg">
                            <h3 class="text-sm font-bold text-white uppercase mb-4 flex items-center gap-2">
                                <span class="bg-blue-500/20 text-blue-400 p-1.5 rounded"><i
                                        class="fa-solid fa-sliders"></i></span>
                                Özet Ayarları
                            </h3>

                            <div class="space-y-5">
                                {{-- Detay Seviyesi --}}
                                <div class="group">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-blue-500 transition-colors">DETAY
                                        SEVİYESİ</label>
                                    <div class="relative">
                                        <i
                                            class="fa-solid fa-layer-group absolute left-3 top-3.5 text-gray-600 text-xs"></i>
                                        <select name="detail_level"
                                            class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-blue-500 rounded-lg py-2.5 pl-8 text-sm text-white focus:outline-none focus:ring-0 appearance-none cursor-pointer transition-all">
                                            <option value="concise">Kısa ve Öz (Madde Madde)</option>
                                            <option value="detailed" selected>Detaylı Anlatım</option>
                                            <option value="exam_prep">Sınav Odaklı (Önemli Yerler)</option>
                                        </select>
                                        <div class="absolute right-3 top-3.5 pointer-events-none text-gray-500"><i
                                                class="fa-solid fa-chevron-down text-xs"></i></div>
                                    </div>
                                </div>

                                {{-- Dil, Zorluk ve Tema (2'li Grid, Sonuncu Tam Satır) --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    {{-- Çıktı Dili --}}
                                    <div class="group">
                                        <label
                                            class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-blue-500 transition-colors">ÇIKTI
                                            DİLİ</label>
                                        <div class="relative">
                                            <i
                                                class="fa-solid fa-language absolute left-3 top-3.5 text-gray-600 text-xs z-10"></i>
                                            <select name="output_language"
                                                class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-blue-500 rounded-lg py-2.5 pl-8 pr-3 text-sm text-white focus:outline-none focus:ring-0 appearance-none cursor-pointer transition-all">
                                                <option value="auto" selected>Orijinal</option>
                                                <option value="Türkçe">Türkçe'ye Çevir</option>
                                                <option value="İngilizce">İngilizce'ye Çevir</option>
                                            </select>
                                            <div class="absolute right-3 top-3.5 pointer-events-none text-gray-500"><i
                                                    class="fa-solid fa-chevron-down text-xs"></i></div>
                                        </div>
                                    </div>

                                    {{-- Anlatım Dili (Zorluk) --}}
                                    <div class="group">
                                        <label
                                            class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-blue-500 transition-colors">ANLATIM
                                            DİLİ</label>
                                        <div class="relative">
                                            <i
                                                class="fa-solid fa-brain absolute left-3 top-3.5 text-gray-600 text-xs z-10"></i>
                                            <select name="tone"
                                                class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-blue-500 rounded-lg py-2.5 pl-8 pr-3 text-sm text-white focus:outline-none focus:ring-0 appearance-none cursor-pointer transition-all">
                                                <option value="standard" selected>Standart</option>
                                                <option
                                                    value="5 yaşındaki bir çocuğun veya konuya hiç hakim olmayan birinin anlayacağı kadar basit ve sade">
                                                    Basitleştirilmiş</option>
                                                <option value="Akademik, profesyonel ve ileri düzey terimler içeren">
                                                    Akademik</option>
                                            </select>
                                            <div class="absolute right-3 top-3.5 pointer-events-none text-gray-500"><i
                                                    class="fa-solid fa-chevron-down text-xs"></i></div>
                                        </div>
                                    </div>

                                    {{-- Görsel Tema (md:col-span-2 ile tam genişlik alır) --}}
                                    <div class="group md:col-span-2">
                                        <label
                                            class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-blue-500 transition-colors">GÖRSEL
                                            TEMA</label>
                                        <div class="relative">
                                            <i
                                                class="fa-solid fa-palette absolute left-3 top-3.5 text-gray-600 text-xs z-10"></i>
                                            <select name="color_theme"
                                                class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-blue-500 rounded-lg py-2.5 pl-8 pr-3 text-sm text-white focus:outline-none focus:ring-0 appearance-none cursor-pointer transition-all">
                                                <option value="bw" selected>Siyah Beyaz (Baskı Dostu)</option>
                                                <option value="colored">Renkli & Emojili</option>
                                            </select>
                                            <div class="absolute right-3 top-3.5 pointer-events-none text-gray-500"><i
                                                    class="fa-solid fa-chevron-down text-xs"></i></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Özel Talimatlar / Odak Noktası --}}
                                <div class="group mt-4">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 mb-1.5 group-focus-within:text-blue-500 transition-colors">ÖZEL
                                        TALİMATLAR</label>
                                    <div class="relative">
                                        <i
                                            class="fa-solid fa-bullseye absolute left-3 top-3.5 text-gray-600 text-xs z-10"></i>
                                        <textarea name="custom_instructions" rows="3"
                                            class="w-full bg-[#1e1e1e] border border-[#3e3e42] focus:border-blue-500 rounded-lg py-2.5 pl-8 pr-3 text-sm text-white focus:outline-none focus:ring-0 transition-all placeholder-gray-600 resize-none custom-scrollbar"
                                            placeholder="Örn: Sadece 3. üniteye odaklan, önemli kişileri maddeler halinde yaz, tarihleri es geç..."></textarea>
                                    </div>
                                </div>


                                {{-- Bilgi Kutusu --}}
                                <div
                                    class="bg-blue-500/5 border border-blue-500/10 rounded-lg p-3 flex gap-3 items-start mt-4">
                                    <i class="fa-solid fa-circle-info text-blue-400 mt-1"></i>
                                    <div>
                                        <h4 class="text-xs font-bold text-blue-300 mb-1">Ne Olacak?</h4>
                                        <p class="text-[10px] text-gray-400 leading-relaxed">
                                            Seçtiğiniz ayara göre yapay zeka dokümanı okuyacak, ana başlıkları bulacak ve
                                            tıpkı bir matbaa dizgisi gibi <span class="text-white">Exam Editor</span>
                                            üzerine yerleştirecektir.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </form>
    </div>

@endsection