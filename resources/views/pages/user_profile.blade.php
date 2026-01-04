@extends("layouts.app")
@props(['myQuizzos','user'])



@section("content")



<div class="w-full min-h-screen" 
     x-data="profileAvatar({{Js::from([
         'initialUrl' => $user->avatar_url ? asset('storage/' . $user->avatar_url) : '',
         'token'      => csrf_token()
     ]) }})">

    <div class="w-full text-[#F2EDE4] mt-2 p-4 md:p-12 pb-0"> 
 
        <div class="flex flex-col md:flex-row items-center gap-6 md:gap-10 border-b border-gray-700 pb-8" >
            
            <div class="relative w-32 h-32 md:w-40 md:h-40 flex-shrink-0 group">
                <input type="file" 
                       id="avatarUpload" 
                       name="avatar_img" 
                       class="hidden" 
                       accept="image/png, image/jpeg, image/jpg" 
                       @change="updateAvatar($event)">
      
    
                <img x-show="previewUrl"
                     :src="previewUrl" 
                     alt="Avatar" 
                     class="w-full h-full rounded-full object-cover border-4 border-gray-700 group-hover:opacity-70 transition-opacity"
                     :class="{'opacity-50': isUploading}"
                     style="display: none;"> 

           
                <div x-show="!previewUrl" 
                    class="w-full h-full rounded-full bg-gray-800 border-4 border-gray-700 
                           flex items-center justify-center 
                           overflow-hidden group-hover:opacity-70 transition-opacity"
                    :class="{'opacity-50': isUploading}">
                    <i class="fa-solid fa-circle-user text-gray-400 text-[130px] md:text-[165px]"></i>
                </div>
      
                <div x-show="isUploading" class="absolute inset-0 flex items-center justify-center z-10" style="display: none;">
                    <i class="fas fa-spinner fa-spin text-white text-3xl"></i>
                </div>

        
                <label for="avatarUpload" 
                       class="absolute inset-0 w-full h-full rounded-full bg-black bg-opacity-40
                              flex items-center justify-center text-white text-3xl
                              opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer z-20">
                    <i class="fas fa-camera"></i>
                </label>
            </div>

        
            <div class="flex-grow text-center md:text-left">
                <h1 class="text-3xl md:text-4xl font-extrabold leading-none text-gray-100">
                    {{ $user->name ?? "Kullanıcı Adı" }}
                </h1>
                <p class="text-gray-400 text-lg mt-1">
                    {{ $user->email ?? "" }}
                </p>
                
           
                <div class="flex justify-center md:justify-start gap-6 md:gap-8 mt-4 pt-4 border-t border-gray-700/50">
                    <div class="text-center">
                        <span class="block text-3xl font-bold text-blue-400">{{ $myQuizzos->count() }}</span> 
                        <span class="text-sm text-gray-400">Oluşturulan</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-3xl font-bold text-green-400">{{ $user->solved_quizzes_count ?? 0 }}</span>
                        <span class="text-sm text-gray-400">Çözülen</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-3xl font-bold text-yellow-400">#{{ $user->rank ?? '-' }}</span>
                        <span class="text-sm text-gray-400">Sıralama</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div x-data="{ activeTab: 'quizzes' }" class="px-4 md:px-12 mt-2 pb-12">

   
        <div class="flex items-center gap-8 border-b border-gray-700 mb-6">
            
     
            <button @click="activeTab = 'quizzes'" 
                    class="pb-3 text-lg md:text-xl font-semibold transition-all duration-300 relative"
                    :class="activeTab === 'quizzes' ? 'text-blue-400' : 'text-gray-400 hover:text-gray-200'">
                Quizzolarım
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-400 transition-transform duration-300"
                        :class="activeTab === 'quizzes' ? 'scale-x-100' : 'scale-x-0'"></div>
            </button>

      
            <button @click="activeTab = 'exams'" 
                    class="pb-3 text-lg md:text-xl font-semibold transition-all duration-300 relative"
                    :class="activeTab === 'exams' ? 'text-blue-400' : 'text-gray-400 hover:text-gray-200'">
                Sınav Kağıtlarım
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-400 transition-transform duration-300"
                        :class="activeTab === 'exams' ? 'scale-x-100' : 'scale-x-0'"></div>
            </button>
        </div>

     
        <div x-show="activeTab === 'quizzes'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
             
            @if($myQuizzos->count() > 0)
                <div class="grid 2xl:grid-cols-4 xl:grid-cols-3 lg:grid-cols-3 md:grid-cols-3 sm:grid-cols-2 gap-6">
                    @foreach ($myQuizzos as $quiz)
                        <div class="bg-gray-800 rounded-lg p-4 shadow-lg transition duration-150 hover:bg-gray-700 hover:shadow-xl group border border-transparent hover:border-gray-600">
                            <a href="{{-- route('quiz.show', $quiz) --}}" class="block">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="bg-blue-900/50 text-blue-300 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">Quiz</span>
                                    <i class="fa-solid fa-chevron-right text-gray-600 group-hover:text-white transition"></i>
                                </div>
                                <h4 class="text-xl font-semibold text-gray-200 group-hover:text-blue-400 transition truncate">{{ $quiz->title }}</h4>
                                <p class="text-gray-400 text-sm mt-1 flex items-center gap-2">
                                    <i class="fa-solid fa-layer-group text-xs"></i> {{ $quiz->number_of_questions }} Soru
                                    <span class="w-1 h-1 bg-gray-600 rounded-full"></span>
                                    <span class="{{ $quiz->difficulty->value == 'hard' ? 'text-red-400' : ($quiz->difficulty->value == 'medium' ? 'text-yellow-400' : 'text-green-400') }}">
                                        {{ ucfirst($quiz->difficulty->value) }}
                                    </span>
                                </p>
                                <div class="mt-4 pt-3 border-t border-gray-700 flex justify-between items-center text-xs text-gray-500">
                                    <span><i class="fa-regular fa-clock mr-1"></i> {{ $quiz->created_at->diffForHumans() }}</span>
                                    <span>{{ $quiz->results_count }} Çözülme</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-500">
                    <i class="fa-solid fa-box-open text-4xl mb-3 opacity-50"></i>
                    <p>Henüz hiç quiz oluşturmadın.</p>
                </div>
            @endif
        </div>

        <div x-show="activeTab === 'exams'" style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
             
            @if(isset($myExams) && $myExams->count() > 0)
                <div class="grid 2xl:grid-cols-4 xl:grid-cols-3 lg:grid-cols-3 md:grid-cols-3 sm:grid-cols-2 gap-6">
                    @foreach ($myExams as $paper)
                        <div class="bg-gray-800 rounded-lg p-4 shadow-lg transition duration-150 hover:bg-gray-700 hover:shadow-xl group border border-transparent hover:border-gray-600 relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 w-16 h-16 bg-white/5 rounded-full blur-xl group-hover:bg-indigo-500/20 transition"></div>
                            <a href="{{-- route('exam.edit', $paper->id) --}}" class="block relative z-10">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="bg-indigo-900/50 text-indigo-300 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">Kağıt</span>
                                    <button class="text-gray-500 hover:text-red-400 transition" title="PDF İndir">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </button>
                                </div>
                                <h4 class="text-xl font-semibold text-gray-200 group-hover:text-indigo-400 transition truncate">{{ $paper->title ?? 'İsimsiz Sınav' }}</h4>
                                <p class="text-gray-400 text-sm mt-1 flex items-center gap-2">
                                    <i class="fa-solid fa-file-lines text-xs"></i> A4 Formatı
                                </p>
                                <div class="mt-4 pt-3 border-t border-gray-700 flex justify-between items-center text-xs text-gray-500">
                                    <span><i class="fa-regular fa-calendar mr-1"></i> {{ $paper->created_at->format('d.m.Y') }}</span>
                                    <span class="text-indigo-400 hover:underline">Düzenle <i class="fa-solid fa-arrow-right ml-1"></i></span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-500">
                    <i class="fa-solid fa-folder-open text-4xl mb-3 opacity-50"></i>
                    <p>Henüz sınav kağıdı oluşturmadın.</p>
                </div>
            @endif
        </div>

    </div>

</div>

@endsection