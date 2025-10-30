@extends("layouts.app")
@props(["quizzes"])
@section('content')

    <div class="mt-2 p-4 w-full h-full ">
        <div class="w-full text-[#F2EDE4] mt-2 p-4 md:p-12">
            <div class="text-left mb-8">
                <h1 class="text-2xl md:text-3xl font-extrabold  leading-none mb-2">Quizzo'ya Hoşgeldin</h1> 
            </div>
            <div class="mt-2 flex justify-center gap-x-24 gap-y-4 p-5 flex-wrap">
                <a href="{{ route('exam.create') }}"
                    class="w-72 text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-4 text-center  mb-2 cursor-pointer">
                    Sınav Oluştur <i class="fa-solid fa-right-long"></i> PDF Dönüştür <i class="fa-solid fa-file"></i>
                </a>
                <a href="{{ route('quiz.create') }}" class="w-72 text-white bg-gradient-to-br from-green-400 to-blue-600 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-green-200  font-medium rounded-lg text-sm px-5 py-4 text-center cursor-pointer mb-2">
                    Online Quiz Oluştur <i class="fa-solid fa-check"></i>
                </a>
            </div>

            <div class="text-left mb-8 mt-12">
                <h1 class="text-2xl md:text-3xl font-extrabold  leading-none mb-2">Çokça Çözülmüş Quizzolar</h1> 
            </div>
            <div class="mt-18 grid 2xl:grid-cols-4 xl:grid-cols-3 lg:grid-cols-3 md:grid-cols-3 sm:grid-cols-2  gap-6">
                    @foreach ($quizzes as $quiz)
                        <x-quiz_card :quiz="$quiz"/>
                    @endforeach
            </div>
        </div>  

    </div>

@endsection