@extends("layouts.app")

@props(['quiz'])


@section("content")
    <div class="text-white">

        <h2>QUESTIONS CREATE</h2>
        @php echo $quiz @endphp
    </div>
@endsection