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
        <meta name="author" content="Tech Verse">
        <meta name="robots" content="index, follow">

        <!-- Open Graph -->
        <meta property="og:title" content="{{ config('app.name') }}" />
        <meta property="og:description" content="Detailed info about this product." />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:image" content="{{ asset('assets/images/techverse_green_logo.png') }}" />
        <meta property="og:site_name" content="Tech Verse" />

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ config('app.name') }}">
        <meta name="twitter:description" content="Detailed info about this product.">
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

</body>

</html>
