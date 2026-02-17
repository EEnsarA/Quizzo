@props(['paper'])

{{-- Kullanıcı Avatar Mantığı --}}
@php
    $userAvatar = ($paper->user && $paper->user->avatar_url)
        ? asset('storage/' . $paper->user->avatar_url)
        : null;
@endphp

<div
    class="bg-[#2d2d30] border border-gray-700/50 rounded-xl overflow-hidden shadow-lg hover:border-blue-500 hover:shadow-blue-900/20 transition-all duration-300 group h-full flex flex-col relative">

    <div class="p-5 flex flex-col h-full">

        {{-- HEADER (İkon, Tarih, Kullanıcı) --}}
        <div class="flex justify-between items-start mb-4">
            <div
                class="bg-[#252526] text-blue-400 p-2.5 rounded-lg border border-gray-700 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-500 transition-colors duration-300 shadow-sm">
                <i class="fa-solid fa-file-contract text-xl"></i>
            </div>

            <div class="flex flex-col items-end gap-1.5">
                <span class="text-[10px] text-gray-500 font-mono">
                    {{ $paper->created_at->diffForHumans() }}
                </span>

                @if($paper->user)
                    <div class="flex items-center gap-2 bg-[#252526] px-2.5 py-1 rounded-full border border-gray-700/50">
                        @if($userAvatar)
                            <img src="{{ $userAvatar }}" class="w-4 h-4 rounded-full object-cover ring-1 ring-gray-600">
                        @else
                            <i class="fa-regular fa-circle-user text-indigo-400 text-sm"></i>
                        @endif
                        <span class="text-xs font-bold text-gray-300 truncate max-w-[100px]">
                            {{ $paper->user->name }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- BODY (Başlık, Açıklama, Kategori) --}}
        <div class="mb-4">
            <h3 class="text-white font-bold text-lg mb-2 truncate leading-tight tracking-wide"
                title="{{ $paper->title }}">
                {{ $paper->title }}
            </h3>

            @if($paper->description)
                <p class="text-gray-400 text-xs line-clamp-2 mb-3 min-h-[2.5em] leading-relaxed"
                    title="{{ $paper->description }}">
                    {{ $paper->description }}
                </p>
            @else
                <p class="text-gray-600 text-xs italic mb-3 min-h-[2.5em]">Açıklama yok.</p>
            @endif

            <div class="flex flex-wrap gap-1.5">
                @forelse($paper->categories as $cat)
                    <span
                        class="text-[10px] font-semibold bg-[#3e3e42] text-gray-300 border border-gray-600 px-2 py-0.5 rounded-md">
                        {{ $cat->name }}
                    </span>
                @empty
                    <span class="text-[10px] text-transparent select-none">.</span>
                @endforelse
            </div>
        </div>

        {{-- FOOTER (Bilgiler ve Butonlar) --}}
        <div class="mt-auto pt-4 border-t border-gray-700/50">

            {{-- YENİ EKLENEN KISIM: İSTATİSTİKLER --}}
            <div class="flex justify-between items-center mb-3 text-xs font-medium text-gray-400">
                <span class="flex items-center gap-1.5">
                    <i class="fa-regular fa-file"></i> {{ $paper->page_count }} Sayfa
                </span>

                {{-- İndirilme Sayısı --}}
                <span class="flex items-center gap-1.5 bg-[#252526] px-2 py-0.5 rounded border border-gray-700/50">
                    <i class="fa-solid fa-download text-blue-400"></i> {{ $paper->downloads_count }} kez
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button type="button" @click.prevent="$dispatch('trigger-preview', { id: {{ $paper->id }} })"
                    class="bg-[#363639] hover:bg-[#45454a] text-gray-200 hover:text-white py-2 rounded-lg transition flex items-center justify-center border border-gray-600 hover:border-gray-500 cursor-pointer gap-2 text-xs font-bold shadow-sm">
                    <i class="fa-solid fa-eye text-blue-400"></i> Ön İzle
                </button>

                <button type="button" @click.prevent="$dispatch('trigger-download', { id: {{ $paper->id }} })"
                    class="bg-[#3e3e42] text-white hover:bg-[#4e4e52] py-2 rounded-lg transition flex items-center justify-center shadow-sm cursor-pointer gap-2 text-xs font-bold border border-transparent hover:border-gray-600">
                    <i class="fa-regular fa-file-pdf"></i> İndir
                </button>
            </div>
        </div>
    </div>
</div>