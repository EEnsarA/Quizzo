@extends("layouts.app")


@props(['result','rankings'])

@section("content")
@use("App\Enums\Difficulty")



<div class="w-full grid grid-cols-1 md:grid-cols-3 gap-12 p-8">
  
    <div class="md:col-span-2 space-y-6 ">
        
        <x-quiz_result_summary :result="$result" />

        @foreach($result->details as $index => $detail)
            @php
                $question = $result->quiz->questions->firstWhere('id', $detail['question_id']);
                $img = 'storage/' . $question->img_url
            @endphp

            <div class="bg-gray-300 text-[#1A1B1C] rounded-xl shadow p-6 mb-6">
                <h3 class="font-bold mb-4">
                    Q{{ $index+1 }}
                </h3>
                @if ($question->img_url)
                    <div class="mb-6 rounded-lg overflow-hidden">
                        <img src="{{ asset($img) }}" class="w-full h-48 object-cover">
                    </div>
                @else 
                    <div class="mb-6 rounded-lg overflow-hidden">
                       
                    </div>
                @endif
                <div class="pb-6 pt-6 font-semibold leading-12 text-2xl">
                    {{ $question->question_text }}
                </div>

                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6">
                    @foreach($question->answers as $ansIndex => $ans)
                        <li class="p-3 rounded-xl border-2 border-black
                            @if($ans->id == $detail['correct_answer'] ) border-green-500 bg-green-100
                            @elseif($ans->id == $detail['given_answer'] && !$detail['is_correct']) border-red-500 bg-red-100
                            @else bg-gray-100 text-gray-800 border-gray-300 hover:bg-gray-200 hover:border-gray-400
                            @endif
                        ">
                             <span class="font-bold">{{ chr(65 + $ansIndex) }}) </span> {{ $ans->answer_text }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>


    <div>
        <x-success_rank_sidebar :quiz="$result->quiz" :rankings="$rankings" :current_user_id="$result->user_id ?? $result->session_id"/>
    </div>
</div>




@endsection

