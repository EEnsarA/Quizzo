<aside class="w-72 bg-gray-800 border-l border-gray-700 flex flex-col z-20 transition-all"
           x-show="selectedItem" x-transition:enter="translate-x-full" x-transition:enter-end="translate-x-0">
        
        <div class="p-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="font-bold text-sm text-gray-300">ÖZELLİKLER</h3>
            <button @click="deselect()" class="text-gray-500 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="p-4 space-y-6 overflow-y-auto custom-scrollbar" x-data>
            <template x-if="selectedItem">
                <div class="space-y-4">
                    
                    {{-- Pozisyon ve Boyut --}}
                    <div class="grid grid-cols-2 gap-2">
                        <div><label class="text-[10px] text-gray-500 uppercase font-bold">X</label><input type="number" x-model="selectedItem.x" class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white"></div>
                        <div><label class="text-[10px] text-gray-500 uppercase font-bold">Y</label><input type="number" x-model="selectedItem.y" class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white"></div>
                        <div><label class="text-[10px] text-gray-500 uppercase font-bold">Genişlik</label><input type="number" x-model="selectedItem.w" class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white"></div>
                        <div><label class="text-[10px] text-gray-500 uppercase font-bold">Yükseklik</label><input type="number" x-model="selectedItem.h" class="w-full bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white"></div>
                    </div>

                    {{-- Font Ayarları --}}
                    <div class="space-y-3 pt-4 border-t border-gray-700">
                        <p class="text-xs font-bold text-blue-400">YAZI AYARLARI</p>
                        <div>
                            <label class="text-[10px] text-gray-500 uppercase font-bold">Font Boyutu</label>
                            <input type="range" x-model="selectedItem.styles.fontSize" min="8" max="72" class="w-full h-1 bg-gray-600 rounded-lg appearance-none cursor-pointer">
                            <div class="text-right text-xs text-gray-400" x-text="selectedItem.styles.fontSize + 'px'"></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="color" x-model="selectedItem.styles.color" class="w-8 h-8 rounded cursor-pointer bg-transparent border-none" title="Yazı Rengi">
                            <button @click="selectedItem.styles.fontWeight = (selectedItem.styles.fontWeight == 'bold' ? 'normal' : 'bold')" class="p-2 rounded bg-gray-700 hover:bg-gray-600 text-white cursor-pointer"><i class="fa-solid fa-bold"></i></button>
                            <button @click="selectedItem.styles.textAlign = 'left'" class="p-2 rounded bg-gray-700 hover:bg-gray-600 text-white cursor-pointer"><i class="fa-solid fa-align-left"></i></button>
                            <button @click="selectedItem.styles.textAlign = 'center'" class="p-2 rounded bg-gray-700 hover:bg-gray-600 text-white cursor-pointer"><i class="fa-solid fa-align-center"></i></button>
                        </div>
                    </div>

                    {{-- Kutu Ayarları --}}
                    <div class="space-y-3 pt-4 border-t border-gray-700">
                        <p class="text-xs font-bold text-blue-400">KUTU AYARLARI</p>
                        <div>
                            <label class="text-[10px] text-gray-500 uppercase font-bold">Arka Plan</label>
                            <div class="flex items-center gap-2">
                                    {{-- Inputa :value ile kontrol koyduk. Eğer transparent ise #ffffff göster yoksa rengi göster --}}
                                    <input type="color" 
                                        :value="selectedItem.styles.backgroundColor === 'transparent' ? '#ffffff' : selectedItem.styles.backgroundColor" 
                                        @input="selectedItem.styles.backgroundColor = $event.target.value"
                                        class="w-8 h-8 rounded cursor-pointer bg-transparent border-none">
                                    
                                    <button @click="selectedItem.styles.backgroundColor = 'transparent'" class="text-xs text-red-400 hover:underline cursor-pointer">Şeffaf</button>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-500 uppercase font-bold">Çerçeve</label>
                            <div class="flex gap-2 items-center">
                                <input type="number" x-model="selectedItem.styles.borderWidth" class="w-16 bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white" placeholder="px">
                                <input type="color" x-model="selectedItem.styles.borderColor" class="w-8 h-8 rounded cursor-pointer bg-transparent border-none">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-500 uppercase font-bold">Z-Index (Katman)</label>
                            <div class="flex gap-2">
                                <button @click="selectedItem.styles.zIndex++" class="flex-1 bg-gray-700 py-1 rounded text-xs hover:bg-gray-600">Öne</button>
                                <button @click="selectedItem.styles.zIndex--" class="flex-1 bg-gray-700 py-1 rounded text-xs hover:bg-gray-600">Arkaya</button>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </aside>