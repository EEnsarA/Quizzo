
@props(['paper'])

<div class="bg-[#252526] border border-gray-700 rounded-xl overflow-hidden shadow-lg hover:border-blue-500 transition group h-full flex flex-col">
    <div class="p-5 flex flex-col h-full">
        <div class="flex justify-between items-start mb-4">
            <div class="bg-blue-900/30 text-blue-400 p-3 rounded-lg">
                <i class="fa-solid fa-file-contract text-2xl"></i>
            </div>
            <span class="text-xs text-gray-500 font-mono">{{ $paper->created_at->diffForHumans() }}</span>
        </div>
        
        <h3 class="text-white font-bold text-lg mb-1 truncate" title="{{ $paper->title }}">{{ $paper->title }}</h3>
        <p class="text-gray-400 text-sm mb-4">{{ $paper->page_count }} Sayfa</p>
        
        <div class="flex gap-2 mt-auto">
          
            <a href="{{ route('exam.edit', $paper->id)  }}" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold py-2 rounded text-center transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Düzenle
            </a>
   
            <form action="{{ route('exam.delete', $paper->id) }}" method="POST" onsubmit="return confirm('Bu sınav kağıdını silmek istediğine emin misin?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white px-3 py-2 rounded transition h-full w-10 flex items-center justify-center">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>