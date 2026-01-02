

{{-- 4.Modals --}}

{{-- AI (Küçük Modal) --}}
<div x-show="aiModalOpen" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;" x-transition.opacity>
    <div class="bg-[#252526] w-96 rounded-xl shadow-2xl border border-gray-700 p-5" @click.away="aiModalOpen = false">
        <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2"><i class="fa-solid fa-robot text-indigo-400"></i> Bu Soruyu Doldur</h3>
        
        <div class="space-y-3">
            {{-- 1. Konu --}}
            <div>
                <label class="text-[10px] uppercase font-bold text-gray-500 mb-1 block">Konu Başlığı</label>
                <input type="text" x-model="aiPrompt" class="w-full bg-[#1e1e1e] border border-gray-600 rounded p-2 text-xs text-white focus:border-indigo-500 outline-none">
            </div>
            {{-- 2. Metin --}}
            <div>
                <label class="text-[10px] uppercase font-bold text-gray-500 mb-1 block">Metin Yapıştır</label>
                <textarea x-model="aiContext" rows="2" class="w-full bg-[#1e1e1e] border border-gray-600 rounded p-2 text-xs text-white focus:border-indigo-500 outline-none resize-none"></textarea>
            </div>
            {{-- 3. Dosya --}}
            <div>
                <label class="text-[10px] uppercase font-bold text-gray-500 mb-1 block">Dosya</label>
                <label class="flex items-center gap-2 w-full bg-[#1e1e1e] border border-dashed border-gray-600 rounded p-2 cursor-pointer hover:bg-[#2d2d30]">
                    <i class="fa-solid fa-cloud-arrow-up text-gray-400"></i>
                    <span class="text-xs text-gray-300 truncate" x-text="aiFile ? aiFile.name : 'Dosya Seç'"></span>
                    <input type="file" class="hidden" @change="setFile($event)">
                </label>
            </div>
            {{-- 4. Zorluk --}}
            <div>
                <label class="text-[10px] uppercase font-bold text-gray-500 mb-1 block">Zorluk</label>
                <select x-model="aiDifficulty" class="w-full bg-[#1e1e1e] border border-gray-600 rounded p-2 text-xs text-white outline-none cursor-pointer">
                    <option value="easy">Kolay</option><option value="medium">Orta</option><option value="hard">Zor</option>
                </select>
            </div>
        </div>

        <button @click="generateAiContent()" :disabled="aiLoading"      {{-- Tıklayınca sürüklemeyi engeller --}}
        class="w-full no-drag bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 mt-4 rounded text-xs flex items-center justify-center gap-2 transition disabled:opacity-50">
            <span x-show="!aiLoading">Oluştur</span><span x-show="aiLoading"><i class="fa-solid fa-circle-notch animate-spin"></i></span>
        </button>
    </div>
</div>

{{-- MODAL 2: AI (Büyük Modal) --}}
<div x-show="aiBatchModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm" style="display: none;" x-transition.opacity>
    <div class="bg-[#252526] w-[600px] rounded-xl shadow-2xl border border-gray-700 overflow-hidden" @click.away="aiBatchModalOpen = false">
        <div class="bg-[#1e1e1e] p-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center gap-2"><i class="fa-solid fa-wand-magic-sparkles text-blue-500"></i> AI Soru Sihirbazı</h3>
            <button @click="aiBatchModalOpen = false" class="text-gray-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5">
            {{-- KAYNAKLAR --}}
            <div class="space-y-3">
                <div><label class="block text-xs font-bold text-gray-400 uppercase mb-1">Konu Başlığı</label><input type="text" x-model="aiPrompt" class="w-full bg-[#1e1e1e] border border-gray-600 rounded-lg p-2 text-sm text-white focus:border-blue-500 outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-400 uppercase mb-1">Kaynak Metin</label><textarea x-model="aiContext" rows="3" class="w-full bg-[#1e1e1e] border border-gray-600 rounded-lg p-2 text-sm text-white focus:border-blue-500 outline-none resize-none"></textarea></div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Döküman Yükle</label>
                    <label class="flex items-center gap-3 w-full bg-[#1e1e1e] border border-dashed border-gray-600 rounded-lg p-2 cursor-pointer hover:bg-[#2d2d30]">
                        <div class="w-8 h-8 rounded-full bg-[#333] flex items-center justify-center"><i class="fa-solid fa-cloud-arrow-up text-gray-400"></i></div>
                        <span class="text-xs text-gray-300 truncate" x-text="aiFile ? aiFile.name : 'Dosya seçmek için tıklayın...'"></span>
                        <input type="file" class="hidden" @change="setFile($event)">
                    </label>
                </div>
            </div>
            
            <hr class="border-gray-700">

            {{-- KURALLAR --}}
            <div>
                <div class="flex justify-between items-center mb-3"><label class="text-xs font-bold text-gray-400 uppercase">Üretim Kuralları</label><button @click="addAiRequest()" class="text-xs text-blue-400 font-bold hover:underline">+ Kural Ekle</button></div>
                <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                    <template x-for="(req, idx) in aiRequests" :key="idx">
                        <div class="bg-[#1e1e1e] p-2 rounded border border-gray-700 flex flex-col gap-2">
                            <div class="flex gap-2 items-center">
                                <input type="number" x-model="req.count" min="1" max="20" class="w-12 bg-[#2d2d30] border border-gray-600 rounded text-center text-xs text-white p-1.5 outline-none">
                                <select x-model="req.type" class="flex-1 bg-[#2d2d30] border border-gray-600 rounded text-xs text-white p-1.5 outline-none cursor-pointer"><option value="multiple_choice">Çoktan Seçmeli</option><option value="open_ended">Klasik</option><option value="true_false">Doğru/Yanlış</option><option value="fill_in_blanks">Boşluk Doldurma</option></select>
                                <select x-model="req.difficulty" class="w-24 bg-[#2d2d30] border border-gray-600 rounded text-xs text-white p-1.5 outline-none cursor-pointer"><option value="easy">Kolay</option><option value="medium">Orta</option><option value="hard">Zor</option></select>
                                <button @click="removeAiRequest(idx)" class="text-gray-500 hover:text-red-400 px-1"><i class="fa-solid fa-trash text-xs"></i></button>
                            </div>
                            <div x-show="req.type === 'multiple_choice'" class="flex items-center gap-2 pl-1"><span class="text-[10px] text-gray-500">Seçenek Sayısı:</span><select x-model="req.option_count" class="bg-[#2d2d30] border border-gray-600 rounded text-[10px] text-white p-0.5 cursor-pointer"><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div>
                        </div>
                    </template>
                </div>
            </div>
            <button @click="generateBatchAi()" :disabled="aiLoading" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2 disabled:opacity-50"><span x-show="!aiLoading">Oluştur ve Havuza Ekle</span><span x-show="aiLoading"><i class="fa-solid fa-circle-notch animate-spin"></i></span></button>
        </div>
    </div>
</div>