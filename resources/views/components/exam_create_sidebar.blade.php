   
  
    <aside class="flex-none w-72 bg-[#252526] border-r border-[#3e3e42] flex flex-col shadow-2xl z-20 select-none">
        
        <div class="p-4 border-b border-[#3e3e42] bg-[#2d2d30]">
            <h3 class="text-white font-bold flex items-center gap-2">
                <i class="fa-solid fa-toolbox text-blue-500"></i> Araç Kutusu
            </h3>
        </div>
    
        <div class="flex-1 overflow-y-auto p-4 space-y-6 custom-scrollbar">
        

        

            <div>
                <h4 class="text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-wider">EKLE</h4>
                <div class="grid grid-cols-3 gap-2">
                    <div draggable="true" @dragstart="dragStart($event, 'heading')" class="bg-[#333333] hover:bg-[#3e3e42] p-2 rounded flex flex-col items-center cursor-grab gap-1 transition">
                        <i class="fa-solid fa-heading text-white"></i>
                        <span class="text-[10px] text-gray-400">Başlık</span>
                    </div>
                    <div draggable="true" @dragstart="dragStart($event, 'sub_heading')" class="bg-[#333333] hover:bg-[#3e3e42] p-2 rounded flex flex-col items-center cursor-grab gap-1 transition">
                        <i class="fa-solid fa-font text-sm text-gray-300"></i>
                        <span class="text-[10px] text-gray-400">Alt B.</span>
                    </div>
                    <div draggable="true" @dragstart="dragStart($event, 'text')" class="bg-[#333333] hover:bg-[#3e3e42] p-2 rounded flex flex-col items-center cursor-grab gap-1 transition">
                        <i class="fa-solid fa-paragraph text-gray-300"></i>
                        <span class="text-[10px] text-gray-400">Metin</span>
                    </div>
                    <div draggable="true" @dragstart="dragStart($event, 'image')" class="bg-[#333333] hover:bg-[#3e3e42] p-2 rounded flex flex-col items-center cursor-grab gap-1 transition">
                        <i class="fa-regular fa-image text-gray-300"></i>
                        <span class="text-[10px] text-gray-400">Resim</span>
                    </div>
                    <div draggable="true" @dragstart="dragStart($event, 'box')" class="bg-[#333333] hover:bg-[#3e3e42] p-2 rounded flex flex-col items-center cursor-grab gap-1 transition">
                        <i class="fa-regular fa-square text-gray-300"></i>
                        <span class="text-[10px] text-gray-400">Kutu</span>
                    </div>
                </div>
            </div>

           
            <div>
                <h4 class="text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-wider">ŞABLONLAR</h4>
                <div class="space-y-2">
                    <div draggable="true" @dragstart="dragStart($event, 'header_block')" class="flex items-center gap-2 bg-[#333333] hover:bg-[#3e3e42] p-2 rounded cursor-grab">
                        <i class="fa-solid fa-hotel text-blue-400"></i> <span class="text-xs text-gray-300">Okul Başlığı</span>
                    </div>
                    <div draggable="true" @dragstart="dragStart($event, 'student_info')" class="flex items-center gap-2 bg-[#333333] hover:bg-[#3e3e42] p-2 rounded cursor-grab">
                        <i class="fa-solid fa-user-graduate text-blue-400"></i> <span class="text-xs text-gray-300">Öğrenci Bilgi</span>
                    </div>
                </div>
            </div>

          
            <div>
                <h4 class="text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-wider">SORU TİPLERİ</h4>
                <div class="space-y-2">
                    <div draggable="true" @dragstart="dragStart($event, 'multiple_choice')" class="group bg-[#333333] hover:bg-[#3e3e42] p-2 rounded border border-[#3e3e42] hover:border-green-500 cursor-grab flex items-center gap-3">
                        <i class="fa-solid fa-list-ul text-green-500"></i>
                        <span class="text-xs text-gray-300">Çoktan Seçmeli</span>
                    </div>
                    <div draggable="true" @dragstart="dragStart($event, 'open_ended')" class="group bg-[#333333] hover:bg-[#3e3e42] p-2 rounded border border-[#3e3e42] hover:border-orange-500 cursor-grab flex items-center gap-3">
                        <i class="fa-solid fa-align-left text-orange-500"></i>
                        <span class="text-xs text-gray-300">Klasik</span>
                    </div>
                    <div draggable="true" @dragstart="dragStart($event, 'fill_in_blanks')" class="group bg-[#333333] hover:bg-[#3e3e42] p-2 rounded border border-[#3e3e42] hover:border-purple-500 cursor-grab flex items-center gap-3">
                        <i class="fa-solid fa-ellipsis text-purple-500"></i>
                        <span class="text-xs text-gray-300">Boşluk Doldurma</span>
                    </div>
                    <div draggable="true" @dragstart="dragStart($event, 'true_false')" class="group bg-[#333333] hover:bg-[#3e3e42] p-2 rounded border border-[#3e3e42] hover:border-red-500 cursor-grab flex items-center gap-3">
                        <i class="fa-solid fa-check-double text-red-500"></i>
                        <span class="text-xs text-gray-300">Doğru / Yanlış</span>
                    </div>
                    <div draggable="true" @dragstart="dragStart($event, 'custom_question')" class="group bg-[#333333] hover:bg-[#3e3e42] p-2 rounded border border-[#3e3e42] hover:border-yellow-500 cursor-grab flex items-center gap-3">
                        <i class="fa-regular fa-square-plus text-yellow-500"></i>
                        <span class="text-xs text-gray-300">Kendi Sorum (Custom)</span>
                    </div>
                </div>
            </div>

          
            <div class="pt-4 border-t border-[#3e3e42]">
                <h4 class="text-xs font-bold text-blue-400 uppercase mb-3 tracking-wider">AI ILE SORU OLUSTUR</h4>
                
             
                <button @click="aiBatchModalOpen = true" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white p-3 rounded-lg shadow-lg flex items-center justify-center gap-2 mb-4 transition-all group">
                    <i class="fa-solid fa-wand-magic-sparkles group-hover:animate-pulse"></i>
                    <span class="font-bold text-sm">AI Soru Sihirbazı</span>
                </button>

             
                <div class="space-y-2 border border-dashed border-gray-600 rounded-lg min-h-32 p-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] text-gray-500 font-bold uppercase">Soru Havuzu (AI)</span>
                        <button @click="aiPoolGroups = []" class="text-[10px] text-red-400 hover:underline">Temizle</button>
                    </div>
                    <div x-show="aiPoolGroups.length <= 0" class="w-full min-h-24 flex justify-center items-center">
                        <span class="text-[10px] text-gray-500 font-bold uppercase">Üretilen Sorular Buraya Gelecek</span>
                    </div>

                    <template x-for="(group, idx) in aiPoolGroups" :key="group.id">
              
                        <div draggable="true" 
                             @dragstart="dragStart($event, group.type, idx)"
                             class="group bg-[#333333] hover:bg-[#3e3e42] p-3 rounded-lg border border-[#3e3e42] hover:border-indigo-500 cursor-grab active:cursor-grabbing transition-all relative overflow-hidden shadow-sm">
                            
                          
                            <div class="absolute top-0 right-0 px-2 py-0.5 rounded-bl-md text-[10px] font-bold text-white shadow-sm"
                                 :class="{
                                    'bg-green-600': group.difficulty === 'easy',
                                    'bg-yellow-600': group.difficulty === 'medium',
                                    'bg-red-600': group.difficulty === 'hard'
                                 }"
                                 x-text="group.difficultyLabel">
                            </div>

                            <div class="flex items-center gap-2 mt-1">
                      
                                <i :class="{
                                    'fa-solid fa-list-ul text-green-500 text-lg' : group.typeName === 'Çoktan Seçmeli',
                                    'fa-solid fa-align-left text-orange-500 text-lg' : group.typeName === 'Klasik',
                                    'fa-solid fa-ellipsis text-purple-500 text-lg' : group.typeName === 'Boşluk Doldurma',
                                    'fa-solid fa-check-double text-red-500 text-lg' : group.typeName === 'Doğru/Yanlış',
                                }"></i>
                                
                                <div class="flex flex-col leading-tight">
                                    <span class="text-xs font-bold text-gray-200">
                                        (AI) <span x-text="group.typeName"></span>
                                    </span>
                                    <span class="text-[10px] text-gray-400">
                                        <span class="text-indigo-400 font-bold" x-text="group.count"></span> adet kaldı
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

            </div>

            
        </div>
    </aside>