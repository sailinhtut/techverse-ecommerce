@extends('layouts.app')

@section('app_content')
    @include('components.landing_navbar')

    <div
        class="lg:min-h-[90vh] flex flex-col-reverse lg:flex-row items-center bg-gradient-to-br from-primary/30 to-purple-100">
        <div class="w-full lg:w-1/2 p-6 lg:p-16 text-center lg:text-left">
            <p class="text-2xl font-semibold mb-3 pt-12 lg:pt-48">Welcome to Tech Verse Ecommerce Store</p>
            <p class="mb-4 text-sm lg:text-base">
                Discover premium devices, accessories, and cutting-edge technology with exclusive discounts and deals.
                Shop smarter and upgrade your digital lifestyle today.
            </p>
            <a href="{{ route('shop.get') }}" class="btn btn-primary">Shop Now</a>
            

        </div>
        <div class="w-full lg:w-1/2 flex justify-center pt-16">
            <div class="size-[200px] lg:size-[350px] mt-3 carousel slide" data-bs-touch="false" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active w-full flex justify-center items-center inset-0">
                        <img src="{{ asset('assets/images/computer_accessories.png') }}" alt="Computer Accessories"
                            class="size-[200px] lg:size-[350px] hover:scale-95 origin-center hover:-translate-y-4 transition-all duration-500 block">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('assets/images/computer_shelf.png') }}" alt="Computer Accessories"
                            class="size-[200px] lg:size-[350px] hover:scale-95 origin-center hover:-translate-y-4 transition-all duration-500">

                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('assets/images/computer_promotion.png') }}" alt="Computer Accessories"
                            class="size-[200px] lg:size-[350px] hover:scale-95 origin-center hover:-translate-y-4 transition-all duration-500">
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Brand + Device Section --}}
    <div class="py-12 lg:py-44 flex flex-col lg:flex-row items-center">
        <div class="w-full lg:w-1/2 p-6 lg:p-16 relative min-h-[400px] flex justify-center items-center">
            <div class="absolute top-10 right-10 bg-white shadow-md rounded-xl">
                <img src="{{ asset('assets/images/computer_accessories.png') }}" class="w-20 m-2">
            </div>
            <div class="absolute bottom-10 left-10 bg-white shadow-md rounded-xl">
                <img src="{{ asset('assets/images/computer_accessories.png') }}" class="w-20 m-2">
            </div>
            <p class="font-bold text-lg text-center">All You Need To <br> Start Your Dream</p>
        </div>
        <div class="w-full lg:w-1/2 p-6 lg:p-16">
            <p class="text-2xl font-semibold mb-3">Available Brands & Devices</p>
            <p class="text-sm lg:text-base mb-4">
                Explore trusted brands and devices at the best prices. Shop your favorite tech products today.
            </p>
            <div class="flex flex-wrap gap-2">
                @php
                    $available_brands = ['Apple', 'Dell', 'Acer', 'Asus', 'MSI', 'HP', 'Lenovo', 'Microsoft', 'Razer'];
                    $available_devices = [
                        'Desktop',
                        'Laptop',
                        'Smartphone',
                        'Tablet',
                        'Smartwatch',
                        'Game Console',
                        'Headphone',
                        'Earphone',
                        'Fitness Tracker',
                    ];
                @endphp
                @foreach (array_merge($available_brands, $available_devices) as $item)
                    <div
                        class="px-3 py-1 text-xs lg:text-sm border border-slate-300 bg-gray-100 rounded-full select-none hover:bg-gray-800 hover:text-white hover:scale-105 hover:shadow-md transition-all duration-300">
                        {{ $item }}
                    </div>
                @endforeach
                <a href="{{ route('shop.get') }}"
                    class="px-3 py-1 text-xs lg:text-sm border border-slate-300 rounded-full bg-gray-800 text-white flex items-center group transition-all duration-300 active:scale-95">
                    <i class="bi bi-cart3 mr-2 group-hover:scale-110 transition-all"></i> See More
                </a>
            </div>
        </div>
    </div>

    {{-- Features Section --}}
    <div class="py-12 lg:py-44 bg-gray-100 px-6 lg:px-16">
        <p class="text-2xl font-semibold text-center mb-8">Why Shop With Us?</p>
        <div class="grid gap-6 md:grid-cols-3 text-center">
            <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition">
                <i class="bi bi-truck text-2xl text-amber-600"></i>
                <p class="font-semibold mt-2">Fast Delivery</p>
                <p class="text-sm text-gray-600">Get your products delivered quickly and reliably.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition">
                <i class="bi bi-shield-check text-2xl text-amber-600"></i>
                <p class="font-semibold mt-2">Secure Shopping</p>
                <p class="text-sm text-gray-600">Your data and purchases are fully protected.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition">
                <i class="bi bi-gift text-2xl text-amber-600"></i>
                <p class="font-semibold mt-2">Exclusive Deals</p>
                <p class="text-sm text-gray-600">Enjoy discounts and special offers year-round.</p>
            </div>
        </div>
    </div>

    {{-- Testimonial Section --}}
    <div class="py-12 lg:py-44 px-6 lg:px-16 ">
        <p class="text-2xl font-semibold text-center mb-8">What Our Customers Say</p>
        <div class="grid gap-6 md:grid-cols-3">
            <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition">
                <p class="text-sm italic">“Amazing service and top-notch quality products. My laptop arrived in 2 days!”</p>
                <p class="mt-3 font-semibold">– Sarah L.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition">
                <p class="text-sm italic">“Best store for tech lovers. Found exactly what I wanted at a great price.”</p>
                <p class="mt-3 font-semibold">– David K.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition">
                <p class="text-sm italic">“I trust Tech Verse for all my accessories. Reliable and affordable.”</p>
                <p class="mt-3 font-semibold">– Emma W.</p>
            </div>
        </div>
    </div>

    @include('components.web_footer')
@endsection
