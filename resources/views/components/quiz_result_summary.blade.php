@props(['result'])

<div class=" rounded-xl border border-gray-700 shadow-xl overflow-hidden relative">

    {{-- Arka plan deseni --}}
    <div
        class="absolute top-0 right-0 w-48 h-48 bg-blue-600/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none">
    </div>

    <div class="p-5 md:p-6 flex flex-col md:flex-row items-center gap-6 relative z-10">

        {{-- Kullanıcı Profili --}}
        <div class="flex flex-col items-center md:items-start min-w-[140px]">
            <div class="relative mb-2">
                @if($result->user?->avatar_url)
                    <img src="{{ asset('storage/' . $result->user->avatar_url) }}"
                        class="w-14 h-14 rounded-full border-2 border-gray-700 object-cover shadow-lg">
                @else
                    <div
                        class="w-14 h-14 rounded-full bg-gray-700 flex items-center justify-center text-xl text-gray-400 border-2 border-gray-600">
                        <i class="fa-solid fa-user"></i>
                    </div>
                @endif
                <div
                    class="absolute -bottom-1 -right-1 bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full border border-gray-800 shadow-sm">
                    #{{ $result->attempt_number }}
                </div>
            </div>

            <h2 class="text-sm font-bold text-white">
                {{ $result->user?->name ?? 'Guest-' . substr($result->session_id, 0, 4) }}
            </h2>
            <p class="text-[10px] text-gray-400 mt-0.5">
                {{ $result->created_at->format('d M Y, H:i') }}
            </p>
        </div>

        {{-- Skor Kartları --}}
        <div class="flex-1 w-full grid grid-cols-2 sm:grid-cols-4 gap-3">

            {{-- DOĞRU --}}
            <div
                class="bg-emerald-900/20 border border-emerald-500/30 py-3 rounded-lg text-center flex flex-col justify-center">
                <div class="text-xl font-bold text-emerald-400 leading-none mb-1">{{ $result->correct_count }}</div>
                <div class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest">Doğru</div>
            </div>

            {{-- YANLIŞ --}}
            <div
                class="bg-rose-900/20 border border-rose-500/30 py-3 rounded-lg text-center flex flex-col justify-center">
                <div class="text-xl font-bold text-rose-400 leading-none mb-1">{{ $result->wrong_count }}</div>
                <div class="text-[9px] font-bold text-rose-600 uppercase tracking-widest">Yanlış</div>
            </div>

            {{-- BOŞ --}}
            <div
                class="bg-gray-700/30 border border-gray-600/30 py-3 rounded-lg text-center flex flex-col justify-center">
                <div class="text-xl font-bold text-gray-300 leading-none mb-1">{{ $result->empty_count }}</div>
                <div class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Boş</div>
            </div>

            {{-- NET --}}
            <div
                class="bg-blue-900/20 border border-blue-500/30 py-3 rounded-lg text-center sm:col-span-1 col-span-2 flex flex-col justify-center">
                <div class="text-2xl font-extrabold text-blue-400 leading-none mb-1">{{ $result->net }}</div>
                <div class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">NET</div>
            </div>

        </div>
    </div>

    {{-- Alt Bilgi Şeridi --}}
    <div class="bg-gray-900/50 px-5 py-2.5 flex justify-between items-center border-t border-gray-700/50 text-xs">
        <div class="flex items-center text-gray-400">
            <i class="fa-regular fa-clock mr-1.5 text-blue-400 text-sm"></i>
            Toplam Süre: <span class="text-white font-mono ml-1.5 font-bold">{{ floor($result->time_spent / 60) }}dk
                {{ $result->time_spent % 60 }}sn</span>
        </div>
    </div>
</div>