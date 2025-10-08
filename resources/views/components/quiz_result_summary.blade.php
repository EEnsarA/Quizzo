@props(['result'])

<div class="bg-[#BFBDB0] text-[#1A1B1C] rounded-xl shadow p-6 mb-8 flex items-center justify-between">
    <div class="flex items-center space-x-4">
        <img src="{{ $result->user?->avatar_url ?? 'https://i.pravatar.cc/100' }}" 
             class="w-12 h-12 rounded-full">
        <div>
            <h2 class="font-bold text-lg">{{ $result->user?->name ??  'Guest' . substr($result->session_id, 0, 4) }}</h2>
            <p class="text-gray-500 text-sm">Attempt #{{ $result->attempt_number }}</p>
        </div>
    </div>
    <div class="flex space-x-6 text-center">
        <div>
            <p class="text-xl font-bold text-green-600">{{ $result->correct_count }}</p>
            <p class="text-gray-500 text-sm">Correct</p>
        </div>
        <div>
            <p class="text-xl font-bold text-red-600">{{ $result->wrong_count }}</p>
            <p class="text-gray-500 text-sm">Wrong</p>
        </div>
        <div>
            <p class="text-xl font-bold text-yellow-600">{{ $result->empty_count }}</p>
            <p class="text-gray-500 text-sm">Empty</p>
        </div>
        <div>
            <p class="text-xl font-bold text-blue-600">{{ $result->net }}</p>
            <p class="text-gray-500 text-sm">Net</p>
        </div>
        <div>
            <p class="text-xl font-bold">{{floor($result->time_spent/60)}}min {{$result->time_spent % 60}}sec</p>
            <p class="text-gray-500 text-sm">Time</p>
        </div>
    </div>
</div>