
<div
    x-data="{
        open: {{ $errors->auth_form->any() ? 'true' : 'false' }} ,
        mode: '{{ old('form_type' , 'login') }}',
        errors: {{json_encode($errors->auth_form->getMessages() ?? [])}},
    }" 
    class="w-full h-20 bg-[#1A1B1C] text-white flex items-center p-4 justify-between">
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
            <button @click="open = true" class="cursor-pointer hover:scale-105">
                <i class="fa-regular fa-circle-user fa-lg"></i> 
            </button>
        </div>
    @endif
    <!-- Modal -->
    <div x-show="open" x-cloak   x-transition:enter.duration.500ms x-transition:leave.duration.400ms
     class="fixed inset-0  backdrop-blur-xs flex items-center justify-center">
         <div class=" min-h-96  bg-[#1A1B1C] p-8 rounded-lg shadow-xl max-w-md w-full relative ">
            <p class="text-3xl mt-2 tracking-widest font-extrabold mb-8">Quizzo</p>
            <!-- Login Form -->
            <template x-if="mode === 'login'">
                <div>          
                    <h2 class="text-xl font-semibold mb-4">Login</h2>
                    <form method="POST" action="{{ route('login') }}"> 
                        @csrf
                        <input type="hidden" name="form_type" value="login">
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                             class="w-full border p-2 mb-4  rounded  "/>
                            <template x-if="errors.email">
                                <p  class="text-sm mb-4 text-red-500"  x-text="errors.email[0]"></p> 
                            </template>                             
                        <input type="password" name="password" placeholder="Password" 
                             class="w-full border p-2 mb-4 rounded"/>
                            <template x-if="errors.password">
                                <p  class="text-sm mb-4 text-red-500"  x-text="errors.password[0]"></p> 
                            </template> 

                        <button type="submit"
                             class="w-full mt-5 bg-[#41825e]  transition-all duration-300  transform font-semibold hover:scale-105 hover:bg-[#357652] text-white p-2 rounded cursor-pointer">Login</button>
                    </form>
                    <div class="mt-5 text-center">
                        <button @click="mode = 'register'; errors ={};" class="text-md mt-9 text-blue-400  cursor-pointer">
                            Not a Member Yet ? <strong>Sign Up</strong>
                        </button>
                    </div>
                </div>
            </template>
            <!-- Register Form -->
            <template x-if="mode == 'register'">
                <div>
                   <h2 class="text-xl font-semibold mb-4">Sign Up</h2>
                        <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <input type="hidden" name="form_type" value="register">
                        <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" 
                            class="w-full p-2 mb-4 border rounded focus:outline-none focus:border-current focus:ring-0"/>
                            <template x-if="errors.name">
                                <p  class="text-sm mb-4 text-red-500"  x-text="errors.name[0]"></p> 
                            </template>
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" 
                            class="w-full p-2 mb-4 border rounded focus:outline-none focus:border-current focus:ring-0"/>
                            <template x-if="errors.email">
                                <p  class="text-sm mb-4 text-red-500"  x-text="errors.email[0]"></p> 
                            </template>
                        <input type="password" name="password" placeholder="Password" 
                            class="w-full border p-2 mb-4 rounded"/>
                        <input type="password" name="password_confirmation" placeholder="Confirm Password" 
                            class="w-full border p-2 mb-4 rounded"/>
                            <template x-if="errors.password">
                                <p  class="text-sm mb-4 text-red-500"  x-text="errors.password[0]"></p> 
                            </template>

                        <button type="submit" 
                            class="w-full mt-5 bg-[#41825e] transition-all duration-300 transform font-semibold hover:scale-105 hover:bg-[#357652] text-white p-2 rounded cursor-pointer">
                            Sign Up
                        </button>
                    </form>
                    <div class="mt-5 text-center">
                        <button @click="mode = 'login'; errors ={};" class="text-md cursor-pointer text-blue-400">
                            Already have an account ? <strong>Login</strong>
                        </button>
                    </div>
                </div>
            </template>

            <!-- Kapat Butonu -->
            <button 
                @click="open = false; errors = {};" 
                class="absolute top-2 right-2  hover:text-gray-300  cursor-pointer"
            >
                <i class="fa-solid fa-circle-xmark"></i>
            </button>
        </div>
    </div>
</div>