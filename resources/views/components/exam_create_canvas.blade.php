
    {{-- 2. CANVAS --}}
    <main class="flex-1 flex flex-col min-w-0 bg-[#1e1e1e]">
        
        {{-- TOOLBAR / MODE SEÇİCİ --}}
        <header class="h-14 bg-[#252526] border-b border-[#3e3e42] flex justify-between items-center px-4 shadow-md z-10">
            <div class="flex items-center gap-2">
                <input type="text" value="Yeni Sınav Kağıdı" class="bg-transparent border-none text-white font-bold text-sm focus:ring-0 w-48 placeholder-gray-600">
            </div>

            {{-- İmleç Modları --}}
            <div class="flex bg-[#1e1e1e] p-1 rounded-lg border border-[#3e3e42]">
                <button @click="setMode('select')" :class="cursorMode === 'select' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-[#333]'" class="p-2 rounded transition" title="Seç / Taşı"><i class="fa-solid fa-arrow-pointer"></i></button>
                <button @click="setMode('draw')" :class="cursorMode === 'draw' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-[#333]'" class="p-2 rounded transition" title="Kalem"><i class="fa-solid fa-pencil"></i></button>
                <button @click="setMode('shape')" :class="cursorMode === 'shape' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-[#333]'" class="p-2 rounded transition" title="Şekil"><i class="fa-regular fa-square"></i></button>
                <button @click="setMode('text')" :class="cursorMode === 'text' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-[#333]'" class="p-2 rounded transition" title="Yazı"><i class="fa-solid fa-font"></i></button>
            </div>

            <div class="flex items-center gap-2">
                <button @click="saveToConsole()" class="px-3 py-1.5 bg-[#3e3e42] hover:bg-[#4e4e52] text-white rounded text-xs font-medium transition-colors">Kaydet</button>
                <button class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-xs font-medium shadow-lg transition-colors flex items-center gap-2"><i class="fa-solid fa-download"></i> PDF</button>
            </div>
        </header>
        
        {{-- Sayfalama --}}
        <div class="h-12 bg-[#252526] border-b border-[#3e3e42] flex justify-center items-center gap-4 shadow-sm z-20">
            <button @click="setPage(Math.max(1, activePage - 1))" class="text-gray-400 hover:text-white disabled:opacity-30" :disabled="activePage === 1"><i class="fa-solid fa-chevron-left"></i></button>
            <div class="flex items-center gap-2 bg-[#1e1e1e] px-3 py-1 rounded-full border border-[#3e3e42]"><span class="text-xs font-mono text-gray-400">Sayfa</span><span x-text="activePage" class="text-sm font-bold text-white"></span><span class="text-xs text-gray-500">/</span><span x-text="totalPages" class="text-xs text-gray-500"></span></div>
            <button @click="setPage(Math.min(totalPages, activePage + 1))" class="text-gray-400 hover:text-white disabled:opacity-30" :disabled="activePage === totalPages"><i class="fa-solid fa-chevron-right"></i></button>
            <div class="w-px h-4 bg-gray-600 mx-2"></div>
            <button @click="addPage()" class="text-xs text-blue-400 hover:text-blue-300 font-bold flex items-center gap-1"><i class="fa-solid fa-plus"></i> Ekle</button>
            <button @click="deletePage()" class="text-xs text-red-400 hover:text-red-300 font-bold flex items-center gap-1 ml-2" x-show="totalPages > 1"><i class="fa-solid fa-trash"></i> Sil</button>
        </div>

        {{-- CANVAS ALANI --}}
        <div class="flex-1 overflow-y-auto p-8 md:p-12 flex justify-center bg-[#1e1e1e] relative cursor-crosshair">
            
            <div id="paper" 
                 class="bg-white relative shadow-[0_0_50px_rgba(0,0,0,0.5)]  origin-top"
                 style="width: 210mm; min-height: 297mm;"
                 :class="cursorMode === 'select' ? 'cursor-default' : 'cursor-crosshair'"
                 @click.self="deselect()"
                 @dragover.prevent
                 @drop="handleDrop($event)">
                
                <template x-for="item in currentPageElements" :key="item.id">
                    
                    <div :id="item.id"
                         class="absolute group box-border draggable-item"
                         @click="select(item.id)"
                         :style="`
                            transform: translate(${item.x}px, ${item.y}px);
                            width: ${item.w}px;
                            height: ${item.h}px;
                            z-index: ${item.styles.zIndex};
                            border: ${selectedId === item.id ? '2px solid #3b82f6' : (item.styles.borderWidth + 'px solid ' + item.styles.borderColor)};
                            background-color: ${item.styles.backgroundColor};
                            border-radius: ${item.styles.borderRadius}px;
                            border-style: ${item.styles.borderStyle || 'solid'};
                         `">

                        {{-- 1. BAŞLIK BLOĞU --}}
                        <template x-if="item.type === 'header_block'">
                            <div class="w-full h-full flex flex-col items-center justify-center text-black leading-tight cursor-text">
                                <h2 class="text-xl font-bold font-serif uppercase outline-none" contenteditable="true" @input="item.content.title = $event.target.innerText" x-text="item.content.title"></h2>
                                <h3 class="text-lg font-semibold outline-none" contenteditable="true" @input="item.content.faculty = $event.target.innerText" x-text="item.content.faculty"></h3>
                                <h4 class="text-base mt-1 outline-none" contenteditable="true" @input="item.content.term = $event.target.innerText" x-text="item.content.term"></h4>
                            </div>
                        </template>

                        {{-- 2. ÖĞRENCİ BİLGİ TABLOSU --}}
                        <template x-if="item.type === 'student_info'">
                            <div class="w-full h-full p-2 flex items-center">
                                <table class="w-full text-sm font-mono text-black">
                                    <tr>
                                        <td class="pb-2 w-1/2">
                                            <strong contenteditable="true" @input="item.content.label1 = $event.target.innerText" x-text="item.content.label1"></strong> 
                                            <span contenteditable="true" @input="item.content.val1 = $event.target.innerText" x-text="item.content.val1"></span>
                                        </td>
                                        <td class="pb-2 w-1/2 text-right">
                                            <strong contenteditable="true" @input="item.content.label2 = $event.target.innerText" x-text="item.content.label2"></strong> 
                                            <span contenteditable="true" @input="item.content.val2 = $event.target.innerText" x-text="item.content.val2"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong contenteditable="true" @input="item.content.label3 = $event.target.innerText" x-text="item.content.label3"></strong> 
                                            <span contenteditable="true" @input="item.content.val3 = $event.target.innerText" x-text="item.content.val3"></span>
                                        </td>
                                        <td class="text-right">
                                            <strong contenteditable="true" @input="item.content.label4 = $event.target.innerText" x-text="item.content.label4"></strong> 
                                            <span contenteditable="true" @input="item.content.val4 = $event.target.innerText" x-text="item.content.val4"></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </template>

                        {{-- 3. ÇOKTAN SEÇMELİ --}}
                        <template x-if="item.type === 'multiple_choice'">
                            <div class="w-full h-full p-2 text-black overflow-hidden">
                                <div class="flex justify-between items-start mb-1">
                                    <div class="flex gap-1 w-full">
                                        <span class="font-bold">1.</span>
                                        <div class="font-bold w-full outline-none" contenteditable="true" @input="item.content.question = $event.target.innerText" x-text="item.content.question"></div>
                                    </div>
                                    <span class="text-xs font-bold whitespace-nowrap" contenteditable="true" @input="item.content.point = $event.target.innerText" x-text="'(' + item.content.point + 'p)'"></span>
                                </div>
                                <div class="pl-4 text-sm space-y-1">
                                    <template x-for="(opt, idx) in item.content.options">
                                        <div contenteditable="true" class="outline-none hover:bg-gray-100" @input="item.content.options[idx] = $event.target.innerText" x-text="opt"></div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- 4. AÇIK UÇLU --}}
                        <template x-if="item.type === 'open_ended'">
                            <div class="w-full h-full p-2 text-black overflow-hidden">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex gap-1 w-full">
                                        <span class="font-bold">2.</span>
                                        <div class="font-bold w-full outline-none" contenteditable="true" @input="item.content.question = $event.target.innerText" x-text="item.content.question"></div>
                                    </div>
                                    <span class="text-xs font-bold whitespace-nowrap" contenteditable="true" @input="item.content.point = $event.target.innerText" x-text="'(' + item.content.point + 'p)'"></span>
                                </div>
                                <div class="w-full h-12 border-b border-gray-300 bg-[linear-gradient(to_bottom,transparent_20px,#ccc_21px)] bg-[size:100%_21px]"></div>
                            </div>
                        </template>

                        {{-- 5. BOŞLUK DOLDURMA --}}
                        <template x-if="item.type === 'fill_in_blanks'">
                            <div class="w-full h-full p-2 text-black flex items-center">
                                <span class="font-bold mr-2">3.</span>
                                <div class="flex-1 outline-none text-sm font-medium leading-loose" contenteditable="true" @input="item.content.question = $event.target.innerText" x-text="item.content.question"></div>
                                <span class="text-xs font-bold ml-2" x-text="'(' + item.content.point + 'p)'"></span>
                            </div>
                        </template>

                        {{-- 6. DOĞRU YANLIŞ --}}
                        <template x-if="item.type === 'true_false'">
                            <div class="w-full h-full p-2 text-black flex items-center justify-between">
                                <div class="flex items-center flex-1">
                                    <span class="font-bold mr-2">4.</span>
                                    <div class="outline-none text-sm" contenteditable="true" @input="item.content.question = $event.target.innerText" x-text="item.content.question"></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-sm" contenteditable="true" x-text="item.content.format"></span>
                                    <span class="text-xs font-bold" x-text="'(' + item.content.point + 'p)'"></span>
                                </div>
                            </div>
                        </template>

                        {{-- 7. CUSTOM SORU (Temizlendi) --}}
                        <template x-if="item.type === 'custom_question'">
                            <div class="w-full h-full p-2 overflow-hidden outline-none flex items-center justify-center text-center"
                                 contenteditable="true"
                                 @input="item.content.text = $event.target.innerText"
                                 x-text="item.content.text"
                                 :style="`color: ${item.styles.color}; font-size: ${item.styles.fontSize}px;`">
                            </div>
                        </template>

                        {{-- 8. ARAÇLAR (Başlık, Alt Başlık, Metin) --}}
                        <template x-if="['heading', 'sub_heading', 'text'].includes(item.type)">
                            <div class="w-full h-full p-1 outline-none text-black" contenteditable="true" @input="item.content = $event.target.innerText" x-text="item.content" :style="{ fontSize: item.styles.fontSize + 'px', fontWeight: item.styles.fontWeight, color: item.styles.color, textAlign: item.styles.textAlign }"></div>
                        </template>

                        {{-- 9. RESİM --}}
                        <template x-if="item.type === 'image'">
                            <div class="w-full h-full bg-gray-50 flex items-center justify-center relative group-hover:bg-gray-100">
                                <img x-show="item.content" :src="item.content" class="w-full h-full object-contain pointer-events-none">
                                <div x-show="!item.content" class="text-center absolute">
                                    <label class="cursor-pointer text-gray-400 hover:text-gray-600 flex flex-col items-center">
                                        <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                                        <span class="text-[10px]">Yükle</span>
                                        <input type="file" class="hidden" @change="uploadImage($event, item)">
                                    </label>
                                </div>
                            </div>
                        </template>

                        {{-- 10. KUTU --}}
                        <template x-if="item.type === 'box'">
                            <div class="w-full h-full"></div>
                        </template>

                        {{-- KONTROLLER --}}
                        <div x-show="selectedId === item.id">
                            
                            <div class="absolute -right-2 -bottom-2 w-4 h-4 bg-blue-500 rounded-full cursor-nwse-resize z-50"></div>
                            
                            <button @click.stop="remove(item.id)" 
                                    class="absolute -top-10 -right-0 bg-red-500 text-white w-8 h-8 rounded-full shadow hover:bg-red-600 flex items-center justify-center transition transform hover:scale-110 z-50"
                                    title="Sil">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>

                            <template x-if="['multiple_choice', 'open_ended', 'true_false', 'fill_in_blanks'].includes(item.type)">
                                <button @click.stop="openAiModal(item)" 
                                        class="absolute -top-10 right-10 bg-indigo-600 text-white w-8 h-8 rounded-full shadow hover:bg-indigo-500 flex items-center justify-center transition transform hover:scale-110 z-50 group"
                                        title="AI ile Doldur">
                                    <i class="fa-solid fa-wand-magic-sparkles text-xs group-hover:animate-spin"></i>
                                </button>
                            </template>

                        </div>

                    </div>
                </template>

                {{-- BOŞ UYARISI --}}
                <div x-show="elements.length === 0" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="text-center text-gray-300">
                        <i class="fa-solid fa-arrow-left text-3xl mb-2"></i>
                        <p class="font-bold">Araç kutusundan buraya sürükleyin</p>
                    </div>
                </div>

            </div>
        </div>
    </main>