@extends('layouts.app')

@section('app_content')
    <div class="min-h-screen flex bg-gradient-to-br from-green-200 to-black/10 relative">

        <div class="xl:w-1/2 w-screen min-h-screen px-6 pt-10 xl:pt-50 xl:px-50 flex flex-col justify-start items-center">
            <div class="w-full max-w-sm mx-auto flex flex-col justify-start items-start">
                <pre class="w-[300px] p-2 bg-black rounded text-sm text-white overflow-x-auto">
                {{ json_encode(['session_id' => session()->getId(), 'data' => session()->all()], JSON_PRETTY_PRINT) }}</pre>

                <img src="{{ asset('assets/images/techverse_black_logo.png') }}" alt="{{ config('app.name') }}"
                    class="w-32 mb-6 xl:hidden">
                <p class="text-xl font-semibold mb-6">{{ config('app.name') }}</p>
                <p class="font-semibold mb-2 text-gray-700">Log In Your Account</p>

                <form method="POST" action="{{ route('login') }}" class="space-y-3 w-full">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="input w-full " placeholder="Enter your email">
                    </div>

                    <div class="mb-3 position-relative">
                        <label for="password" class="mb-2  block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required class="input w-full "
                                placeholder="Enter your password" />
                            <span class="absolute top-1/2 right-3 -translate-y-1/2" style="cursor: pointer"
                                onclick="togglePassword('password', this)">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                    </div>

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

                    {{-- Submit Button --}}
                    <button type="submit" class="w-full mt-3 btn btn-primary">
                        Log In
                    </button>

                    <a href="{{ route('shop.get') }}"
                        class="xl:!hidden btn btn-light w-full py-2 rounded-lg text-black font-semibold shadow-sm">
                        Skip To Shopping
                    </a>
                </form>

                <div class="text-center mt-4 w-full">

                </div>
                <div class="text-center mt-4 w-full">
                    <p class="text-sm text-gray-600">
                        Do not have account yet ?
                        <a href="{{ route('register.get') }}" class=" hover:underline mx-auto w-fit">Create an account</a>
                    </p>
                    <p class="text-sm text-gray-600">
                        <a href="{{ route('forgot-password.get') }}" class="hover:underline w-fit mx-auto">Forgot
                            Password ?</a>
                    </p>
                </div>


            </div>
        </div>
        <div class="xl:w-1/2 xl:flex hidden min-h-screen flex-col items-center justify-start pt-30 px-20 bg-white">
            <p class="text-2xl font-semibold mb-3">Explore your technical gadgets and hand-on tools with best deal !</p>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem vero eveniet rerum iste tempora ipsa rem vel
                corrupti praesentium totam asperiores numquam, voluptas ex vitae eum beatae doloremque nulla autem!</p>


            <div id="carouselExampleControlsNoTouching" class="w-full mt-3 carousel slide" data-bs-touch="false"
                data-bs-ride="carousel">
                <div class="carousel-inner">
                    {{-- <div class="carousel-item active">
                        <img src="{{ asset('assets/images/computer_accessories.png') }}" alt="Computer Store"
                            class="size-72">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('assets/images/computer_shelf.png') }}" alt="Computer Store" class="size-72">
                    </div> --}}
                    <div class="carousel-item">
                        <img src="{{ asset('assets/images/computer_promotion.png') }}" alt="Computer Store"
                            class="size-72">
                    </div>

                </div>
            </div>


        </div>

        <a href="/" type="button" class="hidden xl:block btn-close absolute top-10 left-10">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </a>
    </div>
@endsection

@push('script')
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
