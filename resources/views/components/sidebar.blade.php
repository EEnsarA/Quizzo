
<div
    class="bg-[#212121] text-white h-screen fixed left-0 top-0 flex flex-col transation-all duration-300"
    :class="$store.sidebar.open ?  'w-64' : 'w-16'"
   
    >

    <div class="p-4 mt-3">
        <button @click="$store.sidebar.open = !$store.sidebar.open" class="cursor-pointer hover:scale-110">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
    <nav class="mt-4">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('home') }}" class="block py-2 px-4 hover:bg-gray-700 rounded transition-all">
                    <i class="fa fa-home mr-2"></i> <span x-show="$store.sidebar.open" x-transition>Home</span>
                </a>
            </li>
            @if(Auth::check())
            <li>
                <a href="" class="block py-2 px-4 hover:bg-gray-700 rounded transition-all">
                    <i class="fa fa-user mr-2"></i> <span x-show="$store.sidebar.open" x-transition>{{ Auth::user()->name ?? 'My Profile' }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('library.show') }}" class="block py-2 px-4 hover:bg-gray-700 rounded transition-all">
                    <i class="fa-solid fa-book mr-2"></i> <span x-show="$store.sidebar.open" x-transition>Library</span>
                </a>
            </li>
            
            @endif
            <li>
                <a href="{{ route('quiz.create') }}" class="block py-2 px-4 hover:bg-gray-700 rounded transition-all">
                    <i class="fa-solid fa-plus mr-2"></i> <span x-show="$store.sidebar.open" x-transition>Create</span>
                </a>
            </li>
            <li>
                <a href="" class="block py-2 px-4 hover:bg-gray-700 rounded transition-all">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i> <span x-show="$store.sidebar.open" x-transition>Search</span>
                </a>
            </li>
        </ul>
    </nav>
    
</div>