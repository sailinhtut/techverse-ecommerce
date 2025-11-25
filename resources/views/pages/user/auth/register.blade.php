@php
    $site_name = getParsedTemplate('site_name');
    $site_logo = getSiteLogoURL();
@endphp


@extends('layouts.app') @section('app_content')
    <div class="min-h-screen bg-gradient-to-br from-primary/30 to-black/10 relative flex">
        <div class="xl:w-1/2 hidden xl:flex xl:flex-col items-center justify-start pt-30 px-20 bg-white">

            <p class="text-2xl font-semibold mb-3">Create Your Own Account And Enjoy Best Technical Devices In Life !</p>

            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem vero eveniet rerum iste tempora ipsa rem vel
                corrupti praesentium totam asperiores numquam, voluptas ex vitae eum beatae doloremque nulla autem!</p>

            <div id="carouselExampleControlsNoTouching" class="w-full mt-3 carousel slide" data-bs-touch="false"
                data-bs-ride="carousel">
                <div class="carousel-inner">
                    {{-- <div class="carousel-item active">
                        <img src="{{ asset('assets/images/computer_accessories.png') }}" alt="Computer Store"
                            class="size-72">
                    </div> --}}
                    <div class="carousel-item">
                        <img src="{{ asset('assets/images/computer_shelf.png') }}" alt="Computer Store" class="size-72">
                    </div>
                    {{-- <div class="carousel-item">
                        <img src="{{ asset('assets/images/computer_promotion.png') }}" alt="Computer Store" class="size-72">
                    </div> --}}

                </div>
            </div>
        </div>

        <div class="xl:w-1/2 w-screen px-6 pt-10 xl:pt-28 xl:px-50 flex flex-col justify-start items-center">
            <div class="w-full max-w-sm flex flex-col justify-center items-start">
                <img src="{{ $site_logo }}" alt="{{ $site_name }}"
                    class="w-32 mb-6 xl:hidden">
                <p class="text-xl font-semibold mb-6">{{ $site_name }}</p>
                <p class="font-semibold mb-2 text-gray-700">Log In Your Account</p>
                <form method="POST" action="{{ route('register.post') }}" class="space-y-2 w-full" x-data="{ signingUp: false }"
                    @submit="signingUp=true">
                    @csrf
                    <label for="name" class="form-label block text-sm font-medium text-gray-700">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                        class="input w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                        placeholder="Enter your name" />

                    <label for="email" class="form-label block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="input w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200"
                        placeholder="Enter your email" />


                    <div class="mb-3" x-data="{ show: false }">
                        <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" id="password" name="password" required
                                class="input w-full" placeholder="Enter your password" />
                            <span class="absolute top-1/2 right-3 -translate-y-1/2 cursor-pointer z-20" @click="show = !show">
                                <template x-if="!show">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </template>
                                <template x-if="show">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </template>
                            </span>
                        </div>
                    </div>


                    <div class="mb-3" x-data="{ show: false }">
                        <label for="password_confirmation" class="text-sm font-medium text-gray-700">Confirm
                            Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" id="password" name="password_confirmation" required
                                class="input w-full" placeholder="Confirm your password" />
                            <span class="absolute top-1/2 right-3 -translate-y-1/2 cursor-pointer z-20" @click="show = !show">
                                <template x-if="!show">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </template>
                                <template x-if="show">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </template>
                            </span>
                        </div>
                    </div>

                    <div class="w-full mb-5 text-end">
                        <input class="checkbox checkbox-xs" type="checkbox" id="remember" name="remember">
                        <label class="text-xs text-gray-700" for="remember">Remember Me
                        </label>
                    </div>


                    <label class="text-sm text-gray-700" for="terms">
                        <input class="checkbox checkbox-sm" type="checkbox" id="terms" name="terms" required>
                        I have read and agree to the
                        <a href="{{ route('terms.get') }}" target="_blank" class="underline hover:underline">
                            Terms & Conditions
                        </a>
                        and <a href="{{ route('privacy.get') }}" target="_blank" class="underline hover:underline">
                            Privacy Policy
                        </a>.
                    </label>

                    @if (session('error'))
                        <div class="mt-3 alert alert-error m-0 p-2 text-xs">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-3 alert alert-error m-0 p-2">
                            <ul class="m-0 p-0">
                                @foreach ($errors->all() as $error)
                                    <li class="text-xs">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button type="submit"
                        class="mt-3 btn btn-primary w-full py-2 rounded-lg text-white font-semibold shadow-sm"
                        :disabled="signingUp">
                        <span x-show="signingUp" class="loading loading-spinner loading-sm mr-2"></span>
                        <span x-show="signingUp">Signing Up</span>
                        <span x-show="!signingUp">Sign Up</span>
                    </button>


                    <a href="{{ route('shop.get') }}"
                        class="xl:!hidden mt-2 btn btn-light w-full py-2 rounded-lg text-black font-semibold shadow-sm">
                        Skip To Shopping
                    </a>

                </form>

                <div class="text-center mt-4 w-full">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="hover:underline w-fit mx-auto">Log In</a>
                    </p>
                    <p class="text-sm text-gray-600">
                        <a href="{{ route('forgot-password.get') }}" class="hover:underline w-fit mx-auto">Forgot
                            Password ?</a>
                    </p>
                </div>
            </div>
        </div>


        <a href="/" type="button" class="hidden xl:block btn-close absolute top-10 right-10">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </a>
    </div>

    @endsection @push('script')
    <script>
        function togglePassword(inputId, el) {
            const input = document.getElementById(inputId);
            const icon = el.querySelector("i");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>
@endpush
