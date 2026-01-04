<footer class="w-full bg-[#1A1B1C] shadow-neutral-500 shadow-xl  text-gray-400 mt-auto">
    <div class="max-w-7xl mx-auto px-6 py-12">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
            
            <div class="space-y-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group w-fit">
                    <span class="text-2xl font-major-mono font-extrabold text-white tracking-widest  transition-colors">Quizzo</span>
                </a>
                <p class="text-sm leading-relaxed max-w-xs">
                    Bilgini test et, sınırlarını zorla. Kendi quizlerini oluştur veya topluluğun hazırladığı içerikleri keşfet.
                </p>
                <div class="flex gap-4 pt-2">
                    <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 hover:bg-[#41825e] hover:text-white transition-all">
                        <i class="fa-brands fa-twitter"></i>
                    </a>
                    <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 hover:bg-[#41825e] hover:text-white transition-all">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-white font-bold mb-4">Platform</h3>
                <ul class="space-y-3 text-sm">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-gray-200 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-angle-right text-xs"></i> Ana Sayfa
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('library.show') }}" class="hover:text-gray-200  transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-angle-right text-xs"></i> Kütüphane
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('quiz.create') }}" class="hover:text-gray-200  transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-angle-right text-xs"></i> Quiz Oluştur
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('exam.create') }}" class="hover:text-gray-200  transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-angle-right text-xs"></i> Sınav Hazırla
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold mb-4">Geliştirici</h3>
                <div class="bg-[#212121] border border-gray-700 p-4 rounded-xl flex items-center gap-4 group hover:border-gray-600 transition-colors">
                    <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center text-white text-2xl group-hover:bg-gray-700 transition-colors">
                        <i class="fa-brands fa-github"></i>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Ensar A.</p>
                        <a href="https://github.com/EEnsarA" target="_blank" class="text-xs text-blue-400 hover:text-blue-300 font-mono">
                            @EEnsarA
                        </a>
                    </div>
                </div>
                <p class="text-xs mt-4 text-gray-500">
                    &copy; {{ date('Y') }} Quizzo. Tüm hakları saklıdır.
                </p>
            </div>

        </div>
    </div>
</footer>