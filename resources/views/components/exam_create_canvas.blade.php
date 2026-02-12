{{-- 2. CANVAS --}}
<main class=" absolute inset-0 flex-1 flex flex-col min-w-0 bg-[#1e1e1e]"
    @mousedown="if($event.target === $el) deselect()">


    <header class="h-14 bg-[#252526] border-b border-[#3e3e42] flex  items-center px-4 shadow-md z-10">
        <div class="absolute left-4 flex items-center gap-2">

            <input type="text" x-model="examTitle"
                class="bg-transparent border-b border-transparent hover:border-gray-500 focus:border-blue-500 text-white font-bold text-sm focus:outline-none focus:ring-0 w-64 transition-all placeholder-gray-500"
                placeholder="Sınav Adı Giriniz">
        </div>


        <div class="absolute transform -translate-x-1/2 flex bg-[#1e1e1e] p-1 rounded-lg border border-[#3e3e42] gap-1 transition-all duration-300 ease-in-out z-20"
            :class="selectedId ? 'left-[40%]' : 'left-1/2'">


            <button @click="setMode('select')"
                :class="cursorMode === 'select' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-[#333]'"
                class="p-2 rounded transition" title="Seç ve Düzenle">
                <i class="fa-solid fa-arrow-pointer"></i>
            </button>


            <button @click="setMode('move')"
                :class="cursorMode === 'move' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-[#333]'"
                class="p-2 rounded transition" title="Sadece Taşı">
                <i class="fa-solid fa-up-down-left-right"></i>
            </button>

            <div class="w-px h-6 bg-gray-600 mx-1 self-center"></div>


            <button @click="setMode('draw')"
                :class="cursorMode === 'draw' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-[#333]'"
                class="p-2 rounded transition" title="Kalem"><i class="fa-solid fa-pencil"></i></button>
            <button @click="setMode('shape')"
                :class="cursorMode === 'shape' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-[#333]'"
                class="p-2 rounded transition" title="Şekil"><i class="fa-regular fa-square"></i></button>
            <button @click="setMode('text')"
                :class="cursorMode === 'text' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-[#333]'"
                class="p-2 rounded transition" title="Yazı"><i class="fa-solid fa-font"></i></button>
        </div>

        <div class="flex items-center gap-2 absolute top-3 transition-all duration-300 ease-in-out z-[100]"
            :style="selectedItem ? 'right: 19rem;' : 'right: 1rem;'">
            @auth
                <button @click="saveAndAction('library')"
                    class="px-3 py-1.5 bg-[#3e3e42] hover:bg-[#4e4e52] text-white rounded text-xs font-medium transition-colors">Kaydet</button>
                <button @click="saveAndAction('preview')"
                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded text-xs font-medium shadow-lg transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-eye"></i>
                    Ön İzle
                </button>
                <button @click="saveAndAction('download')"
                    class="px-3 py-1.5 bg-emerald-600 hover:bag-emerald-500 text-white rounded text-xs font-medium shadow-lg transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf"></i>
                    PDF İndir
                </button>
            @else
                <button @click="$dispatch('notify', { message: ' Lütfen giriş yapın!', type: 'warning' })"
                    class="px-3 py-1.5 bg-[#3e3e42] hover:bg-[#4e4e52] text-white rounded text-xs font-medium transition-colors">Kaydet</button>
                <button @click="$dispatch('notify', { message: ' Lütfen giriş yapın!', type: 'warning' })"
                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded text-xs font-medium shadow-lg transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-eye"></i>
                    Ön İzle
                </button>
                <button @click="$dispatch('notify', { message: ' Lütfen giriş yapın!', type: 'warning' })"
                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-xs font-medium shadow-lg transition-colors flex items-center gap-2"><i
                        class="fa-solid fa-download"></i> PDF</button>
            @endauth
        </div>
    </header>


    <div class="h-12 bg-[#252526] border-b border-[#3e3e42] flex justify-center items-center gap-4 shadow-sm z-20">
        <button @click="setPage(Math.max(1, activePage - 1))" class="text-gray-400 hover:text-white disabled:opacity-30"
            :disabled="activePage === 1"><i class="fa-solid fa-chevron-left"></i></button>
        <div class="flex items-center gap-2 bg-[#1e1e1e] px-3 py-1 rounded-full border border-[#3e3e42]"><span
                class="text-xs font-mono text-gray-400">Sayfa</span><span x-text="activePage"
                class="text-sm font-bold text-white"></span><span class="text-xs text-gray-500">/</span><span
                x-text="totalPages" class="text-xs text-gray-500"></span></div>
        <button @click="setPage(Math.min(totalPages, activePage + 1))"
            class="text-gray-400 hover:text-white disabled:opacity-30" :disabled="activePage === totalPages"><i
                class="fa-solid fa-chevron-right"></i></button>
        <div class="w-px h-4 bg-gray-600 mx-2"></div>
        <button @click="addPage()"
            class="text-xs text-blue-400 hover:text-blue-300 font-bold flex items-center gap-1"><i
                class="fa-solid fa-plus"></i> Ekle</button>
        <button @click="deletePage()"
            class="text-xs text-red-400 hover:text-red-300 font-bold flex items-center gap-1 ml-2"
            x-show="totalPages > 1"><i class="fa-solid fa-trash"></i> Sil</button>
    </div>


    <div class="flex-1 overflow-y-auto p-8 md:p-12 flex justify-center bg-[#1e1e1e] relative cursor-crosshair">

        <div id="paper"
            class="bg-white relative shadow-[0_0_50px_rgba(0,0,0,0.5)] origin-top transition-all duration-300 ease-in-out"
            style="width: 210mm; min-height: 297mm;" :class="[
                        cursorMode === 'select' ? 'cursor-default' : 'cursor-crosshair',
                        selectedId ? '-translate-x-[10rem]' : 'translate-x-0'
            ]" @click.self="deselect()" @dragover.prevent @drop="handleDrop($event)">

            <template x-for="item in currentPageElements" :key="item.id">

                <div :id="item.id" class="absolute group box-border draggable-item !overflow-visible"
                    @click="select(item.id)" x-init="$nextTick(() => { 
             // Başlangıçta tüm textarea'ların boyunu ayarla
             $el.querySelectorAll('textarea').forEach(t => { 
                 t.style.height = 'auto'; 
                 t.style.height = t.scrollHeight + 'px'; 
             });
         })" :class="selectedId === item.id ? 'ring-2 ring-blue-500 ring-offset-1 z-[9999]' : ''" :style="`
            transform: translate(${item.x}px, ${item.y}px);
            width: ${item.w}px;
            
            /* --- KRİTİK DÜZELTME BURASI --- */
            /* Yükseklik sabit değil, 'en az' kullanıcının seçtiği kadar. */
            /* İçerik taşarsa otomatik uzar (auto). */
            min-height: ${item.h}px;
            height: auto;
            
            z-index: ${selectedId === item.id ? 9999 : item.styles.zIndex}; 
            border: ${item.styles.borderWidth}px ${item.styles.borderStyle || 'solid'} ${item.styles.borderColor};
            background-color: ${item.styles.backgroundColor};
            border-radius: ${item.styles.borderRadius}px;
          `">

                    <template x-if="item.type === 'header_block'">
                        <div
                            class="w-full h-full flex flex-col items-center justify-center text-black leading-tight p-2">
                            <input type="text" x-model="item.content.title"
                                class="w-full text-center bg-transparent border-none focus:ring-0 p-0 font-bold font-serif uppercase placeholder-gray-300"
                                :style="{ fontSize: (parseInt(item.styles.fontSize) + 6) + 'px', color: item.styles.color }"
                                :class="{'pointer-events-none': cursorMode === 'move'}" placeholder="ÜNİVERSİTE ADI">
                            <input type="text" x-model="item.content.faculty"
                                class="w-full text-center bg-transparent border-none focus:ring-0 p-0 font-semibold placeholder-gray-300"
                                :style="{ fontSize: (parseInt(item.styles.fontSize) + 4) + 'px', color: item.styles.color }"
                                :class="{'pointer-events-none': cursorMode === 'move'}" placeholder="Fakülte / Bölüm">
                            <input type="text" x-model="item.content.term"
                                class="w-full text-center bg-transparent border-none focus:ring-0 p-0 mt-1 placeholder-gray-300"
                                :style="{ fontSize: item.styles.fontSize + 'px', color: item.styles.color }"
                                :class="{'pointer-events-none': cursorMode === 'move'}" placeholder="Eğitim Yılı">
                        </div>
                    </template>

                    <template x-if="item.type === 'student_info'">
                        <div class="w-full h-full px-2 py-1 flex flex-col justify-center"
                            :style="{ color: item.styles.color }">
                            <table class="w-full h-full table-fixed border-collapse">
                                <tbody>
                                    <tr class="h-1/2">
                                        <td class="align-middle w-1/2 pr-2 border-r border-transparent">
                                            <div class="flex items-center w-full gap-1">
                                                <input type="text" x-model="item.content.label1"
                                                    class="font-bold bg-transparent border-none p-0 w-1/3 text-sm focus:ring-0 leading-none h-6"
                                                    :class="{'pointer-events-none': cursorMode === 'move'}">
                                                <input type="text" x-model="item.content.val1"
                                                    class="flex-1 min-w-0 bg-transparent border-b border-gray-400 border-dashed p-0 text-sm focus:ring-0 leading-none h-6"
                                                    :class="{'pointer-events-none': cursorMode === 'move'}">
                                            </div>
                                        </td>
                                        <td class="align-middle w-1/2 pl-2">
                                            <div class="flex items-center justify-end w-full gap-1">
                                                <input type="text" x-model="item.content.label2"
                                                    class="font-bold bg-transparent border-none p-0 w-1/3 text-sm text-right focus:ring-0 leading-none h-6"
                                                    :class="{'pointer-events-none': cursorMode === 'move'}">
                                                <input type="text" x-model="item.content.val2"
                                                    class="flex-1 min-w-0 bg-transparent border-b border-gray-400 border-dashed p-0 text-sm text-left focus:ring-0 leading-none h-6"
                                                    :class="{'pointer-events-none': cursorMode === 'move'}">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="h-1/2">
                                        <td class="align-middle w-1/2 pr-2 border-r border-transparent">
                                            <div class="flex items-center w-full gap-1">
                                                <input type="text" x-model="item.content.label3"
                                                    class="font-bold bg-transparent border-none p-0 w-1/3 text-sm focus:ring-0 leading-none h-6"
                                                    :class="{'pointer-events-none': cursorMode === 'move'}">
                                                <input type="text" x-model="item.content.val3"
                                                    class="flex-1 min-w-0 bg-transparent border-b border-gray-400 border-dashed p-0 text-sm focus:ring-0 leading-none h-6"
                                                    :class="{'pointer-events-none': cursorMode === 'move'}">
                                            </div>
                                        </td>
                                        <td class="align-middle w-1/2 pl-2">
                                            <div class="flex items-center justify-end w-full gap-1">
                                                <input type="text" x-model="item.content.label4"
                                                    class="font-bold bg-transparent border-none p-0 w-1/3 text-sm text-right focus:ring-0 leading-none h-6"
                                                    :class="{'pointer-events-none': cursorMode === 'move'}">
                                                <input type="text" x-model="item.content.val4"
                                                    class="flex-1 min-w-0 bg-transparent border-b border-gray-400 border-dashed p-0 text-sm text-left focus:ring-0 leading-none h-6"
                                                    :class="{'pointer-events-none': cursorMode === 'move'}">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <template x-if="item.type === 'multiple_choice'">
                        <div class="w-full p-2 text-black flex flex-col h-full">
                            <div class="flex gap-1 w-full mb-1 items-start">
                                <input type="text" x-model="item.content.number"
                                    class="w-8 font-bold bg-transparent border-none p-0 focus:ring-0 text-right mr-1 mt-0.5"
                                    :style="{ fontSize: item.styles.fontSize + 'px', color: item.styles.color }"
                                    placeholder="1.">

                                <textarea x-model="item.content.question" @input="autoResize($event, item)"
                                    class="flex-1 bg-transparent border-none focus:ring-0 p-0 resize-none overflow-hidden placeholder-gray-400"
                                    :class="{'pointer-events-none': cursorMode === 'move'}"
                                    :style="{ fontSize: item.styles.fontSize + 'px', fontWeight: item.styles.fontWeight, color: item.styles.color, textAlign: item.styles.textAlign, height: 'auto' }"
                                    rows="1"></textarea>

                                <div class="flex items-center gap-0.5 mt-0.5">
                                    <span class="text-xs font-bold" x-show="item.content.point"> (</span>
                                    <input type="text" x-model="item.content.point" placeholder="Pn"
                                        class="w-6 text-center text-xs font-bold bg-transparent border-none p-0 focus:ring-0 placeholder-gray-300"
                                        :class="{'pointer-events-none': cursorMode === 'move'}">
                                    <span class="text-xs font-bold" x-show="item.content.point">p) </span>
                                </div>
                            </div>

                            <div class="pl-8 space-y-1 w-full flex-1">
                                <template x-for="(opt, idx) in item.content.options">
                                    <div class="flex items-start gap-2 w-full">
                                        <span class="font-bold mt-0.5"
                                            :style="{ fontSize: item.styles.fontSize + 'px', color: item.styles.color }"
                                            x-text="String.fromCharCode(65 + idx) + ')'"></span>
                                        <textarea x-model="item.content.options[idx]" @input="autoResize($event, item)"
                                            rows="1"
                                            class="flex-1 bg-transparent border-none focus:ring-0 p-0 hover:bg-gray-50 rounded px-1 resize-none overflow-hidden"
                                            :style="{ fontSize: item.styles.fontSize + 'px', color: item.styles.color, height: 'auto' }"
                                            :class="{'pointer-events-none': cursorMode === 'move'}"></textarea>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="item.type === 'open_ended'">
                        <div class="w-full p-2 text-black flex flex-col h-full">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex gap-1 w-full items-start">
                                    <input type="text" x-model="item.content.number"
                                        class="w-8 font-bold bg-transparent border-none p-0 focus:ring-0 text-right mr-1 mt-0.5"
                                        :style="{ fontSize: item.styles.fontSize + 'px', color: item.styles.color }"
                                        :class="{'pointer-events-none': cursorMode === 'move'}">

                                    <textarea x-model="item.content.question" @input="autoResize($event, item)"
                                        class="w-full bg-transparent border-none focus:ring-0 p-0 resize-none placeholder-gray-400 overflow-hidden"
                                        :style="{ fontSize: item.styles.fontSize + 'px', fontWeight: item.styles.fontWeight, color: item.styles.color, textAlign: item.styles.textAlign, height: 'auto' }"
                                        :class="{'pointer-events-none': cursorMode === 'move'}" rows="1"></textarea>
                                </div>
                                <div class="flex items-center gap-0.5 mt-0.5">
                                    <span class="text-xs font-bold" x-show="item.content.point">(</span>
                                    <input type="text" x-model="item.content.point" placeholder="Pn"
                                        class="w-6 text-center text-xs font-bold bg-transparent border-none p-0 focus:ring-0 placeholder-gray-300"
                                        :class="{'pointer-events-none': cursorMode === 'move'}">
                                    <span class="text-xs font-bold" x-show="item.content.point">p)</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="item.type === 'fill_in_blanks'">
                        <div class="w-full p-2 text-black flex items-start h-full">
                            <input type="text" x-model="item.content.number" @mousedown.stop
                                class="w-8 font-bold bg-transparent border-none p-0 focus:ring-0 text-right mr-2 mt-1.5 leading-none"
                                :style="{ fontSize: item.styles.fontSize + 'px', color: item.styles.color }"
                                :class="{'pointer-events-none': cursorMode === 'move'}">

                            <textarea x-model="item.content.question" @mousedown.stop @input="autoResize($event, item)"
                                class="flex-1 bg-transparent border-none focus:ring-0 p-0 font-medium leading-loose resize-none overflow-hidden placeholder-gray-400"
                                :style="{ fontSize: item.styles.fontSize + 'px', fontWeight: item.styles.fontWeight, color: item.styles.color, textAlign: item.styles.textAlign, height: 'auto' }"
                                :class="{'pointer-events-none': cursorMode === 'move'}" rows="2"></textarea>

                            <div class="flex items-center ml-2 flex-shrink-0 mt-1.5 gap-0.5">
                                <span class="text-xs font-bold" x-show="item.content.point">(</span>
                                <input type="text" x-model="item.content.point" placeholder="Pn"
                                    class="w-6 text-center text-xs font-bold bg-transparent border-none p-0 focus:ring-0 placeholder-gray-300"
                                    :class="{'pointer-events-none': cursorMode === 'move'}">
                                <span class="text-xs font-bold" x-show="item.content.point">p)</span>
                            </div>
                        </div>
                    </template>

                    <template x-if="item.type === 'true_false'">
                        <div class="w-full p-2 text-black flex items-start justify-between h-full">
                            <div class="flex items-start flex-1 gap-2">
                                <input type="text" x-model="item.content.number"
                                    class="w-8 font-bold bg-transparent border-none p-0 focus:ring-0 text-right mt-0.5"
                                    :style="{ fontSize: item.styles.fontSize + 'px', color: item.styles.color }"
                                    :class="{'pointer-events-none': cursorMode === 'move'}">

                                <textarea x-model="item.content.question" @input="autoResize($event, item)"
                                    class="w-full bg-transparent border-none focus:ring-0 p-0 resize-none overflow-hidden placeholder-gray-400"
                                    :style="{ fontSize: item.styles.fontSize + 'px', fontWeight: item.styles.fontWeight, color: item.styles.color, textAlign: item.styles.textAlign, height: 'auto' }"
                                    :class="{'pointer-events-none': cursorMode === 'move'}" rows="1"></textarea>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 mt-0.5">
                                <input type="text" x-model="item.content.format"
                                    class="w-16 text-center font-mono font-bold text-sm bg-transparent border-none p-0 focus:ring-0"
                                    :style="{ color: item.styles.color }"
                                    :class="{'pointer-events-none': cursorMode === 'move'}">
                                <div class="flex items-center gap-0.5">
                                    <span class="text-xs font-bold" x-show="item.content.point">(</span>
                                    <input type="text" x-model="item.content.point" placeholder="Pn"
                                        class="w-6 text-center text-xs font-bold bg-transparent border-none p-0 focus:ring-0 placeholder-gray-300"
                                        :class="{'pointer-events-none': cursorMode === 'move'}">
                                    <span class="text-xs font-bold" x-show="item.content.point">p)</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="item.type === 'custom_question'">
                        <div class="w-full h-full p-2 overflow-hidden flex items-center justify-center">
                            <textarea x-model="item.content.text"
                                class="w-full h-full bg-transparent border-none focus:ring-0 p-0 text-center resize-none placeholder-gray-400"
                                :class="{'pointer-events-none': cursorMode === 'move'}"
                                :style="`color: ${item.styles.color}; font-size: ${item.styles.fontSize}px; font-weight: ${item.styles.fontWeight}; text-align: ${item.styles.textAlign}`"
                                placeholder="Yazı yazın..."></textarea>
                        </div>
                    </template>

                    <template x-if="['heading', 'sub_heading', 'text'].includes(item.type)">
                        <div class="w-full h-full p-1">
                            <textarea x-model="item.content"
                                class="w-full h-full bg-transparent border-none focus:ring-0 p-0 resize-none overflow-hidden placeholder-gray-400"
                                :class="{'pointer-events-none': cursorMode === 'move'}"
                                :style="{ fontSize: item.styles.fontSize + 'px', fontWeight: item.styles.fontWeight, color: item.styles.color, textAlign: item.styles.textAlign }"
                                placeholder="Metin girin..."></textarea>
                        </div>
                    </template>

                    <template x-if="item.type === 'image'">
                        <div
                            class="w-full h-full bg-gray-50 flex items-center justify-center relative group-hover:bg-gray-100">
                            <img x-show="item.content" :src="item.content"
                                class="w-full h-full object-contain pointer-events-none">
                            <div x-show="!item.content" class="text-center absolute">
                                <label
                                    class="cursor-pointer text-gray-400 hover:text-gray-600 flex flex-col items-center">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                                    <span class="text-[10px]">Yükle</span>
                                    <input type="file" class="hidden" @change="uploadImage($event, item)">
                                </label>
                            </div>
                        </div>
                    </template>

                    <template x-if="item.type === 'box'">
                        <div class="w-full h-full"></div>
                    </template>

                    <div x-show="selectedId === item.id" class="absolute -inset-[2px] pointer-events-none z-[99999]">
                        <div class="absolute inset-0 border-2 border-blue-500 rounded pointer-events-none"></div>
                        <div class="no-drag absolute -right-1.5 -bottom-1.5 w-5 h-5 bg-white border-2 border-blue-500 rounded-full cursor-nwse-resize pointer-events-auto shadow-sm z-[9999]"
                            @mousedown.stop="startResize($event, item)">
                        </div>
                        <div class="absolute -top-10 -right-1 flex gap-1 pointer-events-auto">
                            <template
                                x-if="['multiple_choice', 'open_ended', 'true_false', 'fill_in_blanks'].includes(item.type)">
                                <button @click.stop="returnToPool(item.id)" @mousedown.stop
                                    class="no-drag w-7 h-7 bg-yellow-500 hover:bg-yellow-400 text-white rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110 cursor-pointer"
                                    title="Havuza Geri Gönder">
                                    <i class="fa-solid fa-reply text-[11px] pointer-events-none"></i>
                                </button>
                            </template>
                            <template
                                x-if="['multiple_choice', 'open_ended', 'true_false', 'fill_in_blanks'].includes(item.type)">
                                <button @click.stop="openAiModal(item)" @mousedown.stop
                                    class="no-drag w-7 h-7 bg-indigo-600 hover:bg-indigo-500 text-white rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110 cursor-pointer"
                                    title="AI Düzenle">
                                    <i class="fa-solid fa-wand-magic-sparkles text-[11px] pointer-events-none"></i>
                                </button>
                            </template>
                            <button @click.stop="remove(item.id)" @mousedown.stop
                                class="no-drag w-7 h-7 bg-red-600 hover:bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110 cursor-pointer"
                                title="Sil">
                                <i class="fa-solid fa-trash text-[11px] pointer-events-none"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </template>

            <div x-show="elements.length === 0"
                class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="text-center text-gray-300">
                    <i class="fa-solid fa-arrow-left text-3xl mb-2"></i>
                    <p class="font-bold">Araç kutusundan buraya sürükleyin</p>
                </div>
            </div>

        </div>
    </div>
</main>