@extends("layouts.app")

@section("content")


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />


<div class="flex w-full h-screen overflow-hidden bg-gray-100">
    

    <aside class="flex-none w-[250px] bg-white border-r border-gray-200 p-5 shadow-lg z-10 flex flex-col overflow-y-auto">
        
        <div>
            <h3 class="text-xl font-semibold text-gray-800 border-b-2 border-[#0B2B8C] pb-2 mt-0">
                Bileşenler
            </h3>
            
            <ul class="list-none p-0 mt-4 space-y-2">
                
                <li class="flex items-center p-3 font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-md cursor-grab transition-all duration-150 hover:bg-gray-100 hover:border-[#0B2B8C] hover:text-black active:cursor-grabbing active:bg-blue-500 active:text-white" draggable="true">
                    <i class="fa-solid fa-heading w-5 h-5 mr-2 text-center"></i>
                    Sınav Başlığı
                </li>
                <li class="flex items-center p-3 font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-md cursor-grab transition-all duration-150 hover:bg-gray-100 hover:border-[#0B2B8C] hover:text-black active:cursor-grabbing active:bg-blue-500 active:text-white" draggable="true">
                    <i class="fa-solid fa-image w-5 h-5 mr-2 text-center"></i>
                    Okul Logosu
                </li>
                <li class="flex items-center p-3 font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-md cursor-grab transition-all duration-150 hover:bg-gray-100 hover:border-[#0B2B8C] hover:text-black active:cursor-grabbing active:bg-blue-500 active:text-white" draggable="true">
                    <i class="fa-solid fa-user-graduate w-5 h-5 mr-2 text-center"></i>
                    Öğrenci Bilgi Alanı
                </li>
                <li class="flex items-center p-3 font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-md cursor-grab transition-all duration-150 hover:bg-gray-100 hover:border-[#0B2B8C] hover:text-black active:cursor-grabbing active:bg-blue-500 active:text-white" draggable="true">
                    <i class="fa-solid fa-list-check w-5 h-5 mr-2 text-center"></i>
                    Çoktan Seçmeli Soru
                </li>
                <li class="flex items-center p-3 font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-md cursor-grab transition-all duration-150 hover:bg-gray-100 hover:border-[#0B2B8C] hover:text-black active:cursor-grabbing active:bg-blue-500 active:text-white" draggable="true">
                    <i class="fa-solid fa-align-left w-5 h-5 mr-2 text-center"></i>
                    Açık Uçlu Soru
                </li>
            </ul>
        </div>
    
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 border-b-2 border-[#0B2B8C] pb-2 mt-0">
                Soru Kaynağı Oluştur
            </h3>

            <div class="mt-4 space-y-3">
                

                <div>
                    <label for="file-upload" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-md cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400"></i>
                            <p class="text-sm text-gray-500"><span class="font-semibold">Slayt / Döküman</span> yükle</p>
                        </div>
                        <input id="file-upload" name="file-upload" type="file" class="hidden" />
                    </label>
                </div>
           
                <button class="w-full flex items-center justify-center p-3 font-semibold text-white bg-[#0B2B8C] rounded-md transition hover:bg-blue-800 active:bg-blue-800 cursor-pointer">
                    <i class="fa-solid fa-wand-magic-sparkles w-5 h-5 mr-2 text-center"></i>
                    AI ile Soru Oluştur
                </button>
            </div>
    
            <h3 class="text-lg font-semibold text-gray-700 mt-6 pb-2">
                AI Soruları (Hazır)
            </h3>
            <ul class="list-none p-0 mt-2 space-y-2">
   
                <li class="flex items-center p-3 font-medium text-gray-700 bg-green-50 border border-green-300 rounded-md cursor-grab transition-all duration-150 hover:bg-green-100 hover:border-green-500 hover:text-black active:cursor-grabbing active:bg-green-500 active:text-white" draggable="true">
                    <i class="fa-solid fa-list-check w-5 h-5 mr-2 text-center"></i>
                    AI Soru 1 (Çoktan Seçmeli)
                </li>
                 <li class="flex items-center p-3 font-medium text-gray-700 bg-green-50 border border-green-300 rounded-md cursor-grab transition-all duration-150 hover:bg-green-100 hover:border-green-500 hover:text-black active:cursor-grabbing active:bg-green-500 active:text-white" draggable="true">
                    <i class="fa-solid fa-align-left w-5 h-5 mr-2 text-center"></i>
                    AI Soru 2 (Açık Uçlu)
                </li>
            </ul>

        </div>
        
    </aside>


    <main class="flex-1 flex flex-col overflow-hidden">
        
  
        <header class="flex justify-between items-center py-4 px-6 bg-white border-b border-gray-200 shadow-sm">
            <input type="text" value="Yeni Sınav Kağıdı" class="text-2xl font-semibold text-gray-800 border-none rounded-md p-1 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Sınav Başlığı Girin...">
            <div class="space-x-3">
                <button class="font-medium rounded-lg text-sm px-6 py-2.5 text-white bg-gray-500 transition hover:bg-gray-600 cursor-pointer">
                    Kaydet
                </button>
                <button type="button" class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800  font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 cursor-pointer">
                    PDF İndir
                </button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 flex justify-center bg-gray-100">
            
   
            <div id="a4-page" class="bg-white w-[21cm] min-h-[29.7cm] shadow-xl p-[2cm] box-border relative">
                
  

                <div class="absolute border border-dashed border-transparent p-2 hover:border-blue-500 hover:cursor-move" style="top: 2cm; left: 2cm; width: 17cm;">
                    <div class="text-center">
                        <h2 class="text-xl font-bold">Atatürk Üniversitesi</h2>
                        <h3 class="text-lg font-semibold">Mühendislik Fakültesi - 2025 Güz Dönemi</h3>
                        <h4 class="text-base font-medium">BM-101 Programlamaya Giriş Vize Sınavı</h4>
                    </div>
                </div>

                <div class="absolute border border-dashed border-transparent p-4 hover:border-blue-500 hover:cursor-move bg-gray-50 border-gray-200 rounded-md" style="top: 6cm; left: 2cm; width: 17cm;">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr>
                                <td class="w-1/2 pb-2"><strong>Ad Soyad:</strong> _________________________</td>
                                <td class="w-1/2 pb-2"><strong>Puan:</strong> _____ / 100</td>
                            </tr>
                            <tr>
                                <td><strong>Öğrenci No:</strong> _________________________</td>
                                <td><strong>Tarih:</strong> 30.10.2025</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

               
                <div class="absolute border border-dashed border-transparent p-2 hover:border-blue-500 hover:cursor-move" style="top: 9cm; left: 2cm; width: 17cm;">
                    <p class="font-bold">Soru 1 (10 Puan): <span class="font-normal">Aşağıdakilerden hangisi bir programlama dili değildir?</span></p>
                  
                    <ol class="list-[upper-alpha] list-inside pl-4 mt-2 space-y-1">
                        <li>Python</li>
                        <li>HTML</li>
                        <li>Java</li>
                        <li>C++</li>
                    </ol>
                </div>

                <div class="mt-12 absolute border border-dashed border-transparent p-2 hover:border-blue-500 hover:cursor-move" style="top: 12cm; left: 2cm; width: 17cm;">
                    <p class="font-bold">Soru 2 (15 Puan): <span class="font-normal">"Değişken" (variable) kavramını tek bir cümle ile açıklayınız.</span></p>
                    <div class="w-full h-[100px] border border-gray-300 bg-gray-50 mt-2 rounded-md">
                    </div>
                </div>

            </div>
           

        </div>
        

    </main>


</div>

@endsection