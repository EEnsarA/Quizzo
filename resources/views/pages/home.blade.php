@extends("layouts.app")
@props(["quizzes"])
@section('content')

    <div class="mt-2 p-4 w-full h-full ">
        <div class="w-full text-[#F2EDE4] mt-2 p-4 md:p-12">
            <div class="text-left mb-8">
                <h1 class="text-3xl md:text-4xl font-extrabold  leading-none mb-2">Çokça Çözülmüş Quizzolar</h1> 
            </div>
            <div class="mt-18 grid 2xl:grid-cols-4 xl:grid-cols-3 lg:grid-cols-3 md:grid-cols-3 sm:grid-cols-2  gap-6">
                    @foreach ($quizzes as $quiz)
                        <x-quiz_card :quiz="$quiz"/>
                    @endforeach
            </div>
        </div>  

    </div>

@endsection