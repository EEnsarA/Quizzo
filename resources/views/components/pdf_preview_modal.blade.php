<div x-show="showPreviewModal" 
     style="display: none;"
     class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
     x-transition.opacity>

    <div class="bg-[#252526] w-full max-w-5xl h-[90vh] rounded-xl border border-gray-700 shadow-2xl flex flex-col relative"
         @click.away="showPreviewModal = false">
        
        {{-- Modal Başlık ve Kapat --}}
        <div class="flex justify-between items-center p-4 border-b border-gray-700 bg-[#1e1e1e] rounded-t-xl">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <i class="fa-solid fa-print text-blue-500"></i> Ön İzleme
            </h3>
            <button @click="showPreviewModal = false" class="text-gray-400 hover:text-white hover:bg-white/10 p-2 rounded-lg transition">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        {{-- PDF Görüntüleyici (Iframe) --}}
        <div class="flex-1 bg-gray-100 relative w-full h-full">
            {{-- Loading Spinner --}}
            <div x-show="iframeLoading" class="absolute inset-0 flex items-center justify-center bg-white z-10">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent"></div>
            </div>

            <iframe 
                x-show="previewUrl"
                :src="previewUrl" 
                class="w-full h-full" 
                frameborder="0"
                @load="iframeLoading = false">
            </iframe>
        </div>
    </div>
</div>