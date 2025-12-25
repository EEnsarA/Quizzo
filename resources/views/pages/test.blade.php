@extends("layouts.app")

@section("content")

<div x-data="examCanvas()" x-init="init()" class="flex h-screen bg-[#1e1e1e] font-sans overflow-hidden">
    
    {{-- 1. SOL SIDEBAR (KAYNAK) --}}
    <x-exam_create_sidebar />

    {{-- 2. ANA DÜZENLEYİCİ (TARGET) CANVAS --}}
    <x-exam_create_canvas />

    {{-- 3. SAĞ AYAR PANELİ (ÖZELLİKLER) - Geri Eklendi --}}
    <x-exam_create_properties />

    {{-- 4.Modals --}}
    <x-exam_create_modals />
    
   


@endsection