@props([
    'icon' => 'fa-regular fa-folder-open', 
    'color' => 'text-gray-400',            
    'title' => 'Burada henüz bir şey yok', 
    'desc' => '',                          
    'btnText' => null,                    
    'btnLink' => '#',                     
    'btnIcon' => 'fa-solid fa-plus',       
    'btnClass' => 'bg-indigo-600 hover:bg-indigo-500 text-white' 
])

<div class="flex flex-col items-center justify-center bg-gray-800/30 rounded-2xl p-12 border-2 border-dashed border-gray-700 text-center group hover:border-gray-600 transition h-full">
    
  
    <div class="bg-gray-700 p-4 rounded-full mb-4 group-hover:scale-110 transition duration-300 shadow-lg">
        <i class="{{ $icon }} text-4xl {{ $color }}"></i>
    </div>


    <h3 class="text-xl font-bold text-gray-300">
        {{ $title }}
    </h3>

  
    @if(!empty($desc))
        <p class="text-gray-500 mt-3 max-w-md mx-auto leading-relaxed text-sm">
            {{ $desc }}
        </p>
    @endif

   
    @if($btnText)
        <a href="{{ $btnLink }}" 
           class="mt-8 px-8 py-3 rounded-xl font-bold transition flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1 {{ $btnClass }}">
            <i class="{{ $btnIcon }}"></i> 
            <span>{{ $btnText }}</span>
        </a>
    @endif
</div>