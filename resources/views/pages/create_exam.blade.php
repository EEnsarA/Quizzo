@extends("layouts.app")
@props(["examPaper"])

@section("content")

<div x-data="examCanvas({{ Js::from([
    'token' => csrf_token(),
    'initialElements' => isset($examPaper) ? $examPaper->canvas_data : [], // Kayıtlı elemanlar
    'examTitle' => isset($examPaper) ? $examPaper->title : 'Yeni Sınav Kağıdı',
    'examId' => isset($examPaper) ? $examPaper->id : null // ID varsa Update modudur
    ]) }})"
    class="flex relative w-full h-[calc(100vh-64px)] bg-[#1e1e1e] font-sans overflow-hidden">
    
    {{-- 1. SOL SIDEBAR (KAYNAK) --}}
    <x-exam_create_sidebar />

    <div class="flex-1 relative h-full min-w-0 transition-[padding] duration-300 ease-in-out"
     :class="{'pr-0': !selectedItem, 'pr-72': selectedItem}">
        {{-- 2. ANA DÜZENLEYİCİ (TARGET) CANVAS --}}
        <x-exam_create_canvas />
        {{-- 4.Modals --}}
        <x-exam_create_modals />
    </div>
    
    {{-- 3. SAĞ AYAR PANELİ (ÖZELLİKLER) - Geri Eklendi --}}
    <x-exam_create_properties />
    
    
    {{-- İSİM DEĞİŞTİRME MODALI --}}
    <div x-show="showTitleModal" 
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm"
        x-transition.opacity
        style="display: none;">

        <div class="bg-[#252526] w-full max-w-md p-6 rounded-xl border border-gray-700 shadow-2xl transform transition-all"
            @click.away="showTitleModal = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">

            <h3 class="text-xl font-bold text-white mb-2">Sınavına Bir İsim Ver 📄</h3>
            <p class="text-gray-400 text-sm mb-4">Kaydetmeden önce sınav kağıdın için açıklayıcı bir başlık belirle.</p>

            {{-- Input --}}
            <input type="text" 
                x-model="tempTitle" 
                @keydown.enter="saveTitleAndContinue()"
                class="w-full bg-[#1e1e1e] border border-gray-600 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition mb-4"
                placeholder="Örn: Matematik 1. Dönem Vizesi">

            <div class="flex justify-end gap-3">
                <button @click="showTitleModal = false" class="px-4 py-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition text-sm font-medium">
                    Vazgeç
                </button>
                <button @click="saveTitleAndContinue()" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition text-sm font-bold shadow-lg shadow-blue-900/20">
                    Kaydet ve Devam Et
                </button>
            </div>
        </div>
    </div>


@endsection