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

                        <div class="flex flex-wrap gap-2 mb-3 min-h-[32px]">
                            <template x-for="catId in tempCategories" :key="catId">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-600 text-white shadow-sm animate-fadeIn">
                                    <span x-text="getCategoryName(catId)"></span>
                                    <button @click="toggleCategory(catId)"
                                        class="ml-2 hover:text-blue-200 focus:outline-none">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </span>
                            </template>
                            <span x-show="tempCategories.length === 0" class="text-xs text-gray-600 italic py-1">Henüz
                                kategori seçilmedi.</span>
                        </div>

                        <div
                            class="bg-[#1e1e1e] border border-gray-700 rounded-lg p-3 max-h-40 overflow-y-auto custom-scrollbar">
                            <div class="flex flex-wrap gap-2">
                                @foreach($categories as $cat)
                                    <button @click="toggleCategory({{ $cat->id }})"
                                        class="px-3 py-1.5 rounded-md text-xs font-medium transition-all border select-none"
                                        :class="tempCategories.includes({{ $cat->id }}) 
                                                                ? 'bg-blue-900/30 border-blue-500 text-blue-400 opacity-50 cursor-not-allowed' 
                                                                : 'bg-[#2d2d2d] border-gray-600 text-gray-300 hover:border-gray-400 hover:text-white'">
                                        {{ $cat->name }}
                                    </button>
                                @endforeach
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