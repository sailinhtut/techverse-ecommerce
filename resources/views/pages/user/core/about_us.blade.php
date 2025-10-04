@extends('layouts.app')

@section('app_content')
    @include('components.landing_navbar')
    <div class="p-6 lg:p-7 mt-[60px] max-w-4xl mx-auto">
        <p class="text-xl md:text-2xl font-semibold text-gray-800 mb-4">About Us</p>
        <p class="text-gray-600 leading-relaxed mb-6">
            Welcome to <span class="font-semibold text-gray-900">{{ config('app.name') }}</span>,
            your trusted destination for cutting-edge technology devices and computer products.
            As an <span class="font-medium">E-Commerce retailer</span>, we aim to deliver a seamless shopping
            experience where innovation meets convenience.
        </p>

        <p class="text-lg font-semibold text-gray-800 mb-3">What We Do</p>
        <p class="text-gray-600 leading-relaxed mb-6">
            At {{ config('app.name') }}, we provide a wide selection of laptops, desktops, accessories,
            and the latest tech devices from top global brands. Whether you are a student,
            a professional, or a gamer, we strive to equip you with the technology
            you need to stay connected and empowered.
        </p>

        <p class="text-lg font-semibold text-gray-800 mb-3">Our Features</p>
        <ul class="list-disc list-inside text-gray-600 space-y-2 mb-6">
            <li>E-Commerce Online Shopping with secure checkout</li>
            <li>Fast & flexible shipping options</li>
            <li>User profiles with wishlist and saved items</li>
            <li>Order & payment history tracking</li>
            <li>Multiple card support powered by <span class="font-medium">Stripe</span></li>
            <li>Future-ready mobile version for shopping on the go</li>
            <li>And much more to enhance your online shopping experience</li>
        </ul>

        <p class="text-lg font-semibold text-gray-800 mb-3">Our Vision</p>
        <p class="text-gray-600 leading-relaxed mb-6">
            We believe in simplifying technology retail by offering
            a reliable, transparent, and customer-focused platform.
            Our vision is to be more than just an online store — we aspire
            to become a trusted partner in your digital lifestyle.
        </p>

        <div class="mt-8">
            <a href="{{ route('shop.get') }}" class="btn btn-primary">
                Start Shopping
            </a>
        </div>
    </div>
    @include('components.web_footer')
@endsection
