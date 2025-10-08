@extends("layouts.app")
@props(['myQuizzos','libraryQuizzos'])
@section("content")

    <div class="mt-2 p-4 w-full h-full ">
        <div class="w-full text-[#F2EDE4] mt-2 p-4 md:p-12">
            <div class="text-left mb-4">
                <h1 class="text-3xl md:text-4xl font-extrabold leading-none">My Library</h1> 
            </div>
            <div class="p-4 pl-4">
                <div class="text-left mt-12 mb-6">
                    <h1 class="text-2xl md:text-3xl font-semibold leading-none">Added Quizzos</h1> 
                </div>
                <div class="mt-18 grid 2xl:grid-cols-4 xl:grid-cols-3 lg:grid-cols-3 md:grid-cols-3 sm:grid-cols-2  gap-6">
                        @foreach ($libraryQuizzos as $quiz)
                            <x-quiz_library_card :quiz="$quiz"/>
                        @endforeach
                </div>
                <div class="text-left mt-12 mb-6">
                    <h1 class="text-2xl md:text-3xl font-semibold leading-none">My Quizzos</h1> 
                </div>
                <div class="mt-18 grid 2xl:grid-cols-4 xl:grid-cols-3 lg:grid-cols-3 md:grid-cols-3 sm:grid-cols-2  gap-6">
                        @foreach ($myQuizzos as $quiz)
                            <x-quiz_library_card :quiz="$quiz" :myQuiz="true" />
                        @endforeach
                </div>
            </div>
        </div>  

    </div>

@endsection