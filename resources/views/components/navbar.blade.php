<div x-data="{
        open: {{ $errors->auth_form->any() ? 'true' : 'false' }} ,
        mode: '{{ old('form_type', 'login') }}',
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

            <button @click="open = true"
                class="flex items-center gap-2 px-4 py-2 rounded-full border border-gray-600 hover:border-gray-400 hover:bg-[#252627] transition-all duration-300 group cursor-pointer">
                <i class="fa-regular fa-circle-user text-lg  transition-colors"></i>
                <span class="text-sm font-semibold">Giriş Yap</span>
            </button>
        </div>
    @endif

    <div x-show="open" x-cloak x-transition:enter.duration.500ms x-transition:leave.duration.400ms
        class="fixed inset-0 z-[999] backdrop-blur-sm bg-black/50 flex items-center justify-center">

        <div @click.away="open = false"
            class="bg-[#1A1B1C] p-8 rounded-2xl shadow-2xl max-w-md w-full relative border border-gray-700">

            <div class="text-center mb-8">
                <p class="text-3xl tracking-widest font-extrabold text-white">Quizzo</p>
                <p class="text-xs text-gray-500 mt-1">Platforma Hoşgeldiniz</p>
            </div>

            <template x-if="mode === 'login'">
                <div>
                    <h2 class="text-xl font-bold mb-6 text-gray-200 flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket text-[#41825e]"></i>Sign In
                    </h2>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="form_type" value="login">

                        <div class="mb-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-envelope text-gray-500"></i>
                            </div>
                            <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}"
                                class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500" />
                            <template x-if="errors.email">
                                <p class="text-xs mt-1 text-red-500 font-semibold" x-text="errors.email[0]"></p>
                            </template>
                        </div>

                        <div class="mb-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-500"></i>
                            </div>
                            <input type="password" name="password" placeholder="Password"
                                class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500" />
                            <template x-if="errors.password">
                                <p class="text-xs mt-1 text-red-500 font-semibold" x-text="errors.password[0]"></p>
                            </template>
                        </div>

                        <button type="submit"
                            class="w-full mt-4 bg-[#41825e] transition-all duration-300 transform font-semibold hover:scale-[1.02] hover:bg-[#357652] hover:shadow-lg hover:shadow-green-900/20 text-white py-3 rounded-lg cursor-pointer flex justify-center items-center gap-2">
                            Giriş Yap <i class="fa-solid fa-arrow-right"></i>
                        </button>
                        <div class="relative flex py-5 items-center">
                            <div class="flex-grow border-t border-gray-700"></div>
                            <span class="flex-shrink-0 mx-4 text-gray-500 text-xs font-semibold">VEYA</span>
                            <div class="flex-grow border-t border-gray-700"></div>
                        </div>

                        <a href="{{ route('google.login') }}"
                            class="w-full bg-[#252627] hover:bg-white hover:text-black border border-gray-700 text-gray-300 py-3 rounded-lg transition-all duration-300 transform font-semibold hover:-translate-y-1 hover:shadow-lg flex items-center justify-center gap-3 group">

                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                <path fill="#34A853"
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                <path fill="#FBBC05"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                <path fill="#EA4335"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                            </svg>
                            Google ile Devam Et
                        </a>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-400">Not a Member Yet?</p>
                        <button @click="mode = 'register'; errors ={};"
                            class="text-md font-bold text-blue-400 hover:text-blue-300 transition-colors cursor-pointer mt-1">
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
                                class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500" />
                            <template x-if="errors.name">
                                <p class="text-xs mt-1 text-red-500 font-semibold" x-text="errors.name[0]"></p>
                            </template>
                        </div>

                        <div class="mb-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-envelope text-gray-500"></i>
                            </div>
                            <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}"
                                class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500" />
                            <template x-if="errors.email">
                                <p class="text-xs mt-1 text-red-500 font-semibold" x-text="errors.email[0]"></p>
                            </template>
                        </div>

                        <div class="mb-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-500"></i>
                            </div>
                            <input type="password" name="password" placeholder="Password"
                                class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500" />
                        </div>

                        <div class="mb-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-check-double text-gray-500"></i>
                            </div>
                            <input type="password" name="password_confirmation" placeholder="Confirm Password"
                                class="w-full bg-[#1A1B1C] border border-gray-600 text-white pl-10 p-2.5 rounded-lg focus:outline-none focus:border-[#41825e] focus:ring-1 focus:ring-[#41825e] transition-colors placeholder-gray-500" />
                            <template x-if="errors.password">
                                <p class="text-xs mt-1 text-red-500 font-semibold" x-text="errors.password[0]"></p>
                            </template>
                        </div>

                        <button type="submit"
                            class="w-full mt-4 bg-[#41825e] transition-all duration-300 transform font-semibold hover:scale-[1.02] hover:bg-[#357652] hover:shadow-lg hover:shadow-green-900/20 text-white py-3 rounded-lg cursor-pointer flex justify-center items-center gap-2">
                            Register Now <i class="fa-solid fa-user-check"></i>
                        </button>
                        <div class="relative flex py-5 items-center">
                            <div class="flex-grow border-t border-gray-700"></div>
                            <span class="flex-shrink-0 mx-4 text-gray-500 text-xs font-semibold">VEYA</span>
                            <div class="flex-grow border-t border-gray-700"></div>
                        </div>

                        <a href="{{ route('google.login') }}"
                            class="w-full bg-[#252627] hover:bg-white hover:text-black border border-gray-700 text-gray-300 py-3 rounded-lg transition-all duration-300 transform font-semibold hover:-translate-y-1 hover:shadow-lg flex items-center justify-center gap-3 group">

                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                <path fill="#34A853"
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                <path fill="#FBBC05"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                <path fill="#EA4335"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                            </svg>
                            Google ile Devam Et
                        </a>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-400">Already have an account?</p>
                        <button @click="mode = 'login'; errors ={};"
                            class="text-md font-bold text-blue-400 hover:text-blue-300 transition-colors cursor-pointer mt-1">
                            Login Here
                        </button>
                    </div>
                </div>
            </template>

            <button @click="open = false; errors = {};"
                class="absolute top-4 right-4 text-gray-500 hover:text-white transition-colors cursor-pointer bg-transparent p-1 rounded-full hover:bg-white/10">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
    </div>
</div>