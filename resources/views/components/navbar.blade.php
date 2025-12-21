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
       
         class="fixed inset-0 z-[999] backdrop-blur-sm bg-black/40 flex items-center justify-center">
         
         <div @click.away="open = false" class="min-h-96 bg-[#1A1B1C] p-8 rounded-lg shadow-xl max-w-md w-full relative border border-gray-700">
            <p class="text-3xl mt-2 tracking-widest font-extrabold mb-8 text-white">Quizzo</p>
            
            <template x-if="mode === 'login'">
                <div>          
                    <h2 class="text-xl font-semibold mb-4 text-gray-200">Login</h2>
                    <form method="POST" action="{{ route('login') }}"> 
                        @csrf
                        <input type="hidden" name="form_type" value="login">
                        
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white p-2 mb-4 rounded focus:outline-none focus:border-[#41825e]"/>
                            <template x-if="errors.email">
                                <p class="text-sm mb-4 text-red-500" x-text="errors.email[0]"></p> 
                            </template> 
                                            
                        <input type="password" name="password" placeholder="Password" 
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white p-2 mb-4 rounded focus:outline-none focus:border-[#41825e]"/>
                            <template x-if="errors.password">
                                <p class="text-sm mb-4 text-red-500" x-text="errors.password[0]"></p> 
                            </template> 

                        <button type="submit"
                            class="w-full mt-5 bg-[#41825e] transition-all duration-300 transform font-semibold hover:scale-105 hover:bg-[#357652] text-white p-2 rounded cursor-pointer">Login</button>
                    </form>
                    <div class="mt-5 text-center">
                        <button @click="mode = 'register'; errors ={};" class="text-md mt-9 text-blue-400 cursor-pointer hover:text-blue-300 transition-colors">
                            Not a Member Yet ? <strong>Sign Up</strong>
                        </button>
                    </div>
                </div>
            </template>
            
            <template x-if="mode == 'register'">
                <div>
                    <h2 class="text-xl font-semibold mb-4 text-gray-200">Sign Up</h2>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <input type="hidden" name="form_type" value="register">
                        
                        <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" 
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white p-2 mb-4 rounded focus:outline-none focus:border-[#41825e]"/>
                            <template x-if="errors.name">
                                <p class="text-sm mb-4 text-red-500" x-text="errors.name[0]"></p> 
                            </template>

                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" 
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white p-2 mb-4 rounded focus:outline-none focus:border-[#41825e]"/>
                            <template x-if="errors.email">
                                <p class="text-sm mb-4 text-red-500" x-text="errors.email[0]"></p> 
                            </template>

                        <input type="password" name="password" placeholder="Password" 
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white p-2 mb-4 rounded focus:outline-none focus:border-[#41825e]"/>
                        
                        <input type="password" name="password_confirmation" placeholder="Confirm Password" 
                            class="w-full bg-[#1A1B1C] border border-gray-600 text-white p-2 mb-4 rounded focus:outline-none focus:border-[#41825e]"/>
                            <template x-if="errors.password">
                                <p class="text-sm mb-4 text-red-500" x-text="errors.password[0]"></p> 
                            </template>

                        <button type="submit" 
                            class="w-full mt-5 bg-[#41825e] transition-all duration-300 transform font-semibold hover:scale-105 hover:bg-[#357652] text-white p-2 rounded cursor-pointer">
                            Sign Up
                        </button>
                    </form>
                    <div class="mt-5 text-center">
                        <button @click="mode = 'login'; errors ={};" class="text-md cursor-pointer text-blue-400 hover:text-blue-300 transition-colors">
                            Already have an account ? <strong>Login</strong>
                        </button>
                    </div>
                </div>
            </template>

            <button 
                @click="open = false; errors = {};" 
                class="absolute top-4 right-4 text-gray-500 hover:text-white transition-colors cursor-pointer"
            >
                <i class="fa-solid fa-circle-xmark text-xl"></i>
            </button>
        </div>
    </div>
</div>