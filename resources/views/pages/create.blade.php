 @extends("layouts.app")

 @section("content")
 
    <div class="mt-5 p-4 w-full h-full ">
            <div class="w-full text-[#F2EDE4] mt-5 p-8 md:p-12">
                <div class="text-center mb-8">
                    <h1 class="text-4xl md:text-5xl font-extrabold  tracking-tight leading-none mb-2">Quizzo</h1>
                    <p class="text-lg md:text-xl  font-medium">Bilginin Sınırlarını Keşfet: Quizzo ile Kendi Quizzini Oluştur!</p>
                </div>
        
                <form action="#" method="POST" class="space-y-6">
                    <div class="flex flex-col">
                        <label for="topic" class=" font-semibold mb-2">Konu:</label>
                        <input
                            type="text"
                            id="topic"
                            placeholder="Örn: 'Türkiye Cumhuriyeti Tarihi' veya 'Python Programlama'"
                            class="px-4 py-2 border border-gray-300 rounded-lg "
                        />
                    </div>
        
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col">
                            <label for="question-count" class=" font-semibold mb-2">Soru Sayısı:</label>
                            <input
                                type="number"
                                id="question-count"
                                value="5"
                                min="1"
                                max="5"
                                class="px-4 py-2 border border-gray-300 rounded-lg "
                            />  
                            <div class=" mt-2">
                                <span class=" text-sm text-[#BFBFBD]" >soru sayısı en fazla 5 olabilir daha fazlası için</span>
                                <a href="" class=" text-sm text-[#6e879a] hover:text-[#386282]">Premiuma Geçin</a>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label for="option-count" class=" font-semibold mb-2">Seçenek Sayısı:</label>
                            <input
                                type="number"
                                id="option-count"
                                value="4"
                                min="2"
                                max="5"
                                class="px-4 py-2 border border-gray-300 rounded-lg  "
                            />
                        </div>
                    </div>
        
                    <button type="submit" class="w-full py-3 rounded-lg text-white font-bold transition-all duration-300 transform bg-[#41825e] hover:bg-[#357652] hover:scale-105 shadow-lg cursor-pointer">
                        Testi Oluştur
                    </button>
                </form>
            </div>
            <div class="w-full h-120 p-8 md:p-12 text-[#F2EDE4]">
                Oluşturuluyor..
            </div>
    </div>

@endsection    


