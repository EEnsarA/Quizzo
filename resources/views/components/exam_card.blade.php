@props(['paper'])

<div class="bg-[#252526] border border-gray-700 rounded-xl overflow-hidden shadow-lg hover:border-blue-500 hover:shadow-blue-900/20 transition-all duration-300 group h-full flex flex-col">
    
    {{-- ÜST KISIM: İKON VE BİLGİ --}}
    <div class="p-5 flex flex-col h-full">
        <div class="flex justify-between items-start mb-4">
            {{-- Dosya İkonu --}}
            <div class="bg-blue-900/20 text-blue-400 p-3 rounded-lg border border-blue-500/10 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                <i class="fa-solid fa-file-contract text-2xl"></i>
            </div>
            {{-- Tarih --}}
            <span class="text-[10px] text-gray-500 font-mono bg-black/20 px-2 py-1 rounded border border-white/5">
                {{ $paper->created_at->diffForHumans() }}
            </span>
        </div>
        
        {{-- Başlık ve Sayfa Sayısı --}}
        <h3 class="text-white font-bold text-lg mb-1 truncate leading-tight" title="{{ $paper->title }}">
            {{ $paper->title }}
        </h3>
        <p class="text-gray-400 text-xs font-medium mb-5 flex items-center gap-1.5">
            <i class="fa-regular fa-copy"></i> {{ $paper->page_count }} Sayfa
        </p>
        
        {{-- ALT AKSİYONLAR (Grid Yapısı) --}}
        <div class="mt-auto space-y-2">
            
            {{-- 1. Satır: Ana Buton (DÜZENLE) --}}
            <a href="{{ route('exam.edit', $paper->id) }}" 
               class="block w-full bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold py-2 rounded-lg text-center transition shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Düzenle
            </a>

            {{-- 2. Satır: Yan Aksiyonlar (Ön İzle | İndir | Sil) --}}
            <div class="grid grid-cols-3 gap-2">
                
                {{-- ÖN İZLE (Button + Dispatch) --}}
                <button type="button" 
                        @click.prevent="$dispatch('trigger-preview', { id: {{ $paper->id }} })"
                        title="Ön İzle"
                        class="bg-[#323233] hover:bg-[#3e3e40] text-gray-300 hover:text-white py-2 rounded-lg transition flex items-center justify-center border border-white/5 hover:border-white/10 cursor-pointer">
                    <i class="fa-solid fa-eye"></i>
                </button>

                {{-- İNDİR (Button + Dispatch) --}}
                <button type="button"
                        @click.prevent="$dispatch('trigger-download', { id: {{ $paper->id }} })"
                        title="PDF İndir"
                        class="bg-[#323233] hover:bg-emerald-600/20 hover:text-emerald-400 text-gray-300 py-2 rounded-lg transition flex items-center justify-center border border-white/5 hover:border-emerald-500/30 cursor-pointer">
                    <i class="fa-solid fa-file-pdf"></i>
                </button>

                {{-- SİL (Form olarak kalabilir, burası doğru) --}}
                <form action="{{ route('exam.delete', $paper->id) }}" method="POST" 
                      onsubmit="return confirm('Bu sınav kağıdını silmek istediğine emin misin?');"
                      class="h-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            title="Sil"
                            class="w-full h-full bg-[#323233] hover:bg-red-600/20 hover:text-red-400 text-gray-300 rounded-lg transition flex items-center justify-center border border-white/5 hover:border-red-500/30">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>