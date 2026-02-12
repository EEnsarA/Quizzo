@props(['paper'])

<div x-data="{ 
        isPublic: {{ $paper->is_public ? 'true' : 'false' }}, 
        loading: false,
        togglePublic() {
            this.loading = true;
            axios.post('/exam/{{ $paper->id }}/toggle-public')
                .then(res => {
                    this.isPublic = res.data.is_public;
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: res.data.message, type: 'success' } }));
                })
                .catch(err => {
                    console.error(err);
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'İşlem başarısız.', type: 'error' } }));
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }"
    class="bg-[#252526] border border-gray-700 rounded-xl overflow-hidden shadow-lg hover:border-blue-500 hover:shadow-blue-900/20 transition-all duration-300 group h-full flex flex-col relative">

    <div class="p-5 flex flex-col h-full">

        <div class="flex justify-between items-start mb-3">

            <div
                class="bg-blue-900/20 text-blue-400 p-2 rounded-lg border border-blue-500/10 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                <i class="fa-solid fa-file-contract text-xl"></i>
            </div>

            <div class="flex flex-col items-end gap-2">
                <span class="text-[10px] text-gray-500 font-mono">
                    {{ $paper->created_at->diffForHumans() }}
                </span>

                <button @click="togglePublic()" :disabled="loading"
                    class="text-[10px] font-bold px-2 py-1 rounded border transition-colors flex items-center gap-1"
                    :class="isPublic 
                        ? 'bg-emerald-900/30 text-emerald-400 border-emerald-500/30 hover:bg-emerald-900/50' 
                        : 'bg-gray-800 text-gray-400 border-gray-600 hover:bg-gray-700'">

                    <i class="fa-solid"
                        :class="loading ? 'fa-circle-notch animate-spin' : (isPublic ? 'fa-globe' : 'fa-lock')"></i>
                    <span x-text="isPublic ? 'PUBLIC' : 'PRIVATE'"></span>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <h3 class="text-white font-bold text-lg mb-1 truncate leading-tight" title="{{ $paper->title }}">
                {{ $paper->title }}
            </h3>

            @if($paper->description)
                <p class="text-gray-400 text-xs line-clamp-2 mb-2 min-h-[2.5em]" title="{{ $paper->description }}">
                    {{ $paper->description }}
                </p>
            @else
                <p class="text-gray-600 text-xs italic mb-2 min-h-[2.5em]">Açıklama yok.</p>
            @endif

            <div class="flex flex-wrap gap-1 mt-2">
                @forelse($paper->categories as $cat)
                    <span class="text-[10px] bg-gray-800 text-gray-300 border border-gray-600 px-1.5 py-0.5 rounded">
                        {{ $cat->name }}
                    </span>
                @empty
                    <span class="text-[10px] text-transparent select-none">.</span>
                @endforelse
            </div>
        </div>

        <div class="mt-auto">
            <p class="text-gray-500 text-xs font-medium mb-3 flex items-center gap-1.5 border-t border-gray-700 pt-2">
                <i class="fa-regular fa-copy"></i> {{ $paper->page_count }} Sayfa
            </p>

            <div class="space-y-2">
                <a href="{{ route('exam.edit', $paper->id) }}"
                    class="block w-full bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold py-2 rounded-lg text-center transition shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i> Düzenle
                </a>

                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click.prevent="$dispatch('trigger-preview', { id: {{ $paper->id }} })"
                        title="Ön İzle"
                        class="bg-[#323233] hover:bg-[#3e3e40] text-gray-300 hover:text-white py-2 rounded-lg transition flex items-center justify-center border border-white/5 hover:border-white/10 cursor-pointer">
                        <i class="fa-solid fa-eye"></i>
                    </button>

                    <button type="button" @click.prevent="$dispatch('trigger-download', { id: {{ $paper->id }} })"
                        title="PDF İndir"
                        class="bg-[#323233] hover:bg-emerald-600/20 hover:text-emerald-400 text-gray-300 py-2 rounded-lg transition flex items-center justify-center border border-white/5 hover:border-emerald-500/30 cursor-pointer">
                        <i class="fa-solid fa-file-pdf"></i>
                    </button>

                    <form action="{{ route('exam.delete', $paper->id) }}" method="POST"
                        onsubmit="return confirm('Bu sınav kağıdını silmek istediğine emin misin?');"
                        class="h-full block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Sil"
                            class="w-full h-full bg-[#323233] hover:bg-red-600/20 hover:text-red-400 text-gray-300 rounded-lg transition flex items-center justify-center border border-white/5 hover:border-red-500/30">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>