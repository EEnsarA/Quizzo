@extends("layouts.app")
@props(["examPaper"])

@section("content")

<div x-data="examCanvas({{ Js::from([
    'token' => csrf_token(),
    'initialElements' => isset($examPaper) ? $examPaper->canvas_data : [], // Kayıtlı elemanlar
    'examTitle' => isset($examPaper) ? $examPaper->title : 'Yeni Sınav Kağıdı',
    'examId' => isset($examPaper) ? $examPaper->id : null // ID varsa Update modudur
    ]) }})"
    class="flex h-screen bg-[#1e1e1e] font-sans overflow-hidden">
    
    {{-- 1. SOL SIDEBAR (KAYNAK) --}}
    <x-exam_create_sidebar />

    {{-- 2. ANA DÜZENLEYİCİ (TARGET) CANVAS --}}
    <x-exam_create_canvas />

    {{-- 3. SAĞ AYAR PANELİ (ÖZELLİKLER) - Geri Eklendi --}}
    <x-exam_create_properties />

    {{-- 4.Modals --}}
    <x-exam_create_modals />
    
   


@endsection