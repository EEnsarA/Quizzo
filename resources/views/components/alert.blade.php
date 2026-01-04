<div x-data="{ 
        notifications: [],
        add(message, type = 'success') {
            const id = Date.now();
            this.notifications.push({ id, message, type });
            setTimeout(() => this.remove(id), 4000); // Süreyi 4 saniye yaptım, okunması için
        },
        remove(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }"
    @notify.window="add($event.detail.message, $event.detail.type)"
    class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"> 
   

    <template x-for="note in notifications" :key="note.id">
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2 translate-x-2"
             x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             
             class="pointer-events-auto w-full max-w-sm rounded-lg shadow-2xl overflow-hidden border-l-4 backdrop-blur-md relative transform transition-all hover:scale-[1.02]"
             :class="{
                'border-emerald-500 bg-[#1e1e1e]/90 text-white': note.type === 'success',
                'border-red-500 bg-[#1e1e1e]/90 text-white': note.type === 'error',
                'border-blue-500 bg-[#1e1e1e]/90 text-white': note.type === 'info',
                'border-yellow-500 bg-[#1e1e1e]/90 text-white': note.type === 'warning'
             }">
            
            <div class="p-4 flex items-start gap-4">
                {{-- İkon Alanı --}}
                <div class="flex-shrink-0 mt-0.5">
                    <i class="fa-solid text-xl" :class="{
                        'fa-circle-check text-emerald-400': note.type === 'success',
                        'fa-circle-xmark text-red-400': note.type === 'error',
                        'fa-circle-info text-blue-400': note.type === 'info',
                        'fa-triangle-exclamation text-yellow-400': note.type === 'warning'
                    }"></i>
                </div>

                <div class="flex-1">
                    <h3 class="font-bold text-sm mb-0.5 uppercase tracking-wider opacity-70" x-text="note.type === 'success' ? 'Başarılı' : (note.type === 'error' ? 'Hata' : 'Bilgi')"></h3>
                    <p class="text-sm font-medium leading-relaxed opacity-95" x-text="note.message"></p>
                </div>

       
                <button @click="remove(note.id)" class="flex-shrink-0 text-gray-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>


            <div class="h-1 w-full bg-gray-700/50">
                <div class="h-full w-full origin-left animate-[shrink_4s_linear_forwards]"
                     :class="{
                        'bg-emerald-500': note.type === 'success',
                        'bg-red-500': note.type === 'error',
                        'bg-blue-500': note.type === 'info',
                        'bg-yellow-500': note.type === 'warning'
                     }"></div>
            </div>

        </div>
    </template>
</div>

<style>
@keyframes shrink {
    from { transform: scaleX(1); }
    to { transform: scaleX(0); }
}
</style>