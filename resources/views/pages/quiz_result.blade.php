@extends("layouts.app")


@props(['result','rankings'])

@section("content")
@use("App\Enums\Difficulty")


<div class="w-full grid grid-cols-1 md:grid-cols-3 gap-12 p-8">
  
    <div class="md:col-span-2 space-y-6">
        
        <x-quiz_result_summary :result="$result" />

        @foreach($result->details as $index => $detail)
            @php
                $question = $result->quiz->questions->firstWhere('id', $detail['question_id']);
            @endphp

            <div class="bg-white rounded-xl shadow p-6 mb-6">
                <h3 class="font-bold mb-4">
                    Q{{ $index+1 }}. {{ $question->question_text }}
                </h3>
                @if ($question->question_image)
                    <div class="mb-6 rounded-lg overflow-hidden">
                        <img src="{{ asset('storage/' . $question->question_image) }}" alt="Question image" class="w-full h-auto object-cover">
                    </div>
                @else 
                    <div class="mb-6 rounded-lg overflow-hidden">
                        <img src="{{ 'https://picsum.photos/400/200' }}" alt="Question image" class="w-full h-auto object-cover">
                    </div>
                @endif

                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($question->answers as $ans)
                        <li class="p-3 rounded-lg border-2
                            @if($ans->id == $detail['correct_answer'] ) border-green-500 bg-green-100
                            @elseif($ans->id == $detail['given_answer'] && !$detail['is_correct']) border-red-500 bg-red-100
                            @else border-gray-200
                            @endif
                        ">
                            {{ $ans->answer_text }}
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

