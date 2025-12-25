<div
    class="bg-[#212121] text-white h-screen fixed left-0 top-0 flex flex-col transition-all duration-300 z-[60]"
    :class="$store.sidebar.open ? 'w-64' : 'w-16'"
>

    <div class="p-4 mt-3 flex justify-center">
        <button 
            @click="$store.sidebar.open = !$store.sidebar.open" 
            class="cursor-pointer p-2 rounded-lg hover:bg-gray-700 transition-all duration-300 transform"
            :class="$store.sidebar.open ? 'rotate-180' : 'rotate-0'"
        >
            <i class="fa-solid fa-lg" 
               :class="$store.sidebar.open ? 'fa-chevron-right' : 'fa-bars'"></i>
        </button>
    </div>

    <nav class="mt-4 flex-1 overflow-y-auto">
        <ul class="space-y-2 px-2">
            
            <li>
                <a href="{{ route('home') }}" 
                   class="flex items-center py-3 px-3 rounded transition-all whitespace-nowrap group
                   {{ request()->routeIs('home') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                   <div class="w-6 text-center"><i class="fa fa-home"></i></div>
                    <span x-show="$store.sidebar.open" x-transition.opacity.duration.300 class="ml-3 font-medium">Home</span>
                </a>
            </li>

            @if(Auth::check())
            <li>
                <a href="{{ route('profile') }}" 
                   class="flex items-center py-3 px-3 rounded transition-all whitespace-nowrap group
                   {{ request()->routeIs('profile*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                    <div class="w-6 text-center"><i class="fa fa-user"></i></div>
                    <span x-show="$store.sidebar.open" x-transition.opacity.duration.300 class="ml-3 font-medium">
                        {{ Str::limit(Auth::user()->name, 15) ?? 'My Profile' }}
                    </span>
                </a>
            </li>

            <li>
                <a href="{{ route('library.show') }}" 
                   class="flex items-center py-3 px-3 rounded transition-all whitespace-nowrap group
                   {{ request()->routeIs('library*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                    <div class="w-6 text-center"><i class="fa-solid fa-book"></i></div>
                    <span x-show="$store.sidebar.open" x-transition.opacity.duration.300 class="ml-3 font-medium">Library</span>
                </a>
            </li>
            @endif

            <li>
                <a href="{{ route('quiz.create') }}" 
                   class="flex items-center py-3 px-3 rounded transition-all whitespace-nowrap group
                   {{ request()->routeIs('quiz.create') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                    <div class="w-6 text-center"><i class="fa-solid fa-plus"></i></div>
                    <span x-show="$store.sidebar.open" x-transition.opacity.duration.300 class="ml-3 font-medium">Create Quiz</span>
                </a>
            </li>

            <li>
                <a href="{{ route('exam.create') }}" 
                   class="flex items-center py-3 px-3 rounded transition-all whitespace-nowrap group
                   {{ request()->routeIs('exam.create') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                    <div class="w-6 text-center"><i class="fa-solid fa-file-pdf"></i></div>
                    <span x-show="$store.sidebar.open" x-transition.opacity.duration.300 class="ml-3 font-medium">Create Exam</span>
                </a>
            </li>

            <li>
                <a href="#" 
                   class="flex items-center py-3 px-3 rounded transition-all whitespace-nowrap hover:bg-gray-700 group">
                    <div class="w-6 text-center"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <span x-show="$store.sidebar.open" x-transition.opacity.duration.300 class="ml-3 font-medium">Search</span>
                </a>
            </li>

            <li>
                <a href="{{ route('test') }}" 
                   class="flex items-center py-3 px-3 rounded transition-all whitespace-nowrap hover:bg-gray-700 group">
                    <div class="w-6 text-center"><i class="fa-solid fa-hammer"></i></div>
                    <span x-show="$store.sidebar.open" x-transition.opacity.duration.300 class="ml-3 font-medium">Test</span>
                </a>
            </li>

        </ul>
    </nav>
    
</div>