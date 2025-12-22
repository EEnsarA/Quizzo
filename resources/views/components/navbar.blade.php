<div
    x-data="{
        open: {{ $errors->auth_form->any() ? 'true' : 'false' }} ,
        mode: '{{ old('form_type' , 'login') }}',
        errors: {{json_encode($errors->auth_form->getMessages() ?? [])}},
    }" 
    
    class="w-full h-20 bg-[#1A1B1C] text-white flex items-center p-4 justify-between relative z-50 shadow-sm border-gray-800">
    
    <div>
        <a href="{{ route('home') }}">
            <p class="text-2xl font-major-mono font-extrabold tracking-widest cursor-pointer">Quizzo</p>
        </a>
    </div>  

    @if(Auth::check())
        <div>
            <form method="POST" action="{{ route('logout') }}">
            @csrf
                <button type="submit" class="cursor-pointer hover:scale-105">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    @else
        <div> 
       
            <button @click="open = true" class="flex items-center gap-2 px-4 py-2 rounded-full border border-gray-600 hover:border-gray-400 hover:bg-[#252627] transition-all duration-300 group cursor-pointer">
                <i class="fa-regular fa-circle-user text-lg  transition-colors"></i> 
                <span class="text-sm font-semibold">Giriş Yap</span>
            </button>
        </div>
    @endif

    <div x-show="open" x-cloak 
     x-transition:enter.duration.500ms 
     x-transition:leave.duration.400ms
     class="fixed inset-0 z-[999] backdrop-blur-sm bg-black/50 flex items-center justify-center">
     
     <div @click.away="open = false" class="bg-[#1A1B1C] p-8 rounded-2xl shadow-2xl max-w-md w-full relative border border-gray-700">
        
        <div class="text-center mb-8">
            <p class="text-3xl tracking-widest font-extrabold text-white">Quizzo</p>
            <p class="text-xs text-gray-500 mt-1">Platforma Hoşgeldiniz</p>
        </div>
        
        <template x-if="mode === 'login'">
            <div>          
                <h2 class="text-xl font-bold mb-6 text-gray-200 flex items-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-[#41825e]"></i>
                </h2>
                
                <form method="POST" action="{{ route('login') }}"> 
                    @csrf
                    <input type="hidden" name="form_type" value="login">
                    
                    <div class="mb-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-gray-500"></i>
                        </div>
                        <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}"
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500"/>
                        <template x-if="errors.email">
                            <p class="text-xs mt-1 text-red-500 font-semibold" x-text="errors.email[0]"></p> 
                        </template> 
                    </div>
                                        
                    <div class="mb-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-500"></i>
                        </div>
                        <input type="password" name="password" placeholder="Password" 
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500"/>
                        <template x-if="errors.password">
                            <p class="text-xs mt-1 text-red-500 font-semibold" x-text="errors.password[0]"></p> 
                        </template> 
                    </div>

                    <button type="submit"
                        class="w-full mt-4 bg-[#41825e] transition-all duration-300 transform font-semibold hover:scale-[1.02] hover:bg-[#357652] hover:shadow-lg hover:shadow-green-900/20 text-white py-3 rounded-lg cursor-pointer flex justify-center items-center gap-2">
                        Giriş Yap <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-400">Not a Member Yet?</p>
                    <button @click="mode = 'register'; errors ={};" class="text-md font-bold text-blue-400 hover:text-blue-300 transition-colors cursor-pointer mt-1">
                        Create an Account
                    </button>
                </div>
            </div>
        </template>
        
        <template x-if="mode == 'register'">
            <div>
                <h2 class="text-xl font-bold mb-6 text-gray-200 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-[#41825e]"></i> Sign Up
                </h2>
                
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="form_type" value="register">
                    
                    <div class="mb-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user text-gray-500"></i>
                        </div>
                        <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" 
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500"/>
                        <template x-if="errors.name">
                            <p class="text-xs mt-1 text-red-500 font-semibold" x-text="errors.name[0]"></p> 
                        </template>
                    </div>

                    <div class="mb-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-gray-500"></i>
                        </div>
                        <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" 
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500"/>
                        <template x-if="errors.email">
                            <p class="text-xs mt-1 text-red-500 font-semibold" x-text="errors.email[0]"></p> 
                        </template>
                    </div>

                    <div class="mb-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-500"></i>
                        </div>
                        <input type="password" name="password" placeholder="Password" 
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500"/>
                    </div>
                    
                    <div class="mb-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-check-double text-gray-500"></i>
                        </div>
                        <input type="password" name="password_confirmation" placeholder="Confirm Password" 
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500"/>
                        <template x-if="errors.password">
                            <p class="text-xs mt-1 text-red-500 font-semibold" x-text="errors.password[0]"></p> 
                        </template>
                    </div>

                    <button type="submit" 
                        class="w-full mt-4 bg-[#41825e] transition-all duration-300 transform font-semibold hover:scale-[1.02] hover:bg-[#357652] hover:shadow-lg hover:shadow-green-900/20 text-white py-3 rounded-lg cursor-pointer flex justify-center items-center gap-2">
                        Register Now <i class="fa-solid fa-user-check"></i>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-400">Already have an account?</p>
                    <button @click="mode = 'login'; errors ={};" class="text-md font-bold text-blue-400 hover:text-blue-300 transition-colors cursor-pointer mt-1">
                        Login Here
                    </button>
                </div>
            </div>
        </template>

        <button 
            @click="open = false; errors = {};" 
            class="absolute top-4 right-4 text-gray-500 hover:text-white transition-colors cursor-pointer bg-transparent p-1 rounded-full hover:bg-white/10"
        >
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>
</div>
</div>