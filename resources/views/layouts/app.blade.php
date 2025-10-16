<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <!-- Required Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @if (!View::hasSection('head'))
        <title>{{ config('app.name') }}</title>
        <meta name="description" content="">
        <meta name="keywords" content="product, tech, ecommerce">
        <meta name="author" content="{{ config('app.name') }}">
        <meta name="robots" content="index, follow">

        <!-- Open Graph -->
        <meta property="og:title" content="{{ config('app.name') }}" />
        <meta property="og:description" content="" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:image" content="{{ asset('assets/images/techverse_green_logo.png') }}" />
        <meta property="og:site_name" content="{{ config('app.name') }}" />

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ config('app.name') }}">
        <meta name="twitter:description" content="">
        <meta name="twitter:image" content="{{ asset('assets/images/techverse_green_logo.png') }}">
    @endif

    @yield('head')

    <!-- PWA / Mobile App -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <!-- <meta name="theme-color" content="#FFBF00"> -->

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    @yield('app_content')
    @stack('script')


    @if (session('success') || session('error') || true)
        <div class="toast fixed bottom-4 right-4 z-50 space-y-2">
            @if (session('success'))
                <div id="status-success"
                    class="alert flex justify-between items-center shadow-lg border rounded-lg
                       bg-green-100
                       dark:bg-black dark:text-green-100 dark:border-gray-700 transition-all">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            class="h-6 w-6 shrink-0 stroke-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn btn-xs btn-circle ml-2" onclick="this.parentElement.remove()">
                        <i data-lucide="x" class="size-4"></i>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div id="status-error"
                    class="alert flex justify-between items-center shadow-lg border rounded-lg
                       bg-green-100
                       dark:bg-black dark:text-green-100 dark:border-gray-700 transition-all">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-error" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn btn-xs btn-circle ml-2" onclick="this.parentElement.remove()">
                        <i data-lucide="x" class="size-4"></i>
                    </button>
                </div>
            @endif
        </div>
    @endif

    <dialog class="modal" id="logout_modal">
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <p class="font-semibold py-0">Log Out</p>
            <p class="py-2 mb-0">Are you sure you want to log out?</p>
            <div class="modal-action mt-0">
                <form method="dialog">
                    <button class="btn">Close</button>
                </form>
                <form method="POST" action="{{ route('logout.post') }}">
                    @csrf
                    <button type="submit" class="btn btn-error">Logout</button>
                </form>
            </div>
        </div>
    </dialog>



</body>

</html>
