@extends("layouts.app")

@section("content")

<div x-data="examCanvas()" x-init="init()" class="flex h-screen bg-[#1e1e1e] font-sans overflow-hidden">
    
    <x-exam_create_sidebar />


    <x-exam_create_canvas />

 
    <x-exam_create_properties />

 
    <x-exam_create_modals />
    
   


@endsection