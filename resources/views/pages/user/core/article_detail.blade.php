@php
    $site_name = getParsedTemplate('site_name');
    $site_logo = getSiteLogoURL();
@endphp

@extends('layouts.app')

@section('head')
    <!-- Page Title & Meta -->
    <title>{{ $article['title'] }} | $site_name</title>
    <meta name="description" content="{{ $article['description'] }}">
    <meta name="keywords" content="{{ implode(',', $article['tags'] ?? []) }}">
    <meta name="author" content="{{ $site_name }}">
    <meta name="robots" content="index, follow">

    <!-- Open Graph (Facebook / LinkedIn) -->
    <meta property="og:title" content="{{ $article['title'] }}" />
    <meta property="og:description" content="{{ $article['description'] }}" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ route('articles.slug.get', ['slug' => $article['slug']]) }}" />
    <meta property="og:image" content="{{ $article['image'] ?? $site_logo }}" />
    <meta property="og:site_name" content="{{ $site_name }}" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $article['title'] }}">
    <meta name="twitter:description" content="{{ $article['description'] }}">
    <meta name="twitter:image" content="{{ $article['image'] ?? $site_logo }}">
@endsection


@section('app_content')
    @include('components.landing_navbar')
    <div class="min-h-screen mt-[60px]">
        <div class="max-w-5xl mx-auto">
            <div class="p-3 lg:px-0">
                <button onclick="history.back()" class="btn btn-sm bg-base-100 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </button>
            </div>

            <div class="w-full mb-[100px] p-4 lg:p-0">
                <?php if ($article['image']): ?>
                <div class="relative w-full h-72 mb-4 overflow-hidden border border-base-300">

                    <!-- Blurred Glassmorphism Background -->

                    <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}"
                        class="absolute inset-0 w-full h-72 object-fill blur-sm ">
                    <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}"
                        class="relative z-10 w-full h-72 object-contain rounded-xl">
                </div>
                <?php endif; ?>


                <h1 class="text-2xl font-semibold mb-2">{{ $article['title'] }}</h1>


                <div class="mt-5 prose !text-justify w-full overflow-scroll text-sm">
                    {!! $article['content'] !!}
                </div>

                <p class='font-semibold mt-4'>Share via</p>
                <div class="flex gap-3 mt-2">
                    <a href="{{ $socialShareLinks['facebook'] }}" target="_blank">
                        <img src="{{ asset('assets/images/social_images/facebook_svg.svg') }}" alt="Facebook"
                            class="size-5">
                    </a>

                    <a href="{{ $socialShareLinks['twitter'] }}" target="_blank">
                        <img src="{{ asset('assets/images/social_images/x_svg.svg') }}" alt="Twitter" class="size-5">
                    </a>

                    <a href="{{ $socialShareLinks['linkedin'] }}" target="_blank">
                        <img src="{{ asset('assets/images/social_images/linkedin_svg.svg') }}" alt="LinkedIn"
                            class="size-5">
                    </a>

                    <a href="{{ $socialShareLinks['whatsapp'] }}" target="_blank">
                        <img src="{{ asset('assets/images/social_images/whatsapp_svg.svg') }}" alt="WhatsApp"
                            class="size-5">
                    </a>

                    <a href="{{ $socialShareLinks['telegram'] }}" target="_blank">
                        <img src="{{ asset('assets/images/social_images/telegram_svg.svg') }}" alt="Telegram"
                            class="size-5">
                    </a>
                </div>

                <p class="mt-5 text-sm ">Date:
                    {{ $article['created_at'] ? \Carbon\Carbon::parse($article['created_at'])->format('Y-m-d h:i A') : '-' }}
                </p>
                <p class="text-sm flex items-center gap-2">View: {{ $article['view_count'] ?? 0 }}

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-3 inline">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </p>
            </div>
        </div>


    </div>

    @include('components.web_footer')
@endsection
