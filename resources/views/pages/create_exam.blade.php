@extends("layouts.app")
@props(["examPaper", "categories"])

@section("content")

    <div x-data="examCanvas({{ Js::from([
        'token' => csrf_token(),
        'initialElements' => isset($examPaper) ? $examPaper->canvas_data : [],
        'examTitle' => isset($examPaper) ? $examPaper->title : 'Yeni Sınav Kağıdı',
        'examId' => isset($examPaper) ? $examPaper->id : null, // ID varsa Update 
        'allCategories' => $categories
    ]) }})" class="flex relative w-full h-[calc(100vh-64px)] bg-[#1e1e1e] font-sans overflow-hidden">


        <x-exam_create_sidebar />

        <div class="flex-1 relative h-full min-w-0 transition-[padding] duration-300 ease-in-out"
            :class="{'pr-0': !selectedItem, 'pr-72': selectedItem}">

            <x-exam_create_canvas />
            <x-exam_create_modals />
        </div>

        <x-exam_create_properties />

        <div x-show="showTitleModal"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm"
            x-transition.opacity style="display: none;">

            <div class="bg-[#252526] w-full max-w-lg p-0 rounded-xl border border-gray-700 shadow-2xl transform transition-all overflow-hidden"
                @click.away="showTitleModal = false" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                <div class="bg-[#1e1e1e] p-4 border-b border-gray-700">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-blue-500"></i> Sınavı Kaydet
                    </h3>
                    <p class="text-gray-400 text-xs mt-1">Sınav kağıdın için son ayarları yap.</p>
                </div>

                <div class="p-6 space-y-5">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Başlık <span
                                class="text-red-500">*</span></label>
                        <input type="text" x-model="tempTitle" @keydown.enter="saveTitleAndContinue()"
                            class="w-full bg-[#1e1e1e] border border-gray-600 text-white rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition placeholder-gray-600"
                            placeholder="Örn: Matematik Vizesi">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kategoriler</label>

                        <div
                            class="flex flex-wrap gap-2 mb-2 min-h-[32px] p-2 bg-[#1e1e1e] border border-gray-700 rounded-lg">
                            <template x-for="catId in tempCategories" :key="catId">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-blue-600/20 text-blue-400 border border-blue-500/30">
                                    <span x-text="getCategoryName(catId)"></span>
                                    <button @click="toggleCategory(catId)"
                                        class="ml-2 hover:text-blue-200 focus:outline-none">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </span>
                            </template>
                            <span x-show="tempCategories.length === 0" class="text-xs text-gray-600 italic py-1 pl-1">Henüz
                                kategori seçilmedi.</span>
                        </div>

                        <div class="bg-[#1e1e1e] border border-gray-700 rounded-lg p-3">

                            <div class="relative mb-2">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-600 text-xs"></i>
                                <input type="text" x-model="categorySearch"
                                    class="w-full bg-[#252526] border border-gray-600 rounded px-8 py-1.5 text-xs text-white focus:outline-none focus:border-blue-500 placeholder-gray-600 transition-colors"
                                    placeholder="Kategori ara...">

                                <button x-show="categorySearch.length > 0" @click="categorySearch = ''"
                                    class="absolute right-3 top-2.5 text-gray-500 hover:text-white">
                                    <i class="fa-solid fa-times text-xs"></i>
                                </button>
                            </div>

                            <div class="max-h-40 overflow-y-auto custom-scrollbar">
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="cat in filteredCategories" :key="cat.id">
                                        <button @click="toggleCategory(cat.id)"
                                            class="px-2 py-1.5 rounded text-[10px] font-medium transition-all border select-none text-left truncate flex items-center justify-between group"
                                            :class="tempCategories.includes(cat.id) 
                                ? 'bg-blue-900/30 border-blue-500 text-blue-400' 
                                : 'bg-[#2d2d2d] border-gray-600 text-gray-300 hover:border-gray-500 hover:text-white'">

                                            <span x-text="cat.name"></span>

                                            <i x-show="tempCategories.includes(cat.id)"
                                                class="fa-solid fa-check text-xs"></i>
                                            <i x-show="!tempCategories.includes(cat.id)"
                                                class="fa-solid fa-plus text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                        </button>
                                    </template>

                                    <div x-show="filteredCategories.length === 0"
                                        class="col-span-full text-center py-2 text-gray-600 text-xs italic">
                                        "<span x-text="categorySearch"></span>" bulunamadı.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Açıklama</label>
                        <textarea x-model="tempDescription" rows="2"
                            class="w-full bg-[#1e1e1e] border border-gray-600 text-white rounded-lg px-4 py-2 focus:border-blue-500 outline-none resize-none text-sm placeholder-gray-600"
                            placeholder="Kısa bir not..."></textarea>
                    </div>

                </div>

                <div class="bg-[#1e1e1e] p-4 border-t border-gray-700 flex justify-end gap-3">
                    <button @click="showTitleModal = false"
                        class="px-4 py-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition text-sm font-medium">
                        Vazgeç
                    </button>
                    <button @click="saveTitleAndContinue()"
                        class="relative px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition text-sm font-bold shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2 min-w-[160px]"
                        :disabled="isLoading" :class="{'opacity-75 cursor-not-allowed': isLoading}">

                        <span x-show="!isLoading" class="flex items-center gap-2">
                            <i class="fa-solid fa-check"></i>
                            <span>Kaydet ve Devam Et</span>
                        </span>

                        <span x-show="isLoading" class="flex items-center gap-2" style="display: none;">
                            <i class="fa-solid fa-circle-notch animate-spin"></i>
                            <span>İşleniyor...</span>
                        </span>

                    </button>
                </div>

            </div>
        </div>

        <x-pdf_preview_modal />

    </div>


@endsection