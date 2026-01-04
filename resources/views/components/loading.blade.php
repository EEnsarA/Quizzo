<div x-data="{ show: false }"
     @toggle-loading.window="show = $event.detail"
     x-show="show"
     x-transition.opacity.duration.300ms
     class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/60 backdrop-blur-sm"
     style="display: none;">
    
    <div class="flex flex-col items-center gap-4">
        <div class="relative w-16 h-16">
            <div class="absolute inset-0 border-4 border-gray-600 rounded-full opacity-25"></div>
            <div class="absolute inset-0 border-4 border-emerald-500 rounded-full border-t-transparent animate-spin"></div>
        </div>
        
        <span class="text-white font-bold tracking-wider animate-pulse">İŞLEM YAPILIYOR...</span>
    </div>
</div>