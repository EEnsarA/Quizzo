@props(['guide'])

@php
    $userAvatar = ($guide->user && $guide->user->avatar_url)
        ? asset('storage/' . $guide->user->avatar_url)
        : null;
@endphp


<div x-data="{ 
        loading: false,
        forkGuide() {
            this.loading = true;
            axios.post('/exam/' + {{ $guide->id }} + '/fork')
                .then(res => {
                    if(res.data.success) {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: res.data.message, type: 'success' } }));
                        setTimeout(() => {
                            window.location.href = res.data.redirect;
                        }, 1000);
                    }
                })
                .catch(err => {
                    console.error(err);
                    let msg = err.response?.data?.message || 'İşlem başarısız.';
                    if (err.response?.status === 401) msg = 'Lütfen önce giriş yapın.';
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: 'error' } }));
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }"
    class="bg-[#252526] border border-gray-700 rounded-xl overflow-hidden shadow-lg hover:border-gray-500 hover:shadow-gray-700/20 transition-all duration-300 group h-full flex flex-col relative">

    <div class="p-5 flex flex-col h-full">

        {{-- Üst Kısım: İkon ve Yazar Bilgisi --}}
        <div class="flex justify-between items-start mb-3">

            {{-- GÜNCELLENDİ: Gri Tema ve file-lines --}}
            <div
                class="bg-gray-800 text-gray-400 p-2 rounded-lg border border-gray-600 group-hover:bg-gray-700 group-hover:text-gray-200 transition-colors duration-300">
                <i class="fa-regular fa-file-lines text-xl px-1"></i>
            </div>

            <div class="flex flex-col items-end gap-1.5">
                <span class="text-[10px] text-gray-500 font-mono">
                    {{ $guide->created_at->diffForHumans() }}
                </span>

                {{-- YENİ: Senin Profil Avatar Yapın --}}
                @if($guide->user)
                    <div class="flex items-center gap-2 bg-[#252526] px-2.5 py-1 rounded-full border border-gray-700/50">
                        @if($userAvatar)
                            <img src="{{ $userAvatar }}" class="w-4 h-4 rounded-full object-cover ring-1 ring-gray-600">
                        @else
                            <i class="fa-regular fa-circle-user text-indigo-400 text-sm"></i>
                        @endif
                        <span class="text-xs font-bold text-gray-300 truncate max-w-[100px]">
                            {{ $guide->user->name }}
                        </span>
                    </div>
                @endif

            </div>
        </div>

        {{-- Orta Kısım: Başlık, Açıklama ve Kategoriler --}}
        <div class="mb-4">
            <h3 class="text-white font-bold text-lg mb-1 truncate leading-tight" title="{{ $guide->title }}">
                {{ $guide->title }}
            </h3>

            @if($guide->description)
                <p class="text-gray-400 text-xs line-clamp-2 mb-2 min-h-[2.5em]" title="{{ $guide->description }}">
                    {{ $guide->description }}
                </p>
            @else
                <p class="text-gray-600 text-xs italic mb-2 min-h-[2.5em]">Açıklama yok.</p>
            @endif

            <div class="flex flex-wrap gap-1 mt-2">
                @forelse($guide->categories as $cat)
                    {{-- GÜNCELLENDİ: Kategori Etiketleri Gri Temaya Uygun --}}
                    <span class="text-[10px] bg-gray-800 text-gray-300 border border-gray-600 px-1.5 py-0.5 rounded">
                        {{ $cat->name }}
                    </span>
                @empty
                    <span class="text-[10px] text-transparent select-none">.</span>
                @endforelse
            </div>
        </div>

        {{-- Alt Kısım: İstatistikler ve Butonlar --}}
        <div class="mt-auto">
            <div class="flex items-center justify-between border-t border-gray-700 pt-3 mb-3">
                <p class="text-gray-500 text-xs font-medium flex items-center gap-1.5">
                    <i class="fa-regular fa-copy"></i> {{ $guide->page_count }} Sayfa
                </p>
                <p class="text-gray-500 text-xs font-medium flex items-center gap-1.5" title="İndirilme Sayısı">
                    <i class="fa-solid fa-download"></i> {{ $guide->downloads_count ?? 0 }}
                </p>
            </div>

            <div class="space-y-2">
                {{-- Ana Eylem: Kütüphaneye Kopyala (Fork) - GÜNCELLENDİ: Gri/Beyaz Tema --}}
                <button type="button" @click.prevent="forkGuide()" :disabled="loading"
                    class="w-full bg-gray-600/10 hover:bg-gray-600 text-gray-300 hover:text-white border border-gray-600/50 py-2 rounded-lg text-xs font-bold transition cursor-pointer flex items-center justify-center gap-2 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">

                    {{-- Yüklenmiyorken GitHub Fork ikonu göster --}}
                    <i x-show="!loading" class="fa-solid fa-code-branch"></i>
                    {{-- Yüklenirken dönen ikon --}}
                    <i x-show="loading" class="fa-solid fa-circle-notch animate-spin" style="display: none;"></i>

                    <span x-show="!loading">Kopyala ve Düzenle</span>
                    <span x-show="loading" style="display: none;">Kopyalanıyor...</span>
                </button>

                {{-- İkincil Eylemler: Ön İzle ve İndir --}}
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click.prevent="$dispatch('trigger-preview', { id: {{ $guide->id }} })"
                        title="Ön İzle"
                        class="bg-[#323233] hover:bg-[#3e3e40] text-gray-300 hover:text-white py-2 rounded-lg transition flex items-center justify-center border border-white/5 hover:border-white/10 cursor-pointer gap-2 text-sm">
                        <i class="fa-solid fa-eye"></i> Ön İzle
                    </button>

                    <button type="button" @click.prevent="$dispatch('trigger-download', { id: {{ $guide->id }} })"
                        title="PDF İndir"
                        class="bg-[#323233] hover:bg-emerald-600/20 hover:text-emerald-400 text-gray-300 py-2 rounded-lg transition flex items-center justify-center border border-white/5 hover:border-emerald-500/30 cursor-pointer gap-2 text-sm">
                        <i class="fa-solid fa-file-pdf"></i> İndir
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>