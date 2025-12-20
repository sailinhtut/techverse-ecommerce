@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')

    @php
        $site_name = getParsedTemplate('site_name');
        $site_currency = getParsedTemplate('site_currency');
        $site_logo = getSiteLogoURL();
    @endphp

    {{-- Categories Bar --}}
    {{-- sticky top-[60px] --}}
    <div x-cloak
        class="hidden lg:flex flex-row md:justify-between bg-white z-10 w-full text-sm border-b border-b-base-300 relative"
        x-data="{ showCategoryBoard: false }">

        <div class="px-3 py-2 flex flex-row items-center gap-1 cursor-default hover:text-primary select-none"
            @click="showCategoryBoard = !showCategoryBoard" @mouseenter="showCategoryBoard = true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
            </svg>
            Categories
        </div>
        <div class="h-full flex-row items-center hidden md:flex">
            <a href="#todays-best" class="h-full py-2 w-full md:w-auto flex items-center hover:text-primary px-3">
                Best Deal
            </a>
            <a href="#populars" class="h-full py-2 w-full md:w-auto flex items-center hover:text-primary px-3">
                Popular Choices
            </a>
            <a href="#promotions-and-discounts"
                class="h-full py-2 w-full md:w-auto flex items-center hover:text-primary px-3">
                Seasonal Promotions
            </a>
            <a href="#for-you" class="h-full py-2 w-full md:w-auto flex items-center hover:text-primary px-3">
                What's New
            </a>
            <a href="/store-locator" class="h-full py-2 w-full md:w-auto flex items-center hover:text-primary px-3">
                Find Store
            </a>
        </div>

        <div class="p-3 pb-7 h-auto w-full bg-base-100 border-b border-b-base-300 absolute top-full left-0 right-0 transition-all duration-300 origin-top "
            :class="showCategoryBoard ? 'scale-y-100' : 'scale-y-0'" x-data="{ active_category_board_content: 1 }">
            <div class="flex flex-col lg:flex-row justify-start gap-3">
                <div class="px-3 w-full lg:w-[250px] hidden lg:block">
                    <p class="text-lg font-semibold flex flex-row items-center gap-2">
                        <img src="{{ $site_logo }}" alt="{!! $site_name !!}" class="h-8">
                        {!! $site_name !!}
                    </p>
                    {{-- <p class="text-xs italic text-justify">
                        Explore Your New Best Favourites
                    </p> --}}
                    <div class="mt-3 flex flex-col gap-1">
                        <a
                            class="flex flex-row items-center justify-between gap-1 text-sm cursor-pointer text-primary hover:bg-primary/20 hover:px-2 px-0 py-1 rounded-md group transition-all">
                            <div class="flex flex-row items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Explore Promotions
                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="size-4 group-hover:translate-x-0 -translate-x-[50px] group-hover:opacity-100 opacity-0 transition-all">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                        <a
                            class="flex flex-row items-center justify-between gap-1 text-sm cursor-pointer text-primary hover:bg-primary/20 hover:px-2 px-0 py-1 rounded-md group transition-all">
                            <div class="flex flex-row items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                                Coupons & Discounts
                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="size-4 group-hover:translate-x-0 -translate-x-[50px] group-hover:opacity-100 opacity-0 transition-all">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                        <a href="{{ route('frequent-questions.get') }}"
                            class="flex flex-row items-center justify-between gap-1 text-sm cursor-pointer text-primary hover:bg-primary/20 hover:px-2 px-0 py-1 rounded-md group transition-all">
                            <div class="flex flex-row items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                </svg>
                                FAQ
                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="size-4 group-hover:translate-x-0 -translate-x-[50px] group-hover:opacity-100 opacity-0 transition-all">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="w-full lg:pt-3 lg:ml-5 md:w-[200px] flex flex-col gap-2">
                    <a class="w-full font-semibold cursor-default hover:text-primary flex justify-between items-center group"
                        :class="`${active_category_board_content == 1 ? 'text-primary' : ''}`"
                        @click="active_category_board_content = 1">All Categories

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4 mr-5 transition-all hidden lg:inline"
                            :class="active_category_board_content == 1 ? 'translate-x-0 opacity-100' :
                                'group-hover:translate-x-0 group-hover:opacity-100 -translate-x-full opacity-0'">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>

                    <a class="font-semibold cursor-default hover:text-primary flex justify-between items-center group"
                        :class="`${active_category_board_content == 2 ? 'text-primary' : ''}`"
                        @click="active_category_board_content = 2">
                        All Brands
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4 mr-5 transition-all hidden lg:inline"
                            :class="active_category_board_content == 2 ? 'translate-x-0 opacity-100' :
                                'group-hover:translate-x-0 group-hover:opacity-100 -translate-x-full opacity-0'">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>

                    <a class="font-semibold cursor-default hover:text-primary flex justify-between items-center group"
                        :class="`${active_category_board_content == 3 ? 'text-primary' : ''}`"
                        @click="active_category_board_content = 3">
                        All Tags
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4 mr-5 transition-all hidden lg:inline"
                            :class="active_category_board_content == 3 ? 'translate-x-0 opacity-100' :
                                'group-hover:translate-x-0 group-hover:opacity-100 -translate-x-full opacity-0'">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>

                <div x-transition x-show="active_category_board_content == 1"
                    class="w-full md:flex-1 pt-3 flex flex-row flex-wrap gap-3 h-fit">
                    @foreach ($categories as $category)
                        <div class="flex flex-col items-start gap-1 w-[150px]">
                            <a href="{{ '/shop/search/category/' . $category['slug'] }}"
                                class="cursor-default font-semibold hover:text-primary hover:underline">{{ $category['name'] }}</a>
                            @if (isset($category['children']))
                                @foreach ($category['children'] as $sub_category)
                                    <a href="{{ '/shop/search/category/' . $sub_category['slug'] }}"
                                        class="cursor-default hover:text-primary hover:underline">{{ $sub_category['name'] }}</a>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>

                <div x-transition x-show="active_category_board_content == 2" class="w-full md:flex-1 pt-3">
                    <p class="font-semibold">All Brands</p>
                    <div class="mt-3 flex h-fit flex-wrap gap-2">
                        @foreach ($brands as $brand)
                            <a href="{{ '/shop/search/brand/' . $brand['slug'] }}"
                                class="cursor-default capitalize px-3 text-sm rounded-full py-0.5 border border-base-300 text-primary hover:bg-primary hover:text-primary-content">
                                {{ $brand['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div x-transition x-show="active_category_board_content == 3" class="w-full md:flex-1 pt-3">
                    <p class="font-semibold">Popular Tags</p>
                    <div class="mt-3 flex h-fit flex-wrap gap-2">
                        @foreach ($tags as $tag)
                            <a href="{{ '/shop/search/tag/' . Str::slug($tag) }}"
                                class="cursor-default capitalize px-3 text-sm rounded-full py-0.5 border border-base-300 text-primary hover:bg-primary hover:text-primary-content">
                                {{ $tag }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Mobile Carousel Slider --}}
    <div x-data="carousel()" x-show="visible" x-init="startAutoSlide()"
        class="group w-full h-[30vh] sm:h-[40vh] lg:hidden relative overflow-hidden z-0">

        <!-- Slides -->
        <template x-for="(image, index) in images" :key="image.id">
            <div class="absolute inset-0 transition-opacity duration-700"
                :class="current === index ? 'opacity-100 z-10 pointer-events-auto' : 'opacity-0 z-0 pointer-events-none'">

                <template x-if="image.link">
                    <a :href="image.link" target="_self" rel="noopener" class="block w-full h-full">
                        <img :src="image.image" alt="" class="w-full h-full object-fill" />
                    </a>
                </template>

                <template x-if="!image.link">
                    <img :src="image.image" alt="" class="w-full h-full object-fill" />
                </template>

            </div>
        </template>

        <!-- Controls (visible on hover) -->
        <div
            class="absolute flex justify-between w-full px-4 transform -translate-y-1/2 top-1/2 z-20 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
            <button type="button" class="btn btn-circle btn-sm" @click="prev()">❮</button>
            <button type="button" class="btn btn-circle btn-sm" @click="next()">❯</button>
        </div>

        <!-- Indicators -->
        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2 z-20">
            <template x-for="(image, index) in images" :key="image.id">
                <div class="w-2 h-2 rounded-full cursor-pointer"
                    :class="current === index ? 'bg-primary' : 'bg-white opacity-50'" @click="goTo(index)"></div>
            </template>
        </div>
    </div>

    {{-- Pop Up Carousel --}}
    <div x-data="popupCarousel()" x-init="showFirst()" x-show="visible"
        class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="relative max-w-[300px] md:max-w-2xl w-full p-0 h-[250px] md:h-[400px] pointer-events-auto">

            <!-- Close Button -->
            <button @click="close()" class="btn btn-sm btn-circle absolute right-2 top-2 z-20">✕</button>

            <!-- Carousel Images -->
            <template x-for="(image, index) in images" :key="image.image">
                <div class="absolute inset-0 transition-opacity duration-500"
                    :class="currentIndex === index ? 'opacity-100 z-10 pointer-events-auto' :
                        'opacity-0 z-0 pointer-events-none'">
                    <a :href="image.link" target="_self" rel="noopener">
                        <img :src="image.image" alt="" class="w-full h-full object-cover rounded-lg">
                    </a>
                </div>
            </template>

            <!-- Controls -->
            <div class="absolute flex justify-between w-full px-4 transform -translate-y-1/2 top-1/2 z-20">
                <button class="btn btn-circle btn-sm" @click="prev()">❮</button>
                <button class="btn btn-circle btn-sm" @click="next()">❯</button>
            </div>
        </div>
    </div>



    <div class="p-3 sm:p-5 lg:p-10">

        {{-- Header Focus Section --}}
        <div x-data='carousel()' x-show="visible" x-init="startAutoSlide()"
            class="w-full hidden lg:flex h-[400px] flex-row items-start justify-center gap-3">


            {{-- Category List --}}
            <div class="w-[250px] h-[400px] p-2  hidden xl:block">
                <div class="flex flex-col border border-base-300 rounded-md">
                    <div class="px-3 py-2 border-b border-b-base-300 font-semibold text-primary">Popular Categories</div>
                    @php
                        // Recursive function to flatten categories
                        function flattenCategories(array $categories, &$flat = [])
                        {
                            foreach ($categories as $cat) {
                                $flat[] = $cat; // add current category
                                if (!empty($cat['children'])) {
                                    flattenCategories($cat['children'], $flat); // add children recursively
                                }
                            }
                            return $flat;
                        }

                        $allCategories = $categories?->all() ?? [];

                        $flatCategories = flattenCategories($allCategories);

                        shuffle($flatCategories);
                        $shuffledCategories = array_slice($flatCategories, 0, 8);
                    @endphp

                    @if (!empty($shuffledCategories))
                        @foreach ($shuffledCategories as $category)
                            <a href="{{ '/shop/search/category/' . $category['slug'] }}"
                                class="select-none px-3 py-2 hover:bg-base-200 hover:text-primary border-b border-b-base-200 group flex items-center justify-between
                           {{ $loop->last ? 'rounded-b-md border-b-0' : '' }}">
                                <div>
                                    {{ $category['name'] }}
                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="size-4 group-hover:translate-x-0 -translate-x-[50px] group-hover:stroke-primary group-hover:opacity-100 opacity-0 transition-all">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 text-center mt-4">No categories available.</p>
                    @endif
                </div>
            </div>


            {{-- Carousel Slider --}}
            <div class="group w-[600px] shrink-0 h-full relative overflow-hidden z-0">

                <!-- Slides -->
                <template x-for="(image, index) in images" :key="image.id">
                    <div class="absolute inset-0 transition-opacity duration-700"
                        :class="current === index ? 'opacity-100 z-10 pointer-events-auto' : 'opacity-0 z-0 pointer-events-none'">
                        <a :href="image.link" target="_self" rel="noopener">
                            <img :src="image.image" alt=""
                                class="w-full h-full object-fill border border-base-300 opacity-0 transition-opacity"
                                onload="this.style.opacity=1;" />
                        </a>
                    </div>
                </template>

                <!-- Controls (only visible on hover) -->
                <div
                    class="absolute flex justify-between w-full px-4 transform -translate-y-1/2 top-1/2 z-20
                            opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                    <button class="btn btn-circle btn-sm" @click="prev()">❮</button>
                    <button class="btn btn-circle btn-sm" @click="next()">❯</button>
                </div>

                <!-- Indicators -->
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2 z-20">
                    <template x-for="(image, index) in images" :key="image.id">
                        <div class="w-2 h-2 rounded-full cursor-pointer"
                            :class="current === index ? 'bg-primary' : 'bg-white opacity-50'" @click="goTo(index)"></div>
                    </template>
                </div>
            </div>

            {{-- Today Best Products --}}
            <div class="h-full w-[300px] flex flex-col items-start px-2" x-data>
                <p class="font-semibold">Today's Best</p>
                <div class="mt-2 w-full h-full overflow-auto flex flex-col  gap-1">
                    @php
                        $sortedPinned = collect($pinned_products)
                            ->sortByDesc(fn($p) => $p['regular_price'] - $p['sale_price'])
                            ->take(4);
                    @endphp

                    @foreach ($sortedPinned as $product)
                        <a href="{{ url('/shop/' . $product['slug']) }}"
                            class="w-full h-[85px] lg:max-w-[400px] bg-base-100 border border-base-300 shadow-sm hover:shadow-md transition flex flex-row gap-3 items-start">

                            <div class="w-[80px] h-full overflow-hidden shrink-0">
                                <img src="{{ $product['image'] ?? asset('assets/images/computer_accessories.png') }}"
                                    alt="{{ $product['name'] }}" class="w-full h-full object-cover bg-white">
                            </div>

                            <div class="flex flex-col select-none py-2 px-3">
                                <p class="text-sm font-semibold line-clamp-1">{{ $product['name'] }}</p>

                                @if (empty($product['sale_price']))
                                    <div class="text-xs flex items-center gap-2">
                                        <span class="font-semibold">{{ number_format($product['regular_price'], 2) }}
                                            {{ $site_currency }}</span>
                                    </div>
                                @else
                                    <div class="text-xs flex items-center gap-2">
                                        <span class="text-gray-400 line-through">
                                            {{ number_format($product['regular_price'], 2) }} {{ $site_currency }}
                                        </span>
                                        <span class="font-semibold">
                                            {{ number_format($product['sale_price'], 2) }} {{ $site_currency }}
                                        </span>
                                    </div>

                                    @php
                                        $discount = round(
                                            (($product['regular_price'] - $product['sale_price']) /
                                                $product['regular_price']) *
                                                100,
                                        );
                                    @endphp

                                    @if ($discount > 0)
                                        <span class="mt-1 text-xs text-green-600 font-semibold">
                                            {{ $discount }}% OFF
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </a>
                    @endforeach

                </div>
            </div>

            {{-- Advertise --}}
            <div class="w-[150px] h-[400px] py-5  hidden 2xl:block">
                <div class="h-full flex flex-col gap-3">
                    <div
                        class="flex-1 flex items-center justify-between gap-3 p-3 rounded-md bg-gradient-to-r from-violet-500 to-purple-600 text-white select-none">
                        <p class="text-sm">Immersive Buying</p>
                        <div class="p-1 rounded-full bg-white animate-bounce">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6 stroke-violet-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </div>
                    </div>

                    <div
                        class="flex-1 flex items-center justify-between gap-3 p-3 rounded-md bg-gradient-to-r from-green-500 to-lime-500 text-white select-none">
                        <p class="text-sm">Fast Delivery</p>
                        <div class="p-1 rounded-full bg-white animate-pulse">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6 stroke-green-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                            </svg>
                        </div>
                    </div>

                    <div
                        class="flex-1 flex items-center justify-between gap-3 p-3 rounded-md bg-gradient-to-r from-blue-500 to-indigo-600 text-white select-none">
                        <p class="text-sm">24/7 Support</p>
                        <div class="p-1 rounded-full bg-white animate-pulse">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6 stroke-blue-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                            </svg>
                        </div>
                    </div>

                    <div
                        class="flex-1 flex items-center justify-between gap-3 p-3 rounded-md bg-gradient-to-r from-amber-400 to-yellow-500 text-white select-none">
                        <p class="text-sm">Secure Payments</p>
                        <div class="p-1 rounded-full bg-white animate-pulse">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6 stroke-amber-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                            </svg>
                        </div>
                    </div>

                    <div
                        class="flex-1 flex items-center justify-between gap-3 p-3 rounded-md bg-gradient-to-r from-pink-500 to-rose-500 text-white select-none">
                        <p class="text-sm">Quality Guarantee</p>
                        <div class="p-1 rounded-full bg-white animate-pulse">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6 stroke-pink-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex flex-row justify-around">
            {{-- Banner Ads --}}
            @if (isset($banner_images) && !empty($banner_images) && count($banner_images) > 0)
                <div class="hidden sticky top-[100px] mt-[50px] w-[200px] h-full p-5 lg:flex flex-col">
                    <p class="text-sm font-semibold">Trending Updates</p>
                    @foreach ($banner_images as $banner)
                        <a href="{{ $banner['link'] }}" class="w-full h-auto my-2" target="_self">
                            <img src="{{ $banner['image'] }}" alt="{{ $banner['title'] }}"
                                class="w-full h-auto border border-base-300 opacity-0 transition-opacity duration-500"
                                onload="this.style.opacity=1;">
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Product Lists Display --}}
            <div class="w-full lg:w-4/5 mb-10">
                {{-- Mobile Search & Filter --}}
                <div class="flex flex-col gap-3">
                    <div class="w-full flex lg:hidden flex-col gap-2">
                        <form action="/shop/search/" method="GET" x-data="{ loading: false }" @submit="loading = true">
                            <p class="label font-semibold mb-2 text-black">Search Products</p>
                            <div class="flex items-center gap-2">
                                <input type="text" name="q" placeholder="Search..." class="input w-full"
                                    value="{{ old('q', $query ?? '') }}" />

                                <button class="btn btn-primary" type="submit" :disabled="loading"
                                    x-text="loading ? 'Searching...' : 'Search'">
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @if (isset($today_best_display) && $today_best_display == true)
                    <div class="mt-5 w-full scroll-mt-[120px]" x-data="pinnedProductState()"
                        x-show="loading || products.length > 0" x-cloak id="todays-best">
                        <div class="flex flex-row justify-between items-center">
                            <p class="font-semibold lg:text-lg">Today's Best</p>
                            <button class="btn btn-sm btn-ghost btn-primary" @click="showViewAll=!showViewAll"
                                x-text="showViewAll ? 'Collapse' : 'View All'"></button>
                        </div>

                        <template x-if="products.length === 0 && !loading">
                            <div class="mt-3 w-full flex flex-col items-center justify-center py-20 gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                                <p class="text-gray-500 col-span-full text-center">No Product Found</p>
                            </div>
                        </template>

                        <div x-show="loading"
                            class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 place-items-center gap-3">
                            <template x-for="i in 6" :key="i">
                                <div class="skeleton w-[150px] h-48 rounded-lg"></div>
                            </template>
                        </div>

                        {{-- Horizontal Scroll --}}
                        <div class="relative" x-show="!showViewAll" x-cloak>
                            <div x-ref="scrollContainer"
                                class="mt-3 flex gap-5 justify-start overflow-auto hidden-scrollbar p-2">
                                <template x-for="(product,index) in products" :key="product.slug">
                                    <div x-data='itemCardState()'
                                        class="min-w-[150px] max-w-[150px] shrink-0 grow-0 bg-base-100 shadow-md border border-base-300 select-none hover:shadow-lg transition-all relative rounded-lg">
                                        <img :src="product.image ?? '{{ asset('assets/images/computer_accessories.png') }}'"
                                            class="w-full h-24 lg:h-32 p-2 lg:p-5 object-contain" alt="">
                                        <div class="p-2 flex flex-col justify-between h-[calc(100%-12rem)]">
                                            <div>
                                                <a :href="`/shop/${product.slug}`"
                                                    class="text-sm font-semibold line-clamp-1" x-text="product.name"
                                                    @click="saveCurrentState()"></a>
                                                {{-- <p class="text-sm text-gray-600 mt-1 line-clamp-2"
                                                    x-text="product.short_description ?? 'No Description'"></p> --}}
                                            </div>
                                            <div class="mt-2 mb-1 flex flex-col items-start justify-between gap-2">
                                                <div class="flex text-sm font-semibold">
                                                    <template x-if="product.sale_price">
                                                        <div class="text-sm ">
                                                            <span class="text-gray-500 line-through">
                                                                <span>
                                                                    <span x-text="product.regular_price"></span>
                                                                </span>
                                                            </span>
                                                            <span class="">
                                                                <span>
                                                                    <span x-text="product.sale_price"></span>
                                                                    {{ $site_currency }}
                                                                </span>
                                                            </span>
                                                            <span class="text-[10px]"
                                                                x-text="`${Math.round(((product.regular_price - product.sale_price) / product.regular_price) * 100)}% Off`"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="!product.sale_price">
                                                        <span><span x-text="product.regular_price"></span>
                                                            {{ $site_currency }}</span>
                                                    </template>
                                                </div>
                                                <div>
                                                    <button @click="addItemToCart(product)" class="btn btn-sm btn-square">
                                                        <template x-show="addingToCart" x-if="addingToCart">
                                                            <span class="loading loading-spinner loading-sm"></span>
                                                        </template>
                                                        <svg x-show="!addingToCart" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 24 24" class="size-4" aria-hidden="true"
                                                            fill="none" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path
                                                                d="M3 3h2l1.6 9.6a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H6" />
                                                            <circle cx="10" cy="20" r="1.5" />
                                                            <circle cx="18" cy="20" r="1.5" />
                                                        </svg>
                                                    </button>
                                                    <button x-show="$store.cart.getQuantity(product.id)>0"
                                                        @click="removeFromCart(product)" class="btn btn-sm btn-square">
                                                        <svg x-show="!removingFromCart" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                                            <path fill-rule="evenodd"
                                                                d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                                                clip-rule="evenodd" />
                                                        </svg>

                                                        <template x-if="removingFromCart">
                                                            <span class="loading loading-spinner loading-sm"></span>
                                                        </template>
                                                    </button>

                                                    <button @click="addWishlist(product)" class="btn btn-sm btn-square">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            x-show="!addingToWishlist && $store.wishlist.isWishlist(product.id)"
                                                            viewBox="0 0 24 24" fill="currentColor"
                                                            class="size-4 fill-primary">
                                                            <path
                                                                d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                                        </svg>

                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            x-show="!addingToWishlist && !$store.wishlist.isWishlist(product.id)"
                                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                        </svg>
                                                        <template x-if="addingToWishlist">
                                                            <span class="loading loading-spinner loading-sm"></span>
                                                        </template>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="$store.cart.getQuantity(product.id)>0" x-cloak
                                            class="absolute top-0 left-2-0 -translate-y-1/3 -translate-x-1/3 flex items-center justify-center size-6 bg-primary text-primary-content rounded-md text-sm"
                                            x-text="$store.cart.getQuantity(product.id)">
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-1 w-full flex justify-end gap-3">
                                <button class="btn btn-circle" @click="prev()">❮</button>
                                <button class="btn btn-circle" @click="next()">❯</button>
                            </div>
                        </div>


                        {{-- View All Vertical Scroll with Pagination --}}
                        <div class="mt-3 w-full grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 place-items-center gap-5"
                            x-show="showViewAll" x-cloak>
                            <template x-for="(product,index) in products" :key="index">
                                <div x-data='itemCardState()'
                                    class="min-w-[150px] max-w-[150px] bg-base-100 shadow-md border border-base-300 select-none hover:shadow-lg transition-all relative rounded-lg">
                                    <img :src="product.image ?? '{{ asset('assets/images/computer_accessories.png') }}'"
                                        class="w-full h-24 lg:h-32 p-2 lg:p-5 object-contain" alt="">
                                    <div class="p-2 flex flex-col justify-between h-[calc(100%-12rem)]">
                                        <div>
                                            <a :href="`/shop/${product.slug}`" class="text-sm font-semibold line-clamp-1"
                                                x-text="product.name" @click="saveCurrentState()"></a>
                                            {{-- <p class="text-sm text-gray-600 mt-1 line-clamp-2"
                                                x-text="product.short_description ?? 'No Description'"></p> --}}
                                        </div>
                                        <div class="mt-2 mb-1 flex flex-col items-start justify-between gap-2">
                                            <div class="flex text-sm font-semibold">
                                                <template x-if="product.sale_price">
                                                    <div class="text-sm ">
                                                        <span class="text-gray-500 line-through">
                                                            <span>
                                                                <span x-text="product.regular_price"></span>
                                                            </span>
                                                        </span>
                                                        <span class="">
                                                            <span>
                                                                <span x-text="product.sale_price"></span>
                                                                {{ $site_currency }}
                                                            </span>
                                                        </span>
                                                        <span class="text-[10px]"
                                                            x-text="`${Math.round(((product.regular_price - product.sale_price) / product.regular_price) * 100)}% Off`"></span>
                                                    </div>
                                                </template>
                                                <template x-if="!product.sale_price">
                                                    <span><span x-text="product.regular_price"></span>
                                                        {{ $site_currency }}</span>
                                                </template>
                                            </div>
                                            <div>
                                                <button @click="addItemToCart(product)" class="btn btn-sm btn-square">
                                                    <template x-show="addingToCart" x-if="addingToCart">
                                                        <span class="loading loading-spinner loading-sm"></span>
                                                    </template>
                                                    <svg x-show="!addingToCart" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24" class="size-4" aria-hidden="true"
                                                        fill="none" stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M3 3h2l1.6 9.6a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H6" />
                                                        <circle cx="10" cy="20" r="1.5" />
                                                        <circle cx="18" cy="20" r="1.5" />
                                                    </svg>
                                                </button>
                                                <button x-show="$store.cart.getQuantity(product.id)>0"
                                                    @click="removeFromCart(product)" class="btn btn-sm btn-square">
                                                    <svg x-show="!removingFromCart" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                                        <path fill-rule="evenodd"
                                                            d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <template x-if="removingFromCart">
                                                        <span class="loading loading-spinner loading-sm"></span>
                                                    </template>
                                                </button>

                                                <button @click="addWishlist(product)" class="btn btn-sm btn-square">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        x-show="!addingToWishlist && $store.wishlist.isWishlist(product.id)"
                                                        viewBox="0 0 24 24" fill="currentColor"
                                                        class="size-4 fill-primary">
                                                        <path
                                                            d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                                    </svg>

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        x-show="!addingToWishlist && !$store.wishlist.isWishlist(product.id)"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                    </svg>
                                                    <template x-if="addingToWishlist">
                                                        <span class="loading loading-spinner loading-sm"></span>
                                                    </template>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="$store.cart.getQuantity(product.id)>0" x-cloak
                                        class="absolute top-0 left-2-0 -translate-y-1/3 -translate-x-1/3 flex items-center justify-center size-6 bg-primary text-primary-content rounded-md text-sm"
                                        x-text="$store.cart.getQuantity(product.id)">
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div x-cloak x-show="!loading && pagination && pagination.next_page_url && showViewAll"
                            class="flex justify-center items-center py-5">
                            <button class="btn btn-sm btn-ghost" @click="loadMoreProducts()">Load More Today's
                                Best</button>
                        </div>
                    </div>
                @endif


                @if (isset($popular_display) && $popular_display == true)
                    <div class="mt-5 w-full scroll-mt-[120px]" x-data="propularProductState()"
                        x-show="loading || products.length > 0" x-cloak id="populars">
                        <div class="flex flex-row justify-between items-center">
                            <p class="font-semibold lg:text-lg">Popular Products</p>
                            <button class="btn btn-sm btn-ghost btn-primary" @click="showViewAll=!showViewAll"
                                x-text="showViewAll ? 'Collapse' : 'View All'"></button>
                        </div>

                        <template x-if="products.length === 0 && !loading">
                            <div class="mt-3 w-full flex flex-col items-center justify-center py-20 gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                                <p class="text-gray-500 col-span-full text-center">No Product Found</p>
                            </div>
                        </template>

                        <div x-show="loading"
                            class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 place-items-center gap-3">
                            <template x-for="i in 6" :key="i">
                                <div class="skeleton w-[150px] h-48 rounded-lg"></div>
                            </template>
                        </div>

                        {{-- Horizontal Scroll --}}
                        <div class="relative" x-show="!showViewAll" x-cloak>
                            <div x-ref="scrollContainer"
                                class="mt-3 flex gap-5 justify-start overflow-auto hidden-scrollbar p-2">
                                <template x-for="(product,index) in products" :key="product.slug">
                                    <div x-data='itemCardState()'
                                        class="min-w-[150px] max-w-[150px] shrink-0 grow-0 bg-base-100 shadow-md border border-base-300 select-none hover:shadow-lg transition-all relative rounded-lg">
                                        <img :src="product.image ?? '{{ asset('assets/images/computer_accessories.png') }}'"
                                            class="w-full h-24 lg:h-32 p-2 lg:p-5 object-contain" alt="">
                                        <div class="p-2 flex flex-col justify-between h-[calc(100%-12rem)]">
                                            <div>
                                                <a :href="`/shop/${product.slug}`"
                                                    class="text-sm font-semibold line-clamp-1" x-text="product.name"
                                                    @click="saveCurrentState()"></a>
                                                {{-- <p class="text-sm text-gray-600 mt-1 line-clamp-2"
                                                    x-text="product.short_description ?? 'No Description'"></p> --}}
                                            </div>
                                            <div class="mt-2 mb-1 flex flex-col items-start justify-between gap-2">
                                                <div class="flex text-sm font-semibold">
                                                    <template x-if="product.sale_price">
                                                        <div class="text-sm ">
                                                            <span class="text-gray-500 line-through">
                                                                <span>
                                                                    <span x-text="product.regular_price"></span>
                                                                </span>
                                                            </span>
                                                            <span class="">
                                                                <span>
                                                                    <span x-text="product.sale_price"></span>
                                                                    {{ $site_currency }}
                                                                </span>
                                                            </span>
                                                            <span class="text-[10px]"
                                                                x-text="`${Math.round(((product.regular_price - product.sale_price) / product.regular_price) * 100)}% Off`"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="!product.sale_price">
                                                        <span><span x-text="product.regular_price"></span>
                                                            {{ $site_currency }}</span>
                                                    </template>
                                                </div>
                                                <div>
                                                    <button @click="addItemToCart(product)" class="btn btn-sm btn-square">
                                                        <template x-show="addingToCart" x-if="addingToCart">
                                                            <span class="loading loading-spinner loading-sm"></span>
                                                        </template>
                                                        <svg x-show="!addingToCart" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 24 24" class="size-4" aria-hidden="true"
                                                            fill="none" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path
                                                                d="M3 3h2l1.6 9.6a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H6" />
                                                            <circle cx="10" cy="20" r="1.5" />
                                                            <circle cx="18" cy="20" r="1.5" />
                                                        </svg>
                                                    </button>
                                                    <button x-show="$store.cart.getQuantity(product.id)>0"
                                                        @click="removeFromCart(product)" class="btn btn-sm btn-square">
                                                        <svg x-show="!removingFromCart" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                                            <path fill-rule="evenodd"
                                                                d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        <template x-if="removingFromCart">
                                                            <span class="loading loading-spinner loading-sm"></span>
                                                        </template>
                                                    </button>

                                                    <button @click="addWishlist(product)" class="btn btn-sm btn-square">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            x-show="!addingToWishlist && $store.wishlist.isWishlist(product.id)"
                                                            viewBox="0 0 24 24" fill="currentColor"
                                                            class="size-4 fill-primary">
                                                            <path
                                                                d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                                        </svg>

                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            x-show="!addingToWishlist && !$store.wishlist.isWishlist(product.id)"
                                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                        </svg>
                                                        <template x-if="addingToWishlist">
                                                            <span class="loading loading-spinner loading-sm"></span>
                                                        </template>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="$store.cart.getQuantity(product.id)>0" x-cloak
                                            class="absolute top-0 left-2-0 -translate-y-1/3 -translate-x-1/3 flex items-center justify-center size-6 bg-primary text-primary-content rounded-md text-sm"
                                            x-text="$store.cart.getQuantity(product.id)">
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-1 w-full flex justify-end gap-3">
                                <button class="btn btn-circle" @click="prev()">❮</button>
                                <button class="btn btn-circle" @click="next()">❯</button>
                            </div>
                        </div>


                        {{-- View All Vertical Scroll with Pagination --}}
                        <div class="mt-3 w-full grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 place-items-center gap-5"
                            x-show="showViewAll" x-cloak>
                            <template x-for="(product,index) in products" :key="index">
                                <div x-data='itemCardState()'
                                    class="min-w-[150px] max-w-[150px]bg-base-100 shadow-md border border-base-300 select-none hover:shadow-lg transition-all relative rounded-lg">
                                    <img :src="product.image ?? '{{ asset('assets/images/computer_accessories.png') }}'"
                                        class="w-full h-24 lg:h-32 p-2 lg:p-5 object-contain" alt="">
                                    <div class="p-2 flex flex-col justify-between h-[calc(100%-12rem)]">
                                        <div>
                                            <a :href="`/shop/${product.slug}`" class="text-sm font-semibold line-clamp-1"
                                                x-text="product.name" @click="saveCurrentState()"></a>
                                            {{-- <p class="text-sm text-gray-600 mt-1 line-clamp-2"
                                                x-text="product.short_description ?? 'No Description'"></p> --}}
                                        </div>
                                        <div class="mt-2 mb-1 flex flex-col items-start justify-between gap-2">
                                            <div class="flex text-sm font-semibold">
                                                <template x-if="product.sale_price">
                                                    <div class="text-sm ">
                                                        <span class="text-gray-500 line-through">
                                                            <span>
                                                                <span x-text="product.regular_price"></span>
                                                            </span>
                                                        </span>
                                                        <span class="">
                                                            <span>
                                                                <span x-text="product.sale_price"></span>
                                                                {{ $site_currency }}
                                                            </span>
                                                        </span>
                                                        <span class="text-[10px]"
                                                            x-text="`${Math.round(((product.regular_price - product.sale_price) / product.regular_price) * 100)}% Off`"></span>
                                                    </div>
                                                </template>
                                                <template x-if="!product.sale_price">
                                                    <span><span x-text="product.regular_price"></span>
                                                        {{ $site_currency }}</span>
                                                </template>
                                            </div>
                                            <div>
                                                <button @click="addItemToCart(product)" class="btn btn-sm btn-square">
                                                    <template x-show="addingToCart" x-if="addingToCart">
                                                        <span class="loading loading-spinner loading-sm"></span>
                                                    </template>
                                                    <svg x-show="!addingToCart" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24" class="size-4" aria-hidden="true"
                                                        fill="none" stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M3 3h2l1.6 9.6a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H6" />
                                                        <circle cx="10" cy="20" r="1.5" />
                                                        <circle cx="18" cy="20" r="1.5" />
                                                    </svg>
                                                </button>
                                                <button x-show="$store.cart.getQuantity(product.id)>0"
                                                    @click="removeFromCart(product)" class="btn btn-sm btn-square">
                                                    <svg x-show="!removingFromCart" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                                        <path fill-rule="evenodd"
                                                            d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <template x-if="removingFromCart">
                                                        <span class="loading loading-spinner loading-sm"></span>
                                                    </template>
                                                </button>

                                                <button @click="addWishlist(product)" class="btn btn-sm btn-square">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        x-show="!addingToWishlist && $store.wishlist.isWishlist(product.id)"
                                                        viewBox="0 0 24 24" fill="currentColor"
                                                        class="size-4 fill-primary">
                                                        <path
                                                            d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                                    </svg>

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        x-show="!addingToWishlist && !$store.wishlist.isWishlist(product.id)"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                    </svg>
                                                    <template x-if="addingToWishlist">
                                                        <span class="loading loading-spinner loading-sm"></span>
                                                    </template>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="$store.cart.getQuantity(product.id)>0" x-cloak
                                        class="absolute top-0 left-2-0 -translate-y-1/3 -translate-x-1/3 flex items-center justify-center size-6 bg-primary text-primary-content rounded-md text-sm"
                                        x-text="$store.cart.getQuantity(product.id)">
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- <div x-show="loading" class="mt-3 flex justify-center items-center py-5 ">
                            <span class="loading loading-spinner loading-lg text-primary"></span>
                        </div> --}}

                        <div x-cloak x-show="!loading && pagination && pagination.next_page_url && showViewAll"
                            class="flex justify-center items-center py-5">
                            <button class="btn btn-sm btn-ghost" @click="loadMoreProducts()">Load More Popular</button>
                        </div>
                    </div>
                @endif


                @if (isset($promotion_display) && $promotion_display)
                    <div class="mt-5 w-full scroll-mt-[120px]" x-data="promotionProductState()"
                        x-show="loading || products.length > 0" x-cloak id="promotions-and-discounts">
                        <div class="flex flex-row justify-between items-center">
                            <p class="font-semibold lg:text-lg">Promotions and Discounts</p>
                            <button class="btn btn-sm btn-ghost btn-primary" @click="showViewAll=!showViewAll"
                                x-text="showViewAll ? 'Collapse' : 'View All'"></button>
                        </div>

                        <template x-if="products.length === 0 && !loading">
                            <div class="mt-3 w-full flex flex-col items-center justify-center py-20 gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                                <p class="text-gray-500 col-span-full text-center">No Product Found</p>
                            </div>
                        </template>

                        <div x-show="loading"
                            class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 place-items-center gap-3">
                            <template x-for="i in 6" :key="i">
                                <div class="skeleton w-[150px] h-48 rounded-lg"></div>
                            </template>
                        </div>

                        {{-- Horizontal Scroll --}}
                        <div class="relative" x-show="!showViewAll" x-cloak>
                            <div x-ref="scrollContainer"
                                class="mt-3 flex gap-5 justify-start overflow-auto hidden-scrollbar p-2">
                                <template x-for="(product,index) in products" :key="product.slug">
                                    <div x-data='itemCardState()'
                                        class="min-w-[150px] max-w-[150px] shrink-0 grow-0 bg-base-100 shadow-md border border-base-300 select-none hover:shadow-lg transition-all relative rounded-lg">
                                        <img :src="product.image ?? '{{ asset('assets/images/computer_accessories.png') }}'"
                                            class="w-full h-24 lg:h-32 p-2 lg:p-5 object-contain" alt="">

                                        <div class="p-2 flex flex-col justify-between h-[calc(100%-12rem)]">
                                            <div>
                                                <a :href="`/shop/${product.slug}`"
                                                    class="text-sm font-semibold line-clamp-1" x-text="product.name"
                                                    @click="saveCurrentState()"></a>
                                                {{-- <p class="text-sm text-gray-600 mt-1 line-clamp-2"
                                                    x-text="product.short_description ?? 'No Description'"></p> --}}
                                            </div>
                                            <div class="mt-2 mb-1 flex flex-col items-start justify-between gap-2">
                                                 <div class="flex text-sm font-semibold">
                                                    <template x-if="product.sale_price">
                                                        <div class="text-sm ">
                                                            <span class="text-gray-500 line-through">
                                                                <span>
                                                                    <span x-text="product.regular_price"></span>
                                                                </span>
                                                            </span>
                                                            <span class="">
                                                                <span>
                                                                    <span x-text="product.sale_price"></span>
                                                                    {{ $site_currency }}
                                                                </span>
                                                            </span>
                                                            <span class="text-[10px]"
                                                                x-text="`${Math.round(((product.regular_price - product.sale_price) / product.regular_price) * 100)}% Off`"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="!product.sale_price">
                                                        <span><span x-text="product.regular_price"></span>
                                                            {{ $site_currency }}</span>
                                                    </template>
                                                </div>
                                                <div>
                                                    <button @click="addItemToCart(product)" class="btn btn-sm btn-square">
                                                        <template x-show="addingToCart" x-if="addingToCart">
                                                            <span class="loading loading-spinner loading-sm"></span>
                                                        </template>
                                                        <svg x-show="!addingToCart" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 24 24" class="size-4" aria-hidden="true"
                                                            fill="none" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path
                                                                d="M3 3h2l1.6 9.6a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H6" />
                                                            <circle cx="10" cy="20" r="1.5" />
                                                            <circle cx="18" cy="20" r="1.5" />
                                                        </svg>
                                                    </button>
                                                    <button x-show="$store.cart.getQuantity(product.id)>0"
                                                        @click="removeFromCart(product)" class="btn btn-sm btn-square">
                                                        <svg x-show="!removingFromCart" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                                            <path fill-rule="evenodd"
                                                                d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        <template x-if="removingFromCart">
                                                            <span class="loading loading-spinner loading-sm"></span>
                                                        </template>
                                                    </button>

                                                    <button @click="addWishlist(product)" class="btn btn-sm btn-square">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            x-show="!addingToWishlist && $store.wishlist.isWishlist(product.id)"
                                                            viewBox="0 0 24 24" fill="currentColor"
                                                            class="size-4 fill-primary">
                                                            <path
                                                                d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                                        </svg>

                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            x-show="!addingToWishlist && !$store.wishlist.isWishlist(product.id)"
                                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                        </svg>
                                                        <template x-if="addingToWishlist">
                                                            <span class="loading loading-spinner loading-sm"></span>
                                                        </template>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="$store.cart.getQuantity(product.id)>0" x-cloak
                                            class="absolute top-0 left-2-0 -translate-y-1/3 -translate-x-1/3 flex items-center justify-center size-6 bg-primary text-primary-content rounded-md text-sm"
                                            x-text="$store.cart.getQuantity(product.id)">
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-1 w-full flex justify-end gap-3">
                                <button class="btn btn-circle" @click="prev()">❮</button>
                                <button class="btn btn-circle" @click="next()">❯</button>
                            </div>
                        </div>


                        {{-- View All Vertical Scroll with Pagination --}}
                        <div class="mt-3 w-full grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 place-items-center gap-5"
                            x-show="showViewAll" x-cloak>
                            <template x-for="(product,index) in products" :key="index">
                                <div x-data='itemCardState()'
                                    class="min-w-[150px] max-w-[150px] bg-base-100 shadow-md border border-base-300 select-none hover:shadow-lg transition-all relative rounded-lg">
                                    <img :src="product.image ?? '{{ asset('assets/images/computer_accessories.png') }}'"
                                        class="w-full h-24 lg:h-32 p-2 lg:p-5 object-contain" alt="">
                                    <div class="p-2 flex flex-col justify-between h-[calc(100%-12rem)]">
                                        <div>
                                            <a :href="`/shop/${product.slug}`" class="text-sm font-semibold line-clamp-1"
                                                x-text="product.name" @click="saveCurrentState()"></a>
                                            {{-- <p class="text-sm text-gray-600 mt-1 line-clamp-2"
                                                x-text="product.short_description ?? 'No Description'"></p> --}}
                                        </div>
                                        <div class="mt-2 mb-1 flex flex-col items-start justify-between gap-2">
                                             <div class="flex text-sm font-semibold">
                                                <template x-if="product.sale_price">
                                                    <div class="text-sm ">
                                                        <span class="text-gray-500 line-through">
                                                            <span>
                                                                <span x-text="product.regular_price"></span>
                                                            </span>
                                                        </span>
                                                        <span class="">
                                                            <span>
                                                                <span x-text="product.sale_price"></span>
                                                                {{ $site_currency }}
                                                            </span>
                                                        </span>
                                                        <span class="text-[10px]"
                                                            x-text="`${Math.round(((product.regular_price - product.sale_price) / product.regular_price) * 100)}% Off`"></span>
                                                    </div>
                                                </template>
                                                <template x-if="!product.sale_price">
                                                    <span><span x-text="product.regular_price"></span>
                                                        {{ $site_currency }}</span>
                                                </template>
                                            </div>
                                            <div>
                                                <button @click="addItemToCart(product)" class="btn btn-sm btn-square">
                                                    <template x-show="addingToCart" x-if="addingToCart">
                                                        <span class="loading loading-spinner loading-sm"></span>
                                                    </template>
                                                    <svg x-show="!addingToCart" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24" class="size-4" aria-hidden="true"
                                                        fill="none" stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M3 3h2l1.6 9.6a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H6" />
                                                        <circle cx="10" cy="20" r="1.5" />
                                                        <circle cx="18" cy="20" r="1.5" />
                                                    </svg>
                                                </button>
                                                <button x-show="$store.cart.getQuantity(product.id)>0"
                                                    @click="removeFromCart(product)" class="btn btn-sm btn-square">
                                                    <svg x-show="!removingFromCart" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                                        <path fill-rule="evenodd"
                                                            d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <template x-if="removingFromCart">
                                                        <span class="loading loading-spinner loading-sm"></span>
                                                    </template>
                                                </button>

                                                <button @click="addWishlist(product)" class="btn btn-sm btn-square">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        x-show="!addingToWishlist && $store.wishlist.isWishlist(product.id)"
                                                        viewBox="0 0 24 24" fill="currentColor"
                                                        class="size-4 fill-primary">
                                                        <path
                                                            d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                                    </svg>

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        x-show="!addingToWishlist && !$store.wishlist.isWishlist(product.id)"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                    </svg>
                                                    <template x-if="addingToWishlist">
                                                        <span class="loading loading-spinner loading-sm"></span>
                                                    </template>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="$store.cart.getQuantity(product.id)>0" x-cloak
                                        class="absolute top-0 left-2-0 -translate-y-1/3 -translate-x-1/3 flex items-center justify-center size-6 bg-primary text-primary-content rounded-md text-sm"
                                        x-text="$store.cart.getQuantity(product.id)">
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- <div x-show="loading" class="mt-3 flex justify-center items-center py-5 ">
                            <span class="loading loading-spinner loading-lg text-primary"></span>
                        </div> --}}

                        <div x-cloak x-show="!loading && pagination && pagination.next_page_url && showViewAll"
                            class="flex justify-center items-center py-5">
                            <button class="btn btn-sm btn-ghost" @click="loadMoreProducts()">Load More Promotions</button>
                        </div>
                    </div>
                @endif

                <div class="mt-5 w-full scroll-mt-[120px]" x-data="mainProductListState()" id="for-you">
                    <div class="flex flex-col gap-2 items-start mb-5">
                        <p class="lg:text-lg font-semibold">
                            {{ isset($product_list_title) ? $product_list_title : 'Available Products' }}
                        </p>
                        @if (isset($go_home_display) && $go_home_display)
                            <a href="{{ route('shop.get') }}" class="btn btn-sm btn-outline ">Go Back To Shop</a>
                        @endif
                    </div>

                    <template x-if="products.length === 0">
                        <div class="mt-3 w-full flex flex-col items-center justify-center py-[20vh] gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </svg>
                            <p class="text-gray-500 col-span-full text-center">No Product Found</p>
                        </div>
                    </template>

                    <div class="fixed inset-0 bg-base-100/70 backdrop-blur-sm flex flex-col items-center justify-center 
                                z-[9999] transition-opacity duration-500"
                        x-show="restoring" x-transition.opacity>
                        <span class="loading loading-spinner loading-lg text-primary mb-3"></span>
                        <p class="text-gray-600 font-medium text-sm">Restoring your view...</p>
                    </div>

                    <div
                        class="mt-3 w-full grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 place-items-center gap-5">
                        <template x-for="(product,index) in products" :key="index">
                            <div x-data='itemCardState()'
                                class="min-w-[150px] max-w-[150px] bg-base-100 shadow-md border border-base-300 select-none hover:shadow-lg transition-all relative rounded-lg">
                                <img :src="product.image ?? '{{ asset('assets/images/computer_accessories.png') }}'"
                                    class="w-full h-24 lg:h-32 p-2 lg:p-5 object-contain" alt="">
                                <div class="p-2 flex flex-col justify-between h-[calc(100%-12rem)]">
                                    <div>
                                        <a :href="`/shop/${product.slug}`" class="text-sm font-semibold line-clamp-1"
                                            x-text="product.name" @click="saveCurrentState()"></a>
                                        {{-- <p class="text-sm text-gray-600 mt-1 line-clamp-2"
                                            x-text="product.short_description ?? 'No Description'"></p> --}}
                                    </div>
                                    <div class="mt-2 mb-1 flex flex-col items-start justify-between gap-2">
                                         <div class="flex text-sm font-semibold">
                                            <template x-if="product.sale_price">
                                                <div class="text-sm ">
                                                    <span class="text-gray-500 line-through">
                                                        <span>
                                                            <span x-text="product.regular_price"></span>
                                                        </span>
                                                    </span>
                                                    <span class="">
                                                        <span>
                                                            <span x-text="product.sale_price"></span>
                                                            {{ $site_currency }}
                                                        </span>
                                                    </span>
                                                    <span class="text-[10px]"
                                                        x-text="`${Math.round(((product.regular_price - product.sale_price) / product.regular_price) * 100)}% Off`"></span>
                                                </div>
                                            </template>
                                            <template x-if="!product.sale_price">
                                                <span><span x-text="product.regular_price"></span>
                                                    {{ $site_currency }}</span>
                                            </template>
                                        </div>
                                        <div>
                                            <button @click="addItemToCart(product)" class="btn btn-sm btn-square">
                                                <template x-show="addingToCart" x-if="addingToCart">
                                                    <span class="loading loading-spinner loading-sm"></span>
                                                </template>
                                                <svg x-show="!addingToCart" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24" class="size-4" aria-hidden="true" fill="none"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M3 3h2l1.6 9.6a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H6" />
                                                    <circle cx="10" cy="20" r="1.5" />
                                                    <circle cx="18" cy="20" r="1.5" />
                                                </svg>
                                            </button>
                                            <button x-show="$store.cart.getQuantity(product.id)>0"
                                                @click="removeFromCart(product)" class="btn btn-sm btn-square">
                                                <svg x-show="!removingFromCart" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                                    <path fill-rule="evenodd"
                                                        d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <template x-if="removingFromCart">
                                                    <span class="loading loading-spinner loading-sm"></span>
                                                </template>
                                            </button>

                                            <button @click="addWishlist(product)" class="btn btn-sm btn-square">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    x-show="!addingToWishlist && $store.wishlist.isWishlist(product.id)"
                                                    viewBox="0 0 24 24" fill="currentColor" class="size-4 fill-primary">
                                                    <path
                                                        d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                                </svg>

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    x-show="!addingToWishlist && !$store.wishlist.isWishlist(product.id)"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                </svg>
                                                <template x-if="addingToWishlist">
                                                    <span class="loading loading-spinner loading-sm"></span>
                                                </template>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="$store.cart.getQuantity(product.id)>0" x-cloak
                                    class="absolute top-0 left-2-0 -translate-y-1/3 -translate-x-1/3 flex items-center justify-center size-6 bg-primary text-primary-content rounded-md text-sm"
                                    x-text="$store.cart.getQuantity(product.id)">
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="loading" class="mt-3 flex justify-center items-center py-5 ">
                        <span class="loading loading-spinner loading-lg text-primary"></span>
                    </div>

                    <div x-show="!loading && pagination && pagination.next_page_url"
                        class="flex justify-center items-center py-5">
                        <button class="btn btn-sm btn-ghost" @click="loadMoreProducts()">Load More Products</button>
                    </div>


                    {{-- Pagination --}}
                    {{-- <div class="flex flex-col md:flex-row justify-between items-start lg:items-center gap-3 mt-10"
                        x-show="!loading">
                        <div class="text-sm text-gray-500">
                            <span class="font-semibold" x-text="pagination.from"></span> –
                            <span class="font-semibold" x-text="pagination.to"></span>
                            of
                            <span class="font-semibold" x-text="pagination.total"></span>
                            results
                        </div>

                        <!-- Right side: pagination controls -->
                        <div class="flex flex-wrap gap-1">
                            <!-- Previous -->
                            <button class="btn btn-sm" :disabled="!pagination.prev_page_url"
                                @click="if(pagination.prev_page_url) fetchProducts(pagination.current_page - 1)">
                                «
                            </button>

                            <!-- First page -->
                            <button class="btn btn-sm" :class="{ 'btn-primary': pagination.current_page === 1 }"
                                @click="fetchProducts(1)">
                                1
                            </button>

                            <!-- Dots before -->
                            <template x-if="pagination.current_page > 3">
                                <span class="btn btn-sm btn-disabled">...</span>
                            </template>

                            <!-- Middle pages -->
                            <template x-for="page in visiblePages()" :key="page">
                                <button class="btn btn-sm" :class="{ 'btn-primary': page === pagination.current_page }"
                                    @click="fetchProducts(page)" x-text="page">
                                </button>
                            </template>

                            <!-- Dots after -->
                            <template x-if="pagination.current_page < pagination.last_page - 2">
                                <span class="btn btn-sm btn-disabled">...</span>
                            </template>

                            <!-- Last page -->
                            <template x-if="pagination.last_page > 1">
                                <button class="btn btn-sm"
                                    :class="{ 'btn-primary': pagination.current_page === pagination.last_page }"
                                    @click="fetchProducts(pagination.last_page)" x-text="pagination.last_page">
                                </button>
                            </template>

                            <!-- Next -->
                            <button class="btn btn-sm" :disabled="!pagination.next_page_url"
                                @click="if(pagination.next_page_url) fetchProducts(pagination.current_page + 1)">
                                »
                            </button>
                        </div>
                    </div> --}}
                </div>
            </div>

        </div>

        {{-- Stats Board --}}
        <div class="flex flex-col sm:flex-row justify-evenly gap-6 bg-gray-50 py-5 rounded-lg">

            <!-- Branches -->
            @if (isset($all_branch_count) && $all_branch_count > 0)
                <div class="flex flex-col items-center text-center text-slate-800 cursor-default">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 stroke-slate-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    <h3 class="text-base font-semibold">Branches</h3>
                    <p class="text-lg font-semibold mt-1" x-text="branchCount">{{ $all_branch_count }}</p>
                </div>
            @endif

            <!-- Products -->
            @if (isset($all_product_count) && $all_product_count > 0)
                <div class="flex flex-col items-center text-center text-slate-800 cursor-default">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 stroke-slate-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <h3 class="text-base font-semibold">Products</h3>
                    <p class="text-lg font-semibold mt-1" x-text="productCount">{{ $all_product_count }}</p>
                </div>
            @endif

            <!-- Categories -->
            @if (isset($all_categories_count) && $all_categories_count > 0)
                <div class="flex flex-col items-center text-center text-slate-800 cursor-default">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 stroke-slate-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                    </svg>
                    <h3 class="text-base font-semibold">Categories</h3>
                    <p class="text-lg font-semibold mt-1" x-text="categoryCount">{{ $all_categories_count }}</p>
                </div>
            @endif

            <!-- Brands -->
            @if (isset($all_brand_count) && $all_brand_count > 0)
                <div class="flex flex-col items-center text-center text-slate-800 cursor-default">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 stroke-slate-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                    <h3 class="text-base font-semibold">Brands</h3>
                    <p class="text-lg font-semibold mt-1" x-text="brandCount">{{ $all_brand_count }}</p>
                </div>
            @endif
        </div>
    </div>


    @include('components.web_footer')
