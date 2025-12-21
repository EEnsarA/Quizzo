@extends("layouts.app")
@props(['myQuizzos','user'])



@section("content")



<div class="mt-2 p-4 w-full h-full" 
    x-data="profileAvatar({{Js::from([
         'initialUrl' => $user->avatar_url ? asset('storage/' . $user->avatar_url) : '',
         'token'      => csrf_token()
     ]) }})">

    <div class="w-full text-[#F2EDE4] mt-2 p-4 md:p-12">
 
        <div class="flex flex-col md:flex-row items-center gap-6 md:gap-10" >
            
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
               
                <h1 class="text-3xl md:text-4xl font-extrabold leading-none">
                    {{ $user->name ?? "Kullanıcı Adı" }}
                </h1>
                <p class="text-gray-400 text-lg mt-1">
                    {{ $user->email ?? "" }}
                </p>
                
             
                <div class="flex justify-center md:justify-start gap-6 md:gap-8 mt-4 pt-4 border-t border-gray-700">
                    <div class="text-center">
                        <span class="block text-3xl font-bold text-blue-400">{{ $myQuizzos->count() }}</span> 
                        <span class="text-sm text-gray-400">Oluşturulan Quiz</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-3xl font-bold text-green-400">45</span> {{-- $user->solved_quizzes_count ?? --}}
                        <span class="text-sm text-gray-400">Çözülen Quiz</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-3xl font-bold text-yellow-400">#24</span> {{-- $user->rank ?? --}}
                        <span class="text-sm text-gray-400">Genel Sıralama</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kendi kütüphane sayfanla aynı stil --}}
        <div class="text-left mt-12 mb-6 pt-8 border-t border-gray-700">
            <h1 class="text-2xl md:text-3xl font-semibold leading-none">Quizzolarım</h1> 
        </div>
        
        {{-- Kendi kütüphane sayfanla aynı grid yapısı --}}
        <div class="mt-18 grid 2xl:grid-cols-4 xl:grid-cols-3 lg:grid-cols-3 md:grid-cols-3 sm:grid-cols-2 gap-6">
            
            @foreach ($myQuizzos as $quiz)
            
                <div class="bg-gray-800 rounded-lg p-4 shadow-lg transition duration-150 hover:bg-gray-700 hover:shadow-xl">
                    <a href="{{-- route('quiz.show', $quiz) --}}" class="block">
                        <h4 class="text-xl font-semibold text-blue-400 truncate">{{ $quiz->title ?? 'Tarih: Osmanlı Yükselme' }}</h4>
                        <p class="text-gray-400 text-sm mt-1">{{ $quiz->number_of_questions }} soru - {{ $quiz->difficulty}}</p>
                        @if ($quiz->results_count > 0)
                            <p class="text-gray-500 text-xs mt-3">{{ $quiz->results_count }} kez çözüldü</p>
                        @else
                            <p class="text-gray-500 text-xs mt-3">Yeni</p>
                        @endif
                    </a>
                </div>
            
            @endforeach
            
        </div>
        
     
        <div class="mt-8 text-white">
     
        </div>

    </div>
</div>

@endsection