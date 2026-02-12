<aside class="absolute right-0 top-0 h-full w-72 bg-gray-800 border-l border-gray-700 flex flex-col z-30 shadow-2xl"
    style="display: none;" x-show="selectedItem" x-transition:enter="transform transition ease-in-out duration-300"
    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full">

    <div class="p-4 border-b border-gray-700 flex justify-between items-center">
        <h3 class="font-bold text-sm text-gray-300">ÖZELLİKLER</h3>
        <button @click="deselect()" class="text-gray-500 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="p-4 space-y-6 overflow-y-auto custom-scrollbar">
        <template x-if="selectedItem">
            <div class="space-y-4">

                <div class="space-y-3 pb-4 border-b border-gray-700">
                    <p class="text-xs font-bold text-blue-400 flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square"></i> İÇERİK DÜZENLE
                    </p>

                    <template x-if="selectedItem.type === 'header_block'">
                        <div class="space-y-2">
                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-bold">Üniversite</label>
                                <input type="text" x-model="selectedItem.content.title"
                                    class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white focus:border-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-bold">Fakülte / Bölüm</label>
                                <input type="text" x-model="selectedItem.content.faculty"
                                    class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white focus:border-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-bold">Dönem</label>
                                <input type="text" x-model="selectedItem.content.term"
                                    class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white focus:border-blue-500 outline-none">
                            </div>
                        </div>
                    </template>

                    <template
                        x-if="['multiple_choice', 'open_ended', 'true_false', 'fill_in_blanks'].includes(selectedItem.type)">
                        <div class="space-y-3">
                            <div class="flex gap-2">
                                <div class="w-1/4">
                                    <label class="text-[10px] text-gray-500 uppercase font-bold">No</label>
                                    <input type="text" x-model="selectedItem.content.number"
                                        class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white text-center">
                                </div>
                                <div class="w-3/4">
                                    <label class="text-[10px] text-gray-500 uppercase font-bold">Puan</label>
                                    <input type="text" x-model="selectedItem.content.point"
                                        class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white"
                                        placeholder="Örn: 10">
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="selectedItem.type === 'multiple_choice'">
                        <div class="space-y-3 mt-2">
                            <div
                                class="flex justify-between items-center bg-gray-700/30 p-2 rounded border border-gray-700">
                                <label class="text-[10px] text-gray-400 uppercase font-bold">Şık Sayısı</label>
                                <div class="flex items-center gap-3">
                                    <button
                                        @click="if(selectedItem.content.options.length > 2) { selectedItem.content.options.pop(); $nextTick(() => autoResize({target: document.getElementById(selectedItem.id)}, selectedItem)); }"
                                        class="w-6 h-6 rounded bg-gray-600 hover:bg-red-500 text-white flex items-center justify-center transition">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>

                                    <span class="text-sm font-bold text-white w-4 text-center"
                                        x-text="selectedItem.content.options.length"></span>

                                    <button
                                        @click="if(selectedItem.content.options.length < 10) { selectedItem.content.options.push(''); $nextTick(() => autoResize({target: document.getElementById(selectedItem.id)}, selectedItem)); }"
                                        class="w-6 h-6 rounded bg-gray-600 hover:bg-green-500 text-white flex items-center justify-center transition">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="text-[10px] text-gray-500 uppercase font-bold">X</label><input type="number"
                            x-model="selectedItem.x"
                            class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white">
                    </div>
                    <div><label class="text-[10px] text-gray-500 uppercase font-bold">Y</label><input type="number"
                            x-model="selectedItem.y"
                            class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white">
                    </div>
                    <div><label class="text-[10px] text-gray-500 uppercase font-bold">Genişlik</label><input
                            type="number" x-model="selectedItem.w"
                            class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white">
                    </div>
                    <div><label class="text-[10px] text-gray-500 uppercase font-bold">Yükseklik</label><input
                            type="number" x-model="selectedItem.h"
                            class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white">
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-gray-700">
                    <p class="text-xs font-bold text-blue-400">YAZI AYARLARI</p>
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Font Boyutu</label>
                        <input type="range" x-model="selectedItem.styles.fontSize" min="8" max="72"
                            class="w-full h-1 bg-gray-600 rounded-lg appearance-none cursor-pointer">
                        <div class="text-right text-xs text-gray-400" x-text="selectedItem.styles.fontSize + 'px'">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="selectedItem.styles.color"
                            class="w-8 h-8 rounded cursor-pointer bg-transparent border-none" title="Yazı Rengi">
                        <button
                            @click="selectedItem.styles.fontWeight = (selectedItem.styles.fontWeight == 'bold' ? 'normal' : 'bold')"
                            class="p-2 rounded bg-gray-700 hover:bg-gray-600 text-white cursor-pointer"><i
                                class="fa-solid fa-bold"></i></button>
                        <button @click="selectedItem.styles.textAlign = 'left'"
                            class="p-2 rounded bg-gray-700 hover:bg-gray-600 text-white cursor-pointer"><i
                                class="fa-solid fa-align-left"></i></button>
                        <button @click="selectedItem.styles.textAlign = 'center'"
                            class="p-2 rounded bg-gray-700 hover:bg-gray-600 text-white cursor-pointer"><i
                                class="fa-solid fa-align-center"></i></button>
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-gray-700">
                    <p class="text-xs font-bold text-blue-400">KUTU AYARLARI</p>
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Arka Plan</label>
                        <div class="flex items-center gap-2">
                            <input type="color"
                                :value="selectedItem.styles.backgroundColor === 'transparent' ? '#ffffff' : selectedItem.styles.backgroundColor"
                                @input="selectedItem.styles.backgroundColor = $event.target.value"
                                class="w-8 h-8 rounded cursor-pointer bg-transparent border-none">
                            <button @click="selectedItem.styles.backgroundColor = 'transparent'"
                                class="text-xs text-red-400 hover:underline cursor-pointer">Şeffaf</button>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Çerçeve</label>
                        <div class="flex gap-2 items-center">
                            <input type="number" x-model="selectedItem.styles.borderWidth"
                                class="w-16 bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white"
                                placeholder="px">
                            <input type="color" x-model="selectedItem.styles.borderColor"
                                class="w-8 h-8 rounded cursor-pointer bg-transparent border-none">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Z-Index (Katman)</label>
                        <div class="flex gap-2">
                            <button @click="selectedItem.styles.zIndex++"
                                class="flex-1 bg-gray-700 py-1 rounded text-xs hover:bg-gray-600">Öne</button>
                            <button @click="selectedItem.styles.zIndex--"
                                class="flex-1 bg-gray-700 py-1 rounded text-xs hover:bg-gray-600">Arkaya</button>
                        </div>
                    </div>
                </div>

            </div>
        </template>
    </div>
</aside>