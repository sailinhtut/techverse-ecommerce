@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')
    <div
        class=" p-4 lg:p-10 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-7 gap-2 lg:gap-5">
        @if (count($products) === 0)
            <p class="text-gray-500 min-h-screen">No Product Found</p>
        @endif
        @foreach ($products as $product)
            <div
                class="bg-base-100 border border-base-300 shadow-md rounded-lg select-none overflow-hidden hover:shadow-lg transition">
                @if (!empty($product['image']))
                    <img src="{{ $product['image'] }}" class="w-full h-24 lg:h-40 p-2 lg:p-5 object-contain" alt="">
                @else
                    <img src="{{ asset('assets/images/computer_accessories.png') }}"
                        class="w-full h-24 lg:h-40 p-2 lg:p-5 object-contain" alt="">
                @endif
                <div class="p-3 flex flex-col justify-between h-[calc(100%-12rem)]">
                    <div>
                        <a href="{{ route('shop.slug.get', ['slug' => $product['slug']]) }}"
                            class="font-semibold line-clamp-1">{{ $product['title'] }}</a>
                        <p class="text-sm text-gray-600 mt-1 line-clamp-1 lg:line-clamp-3">
                            {{ $product['short_description'] ?? 'No Description' }}</p>
                    </div>


                    <div class="mt-4 flex flex-col lg:flex-row items-start justify-between lg:items-center gap-2">
                        <div class="flex text-sm">
                            @if ($product['sale_price'])
                                <span
                                    class="text-gray-500 line-through">${{ number_format($product['regular_price'], 2) }}</span>
                                <span
                                    class="text-muted -decoration-line-through ms-2">${{ number_format($product['sale_price'], 2) }}</span>
                            @else
                                <span>${{ number_format($product['sale_price'] ?? $product['regular_price'], 2) }}</span>
                            @endif
                        </div>

                        <button x-data
                            @click='$store.cart.addItem({
                                id: {{ $product['id'] }},
                                title: @json($product['title']),
                                slug: @json($product['slug']),
                                price: {{ $product['regular_price'] }},
                                image: @json($product['image'])
                            })'
                            class="btn btn-sm lg:btn-square relative">


                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>

                            <span class="lg:hidden">Add To Cart</span>
                            <span class="badge badge-primary badge-sm absolute -top-2 -right-3 lg:-left-3"
                                x-text="$store.cart.items[{{ $product['id'] }}] ? $store.cart.items[{{ $product['id'] }}].quantity : ''"
                                x-show="$store.cart.items[{{ $product['id'] }}]"></span>


                        </button>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @include('components.web_footer')
@endsection
