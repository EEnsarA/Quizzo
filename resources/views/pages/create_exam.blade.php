@extends("layouts.app")

@section("content")


<div class="flex w-full h-screen overflow-hidden bg-[#1e1e1e] font-sans">
    
    
    <aside class="flex-none w-80 bg-[#252526] border-r border-[#3e3e42] flex flex-col shadow-2xl z-20">
        
      
        <div class="p-5 border-b border-[#3e3e42] bg-[#2d2d30]">
            <h3 class="text-white font-bold flex items-center gap-2">
                <i class="fa-solid fa-toolbox text-blue-500"></i> Araç Kutusu
            </h3>
            <p class="text-xs text-gray-400 mt-1">Bileşenleri kağıda sürükleyin.</p>
        </div>

    
        <div class="flex-1 overflow-y-auto p-4 space-y-6 custom-scrollbar">
            
          
            <div>
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider">Temel Öğeler</h4>
                <div class="grid grid-cols-2 gap-3">
                    <div class="group bg-[#333333] hover:bg-[#3e3e42] p-3 rounded-lg border border-[#3e3e42] hover:border-blue-500 cursor-grab active:cursor-grabbing transition-all flex flex-col items-center justify-center gap-2 text-gray-300 hover:text-white" draggable="true">
                        <i class="fa-solid fa-heading text-xl"></i>
                        <span class="text-xs font-medium">Başlık</span>
                    </div>
                    <div class="group bg-[#333333] hover:bg-[#3e3e42] p-3 rounded-lg border border-[#3e3e42] hover:border-blue-500 cursor-grab active:cursor-grabbing transition-all flex flex-col items-center justify-center gap-2 text-gray-300 hover:text-white" draggable="true">
                        <i class="fa-solid fa-image text-xl"></i>
                        <span class="text-xs font-medium">Logo / Resim</span>
                    </div>
                    <div class="col-span-2 group bg-[#333333] hover:bg-[#3e3e42] p-3 rounded-lg border border-[#3e3e42] hover:border-blue-500 cursor-grab active:cursor-grabbing transition-all flex items-center gap-3 text-gray-300 hover:text-white" draggable="true">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span class="text-xs font-medium">Öğrenci Bilgi Kutusu</span>
                    </div>
                </div>
            </div>

          
            <div>
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider">Soru Tipleri</h4>
                <div class="space-y-2">
                    <div class="group bg-[#333333] hover:bg-[#3e3e42] p-3 rounded-lg border border-[#3e3e42] hover:border-green-500 cursor-grab active:cursor-grabbing transition-all flex items-center gap-3 text-gray-300 hover:text-white" draggable="true">
                        <div class="w-8 h-8 rounded bg-green-900/30 text-green-500 flex items-center justify-center"><i class="fa-solid fa-list-ul"></i></div>
                        <span class="text-sm font-medium">Çoktan Seçmeli</span>
                    </div>
                    <div class="group bg-[#333333] hover:bg-[#3e3e42] p-3 rounded-lg border border-[#3e3e42] hover:border-orange-500 cursor-grab active:cursor-grabbing transition-all flex items-center gap-3 text-gray-300 hover:text-white" draggable="true">
                        <div class="w-8 h-8 rounded bg-orange-900/30 text-orange-500 flex items-center justify-center"><i class="fa-solid fa-align-left"></i></div>
                        <span class="text-sm font-medium">Klasik (Açık Uçlu)</span>
                    </div>
                    <div class="group bg-[#333333] hover:bg-[#3e3e42] p-3 rounded-lg border border-[#3e3e42] hover:border-purple-500 cursor-grab active:cursor-grabbing transition-all flex items-center gap-3 text-gray-300 hover:text-white" draggable="true">
                        <div class="w-8 h-8 rounded bg-purple-900/30 text-purple-500 flex items-center justify-center"><i class="fa-solid fa-check-double"></i></div>
                        <span class="text-sm font-medium">Doğru / Yanlış</span>
                    </div>
                </div>
            </div>

        
            <div class="pt-4 border-t border-[#3e3e42]">
                <h4 class="text-xs font-bold text-blue-400 uppercase mb-3 tracking-wider">AI Destek</h4>
         
                <button class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white p-3 rounded-lg shadow-lg flex items-center justify-center gap-2 mb-4 transition-all group">
                    <i class="fa-solid fa-wand-magic-sparkles group-hover:animate-pulse"></i>
                    <span class="font-bold text-sm">AI ile Soru Üret</span>
                </button>

                <label class="block w-full border border-dashed border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:bg-[#333333] hover:border-gray-500 transition-colors group">
                    <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-500 group-hover:text-gray-300 mb-2"></i>
                    <span class="block text-xs text-gray-400">PDF/Word Kaynağı Yükle</span>
                    <input type="file" class="hidden">
                </label>
            </div>

        </div>
    </aside>


 
    <main class="flex-1 flex flex-col min-w-0 bg-[#1e1e1e]">
        
     
        <header class="h-16 bg-[#252526] border-b border-[#3e3e42] flex justify-between items-center px-6 shadow-md z-10">
      
            <div class="flex items-center gap-2 w-1/3">
                <i class="fa-regular fa-pen-to-square text-gray-500"></i>
                <input type="text" value="Yeni Sınav Kağıdı" 
                       class="bg-transparent border-none text-white font-bold text-lg focus:ring-0 w-full placeholder-gray-600" 
                       placeholder="Sınav Başlığı...">
            </div>

           
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 mr-2 flex items-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div> Kaydedildi
                </span>
                <button class="px-4 py-2 bg-[#3e3e42] hover:bg-[#4e4e52] text-white rounded-md text-sm font-medium transition-colors">
                    <i class="fa-regular fa-eye mr-1"></i> Önizle
                </button>
                <button class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-md text-sm font-medium shadow-lg transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-download"></i> PDF İndir
                </button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 md:p-12 flex justify-center bg-[#1e1e1e] relative">
            
            <div id="a4-page" class="bg-white w-[21cm] min-h-[29.7cm] shadow-[0_0_50px_rgba(0,0,0,0.5)] p-[2cm] relative transition-transform origin-top">
            
                <div class="group relative border border-transparent hover:border-dashed hover:border-blue-400 p-2 mb-6 cursor-move">
                    <div class="text-center">
                        <h2 class="text-xl font-bold font-serif text-black uppercase">Atatürk Üniversitesi</h2>
                        <h3 class="text-lg font-semibold text-gray-800">Mühendislik Fakültesi</h3>
                        <h4 class="text-base mt-1">2025-2026 Güz Dönemi Vize Sınavı</h4>
                    </div>
                 
                    <div class="absolute -right-8 top-0 hidden group-hover:flex flex-col gap-1">
                        <button class="w-6 h-6 bg-blue-500 text-white rounded flex items-center justify-center shadow"><i class="fa-solid fa-pen text-xs"></i></button>
                        <button class="w-6 h-6 bg-red-500 text-white rounded flex items-center justify-center shadow"><i class="fa-solid fa-trash text-xs"></i></button>
                    </div>
                </div>

                <div class="group relative border border-transparent hover:border-dashed hover:border-blue-400 p-4 mb-8 bg-gray-50 border-gray-300 rounded cursor-move">
                    <table class="w-full text-sm font-mono text-black">
                        <tr>
                            <td class="pb-2 w-1/2"><strong>Adı Soyadı:</strong> ............................................</td>
                            <td class="pb-2 w-1/2 text-right"><strong>Numara:</strong> ...........................</td>
                        </tr>
                        <tr>
                            <td><strong>İmza:</strong> ........................................................</td>
                            <td class="text-right"><strong>Puan:</strong> ......... / 100</td>
                        </tr>
                    </table>
                </div>

                {{-- 3. Sorular --}}
                <div class="space-y-6 text-black">
                    
                    {{-- Soru 1 --}}
                    <div class="group relative pl-2 hover:bg-blue-50/30 -ml-2 p-2 border-l-4 border-transparent hover:border-blue-500 transition-all cursor-pointer">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold">1. Aşağıdakilerden hangisi bir 'Döngü' yapısı değildir? (10p)</span>
                        </div>
                        <ol class="list-[upper-alpha] list-inside pl-4 space-y-1 text-sm">
                            <li>For</li>
                            <li>While</li>
                            <li>If-Else</li>
                            <li>Do-While</li>
                        </ol>
                    </div>

                    {{-- Soru 2 --}}
                    <div class="group relative pl-2 hover:bg-blue-50/30 -ml-2 p-2 border-l-4 border-transparent hover:border-blue-500 transition-all cursor-pointer">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold">2. Nesne Yönelimli Programlama (OOP) prensiplerini yazınız. (20p)</span>
                        </div>
                        <div class="w-full h-24 border-b border-gray-300 bg-[linear-gradient(to_bottom,transparent_29px,#ccc_30px)] bg-[size:100%_30px]"></div>
                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

@endsection