@endsection

@push('script')
    <script>
        function popupCarousel() {
            return {
                originalImages: @json($popup_images ?? []),
                images: [],
                currentIndex: 0,
                visible: false,
                timer: null,
                interval: 4000, // 4 seconds
                get currentImage() {
                    return this.images[this.currentIndex] || null;
                },
                showFirst() {
                    // Load viewed images from localStorage
                    let viewed = JSON.parse(localStorage.getItem('popupViewed') || '[]');

                    // Filter out already viewed images
                    this.images = this.originalImages.filter(img => !viewed.includes(img.image));

                    if (this.images.length > 0) {
                        this.currentIndex = 0;
                        this.visible = true;
                        this.startAutoSlide();
                    }
                },
                next() {
                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                },
                prev() {
                    this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                },
                goTo(index) {
                    this.currentIndex = index;
                },
                startAutoSlide() {
                    this.stopAutoSlide();
                    this.timer = setInterval(() => this.next(), this.interval);
                },
                stopAutoSlide() {
                    if (this.timer) clearInterval(this.timer);
                },
                close() {
                    const viewed = JSON.parse(localStorage.getItem('popupViewed') || '[]');
                    const shownImages = this.images.map(img => img.image);
                    localStorage.setItem('popupViewed', JSON.stringify([...new Set([...viewed, ...shownImages])]));

                    this.visible = false;
                    this.stopAutoSlide();
                }
            }
        }

        function carousel() {
            return {
                interval: 4000,
                images: @json($carousel_images),
                current: 0,
                timer: null,
                get visible() {
                    return this.images.length > 0;
                },
                next() {
                    this.current = (this.current + 1) % this.images.length;
                },
                prev() {
                    this.current = (this.current - 1 + this.images.length) % this.images.length;
                },
                goTo(index) {
                    this.current = index;
                },
                startAutoSlide() {
                    this.timer = setInterval(() => this.next(), this.interval);
                },
                stopAutoSlide() {
                    clearInterval(this.timer);
                },
            };
        }

        function countdownTimer(endTime) {
            return {
                end: new Date(endTime),
                display: '',
                interval: null,
                init() {
                    this.update();
                    this.interval = setInterval(() => this.update(), 1000);
                },
                update() {
                    const now = new Date();
                    let diff = this.end - now;

                    if (diff <= 0) {
                        this.display = "Sale Ended";
                        clearInterval(this.interval);
                        return;
                    }

                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    diff -= days * (1000 * 60 * 60 * 24);

                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    diff -= hours * (1000 * 60 * 60);

                    const minutes = Math.floor(diff / (1000 * 60));
                    diff -= minutes * (1000 * 60);

                    const seconds = Math.floor(diff / 1000);

                    // pad single digits with 0
                    const pad = (n) => String(n).padStart(2, '0');

                    if (days > 0) {
                        this.display = `Flash Sale ${days}:${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                    } else if (hours > 0) {
                        this.display = `Flash Sale ${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                    } else {
                        this.display = `Flash Sale ${pad(minutes)}:${pad(seconds)}`;
                    }
                }
            }
        }

        function itemCardState() {
            return {
                addingToCart: false,
                removingFromCart: false,
                addingToWishlist: false,
                async addItemToCart(product) {
                    if (this.addingToCart) return;
                    this.addingToCart = true;

                    const added = await this.$store.cart.addItem({
                        product_id: product.id,
                        variant_id: null,
                        variant_combination: null,
                        quantity: 1,
                    });

                    this.addingToCart = false;
                },
                async removeFromCart(product) {
                    if (this.removingFromCart) return;
                    this.removingFromCart = true;
                    const removed = await this.$store.cart.removeByProductId(product.id);
                    this.removingFromCart = false;
                },
                async addWishlist(product) {
                    if (this.addingToWishlist) return;
                    this.addingToWishlist = true;
                    await this.$store.wishlist.addWishlist(product.id);
                    this.addingToWishlist = false;
                }
            }
        }

        function propularProductState() {
            return {
                loading: false,
                products: [],
                pagination: null,
                finishedPagination: false,
                showViewAll: false,
                scrollAmount: 200,

                init() {
                    this.loadMoreProducts();
                },
                async fetchProducts(page = 1, append = false) {
                    if (this.loading || this.finishedPagination) return;
                    this.loading = true;

                    try {
                        const res = await axios.get('/shop/api/popular-products', {
                            params: {
                                page: page
                            },
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                            },
                        });

                        if (append) {
                            this.products.push(...res.data.data);
                        } else {
                            this.products = res.data.data ?? [];
                        }

                        this.pagination = res.data;
                        this.finishedPagination = res.data.current_page >= res.data.last_page;

                    } catch (err) {
                        console.error('Error loading reviews:', err);
                        this.products = [];
                    } finally {
                        this.loading = false;
                    }
                },

                loadMoreProducts() {
                    if (this.pagination == null) {
                        this.fetchProducts(1, false);
                    } else if (this.pagination.next_page_url) {
                        this.fetchProducts(this.pagination.current_page + 1, true);
                    }
                },

                prev() {
                    this.$refs.scrollContainer.scrollBy({
                        left: -this.scrollAmount,
                        behavior: 'smooth'
                    });
                },
                next() {
                    this.$refs.scrollContainer.scrollBy({
                        left: this.scrollAmount,
                        behavior: 'smooth'
                    });
                },
            };
        }


        function pinnedProductState() {
            return {
                loading: false,
                products: [],
                pagination: null,
                finishedPagination: false,
                showViewAll: false,
                scrollAmount: 200,
                init() {
                    this.loadMoreProducts();
                },
                async fetchProducts(page = 1, append = false) {
                    if (this.loading || this.finishedPagination) return;
                    this.loading = true;

                    try {
                        const res = await axios.get('/shop/api/pinned-products', {
                            params: {
                                page: page
                            },
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                            },
                        });

                        if (append) {
                            this.products.push(...res.data.data);
                        } else {
                            this.products = res.data.data ?? [];
                        }

                        this.pagination = res.data;
                        this.finishedPagination = res.data.current_page >= res.data.last_page;

                    } catch (err) {
                        console.error('Error loading reviews:', err);
                        this.products = [];
                    } finally {
                        this.loading = false;
                    }
                },

                loadMoreProducts() {
                    if (this.pagination == null) {
                        this.fetchProducts(1, false);
                    } else if (this.pagination.next_page_url) {
                        this.fetchProducts(this.pagination.current_page + 1, true);
                    }
                },

                prev() {
                    this.$refs.scrollContainer.scrollBy({
                        left: -this.scrollAmount,
                        behavior: 'smooth'
                    });
                },
                next() {
                    this.$refs.scrollContainer.scrollBy({
                        left: this.scrollAmount,
                        behavior: 'smooth'
                    });
                },
            };
        }

        function promotionProductState() {
            return {
                loading: false,
                products: [],
                pagination: null,
                finishedPagination: false,
                showViewAll: false,
                showViewAll: false,
                scrollAmount: 200,
                init() {
                    this.loadMoreProducts();
                },
                async fetchProducts(page = 1, append = false) {
                    if (this.loading || this.finishedPagination) return;
                    this.loading = true;

                    try {

                        const res = await axios.get('/shop/api/promotion-products', {
                            params: {
                                page: page
                            },
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                            },
                        });

                        if (append) {
                            this.products.push(...res.data.data);
                        } else {
                            this.products = res.data.data ?? [];
                        }

                        this.pagination = res.data;
                        this.finishedPagination = res.data.current_page >= res.data.last_page;

                    } catch (err) {
                        console.error('Error loading reviews:', err);
                        this.products = [];
                    } finally {
                        this.loading = false;
                    }
                },

                loadMoreProducts() {
                    if (this.pagination == null) {
                        this.fetchProducts(1, false);
                    } else if (this.pagination.next_page_url) {
                        this.fetchProducts(this.pagination.current_page + 1, true);
                    }
                },

                prev() {
                    this.$refs.scrollContainer.scrollBy({
                        left: -this.scrollAmount,
                        behavior: 'smooth'
                    });
                },
                next() {
                    this.$refs.scrollContainer.scrollBy({
                        left: this.scrollAmount,
                        behavior: 'smooth'
                    });
                },
            };
        }

        function mainProductListState() {
            return {
                loading: false,
                restoring: true,
                categories: @json($categories ?? []),
                products: @json($products),
                tags: @json($tags ?? []),
                pagination: @json($pagination),
                finishedPagination: false,
                searchedQuery: @json($query ?? null),
                search: '',
                storageKey: 'product_list_state',

                async init() {
                    this.loadCurrentState();
                },

                async fetchProducts(page = 1, append = false) {
                    if (this.loading || this.finishedPagination) return;
                    this.loading = true;

                    try {
                        let url = this.pagination.next_page_url;
                        const res = await axios.get(url, {
                            params: {
                                q: this.searchedQuery
                            },
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json"
                            },
                        });

                        if (append) {
                            this.products.push(...res.data.data);
                        } else {
                            this.products = res.data.data ?? [];
                        }

                        this.pagination = res.data;
                        this.finishedPagination = res.data.current_page >= res.data.last_page;
                    } catch (err) {
                        console.error('Error loading products:', err);
                        this.products = [];
                    } finally {
                        this.loading = false;
                    }
                },

                loadMoreProducts() {
                    if (this.pagination == null) {
                        this.fetchProducts(1, false);
                    } else if (this.pagination.next_page_url) {
                        this.fetchProducts(this.pagination.current_page + 1, true);
                    }
                },

                visiblePages() {
                    const total = this.pagination.last_page || 1;
                    const current = this.pagination.current_page || 1;
                    const pages = [];
                    const start = Math.max(2, current - 1);
                    const end = Math.min(total - 1, current + 1);
                    for (let i = start; i <= end; i++) pages.push(i);
                    return pages;
                },

                encodeBase64Unicode(str) {
                    return btoa(unescape(encodeURIComponent(str)));
                },

                decodeBase64Unicode(str) {
                    return decodeURIComponent(escape(atob(str)));
                },

                saveCurrentState() {
                    const state = {
                        products: this.products,
                        pagination: this.pagination,
                        finishedPagination: this.finishedPagination,
                        scrollY: window.scrollY
                    };
                    try {
                        const encrypted = this.encodeBase64Unicode(JSON.stringify(state));
                        sessionStorage.setItem(this.storageKey, encrypted);
                    } catch (e) {
                        console.error("Failed to save state:", e);
                    }
                },

                loadCurrentState() {
                    const savedState = sessionStorage.getItem(this.storageKey);
                    if (savedState) {
                        try {
                            const decrypted = this.decodeBase64Unicode(savedState);
                            const state = JSON.parse(decrypted);

                            this.products = state.products || [];
                            this.pagination = state.pagination || {};
                            this.finishedPagination = state.finishedPagination || false;

                            this.$nextTick(() => {
                                window.scrollTo({
                                    top: state.scrollY || 0,
                                    behavior: 'instant'
                                });
                                setTimeout(() => (this.restoring = false), 200);
                            });
                            sessionStorage.removeItem(this.storageKey);
                            return;
                        } catch (e) {
                            console.error("Failed to restore state:", e);
                        }
                    }

                    this.restoring = false;
                }
            };
        }
    </script>
@endpush
