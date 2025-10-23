@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')
   
    <div class=" p-4 lg:p-10 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-7 gap-2 lg:gap-5">
       

        @if (count($products) === 0)
            <p class="text-gray-500 min-h-screen">No Product Found</p>
        @endif
        @foreach ($products as $product)
            <div
                class="bg-base-100 border border-base-300 shadow-md rounded-lg select-none overflow-hidden hover:shadow-lg transition relative">
                @if (!empty($product['image']))
                    <img src="{{ $product['image'] }}" class="w-full h-24 lg:h-40 p-2 lg:p-5 object-contain" alt="">
                @else
                    <img src="{{ asset('assets/images/computer_accessories.png') }}"
                        class="w-full h-24 lg:h-40 p-2 lg:p-5 object-contain" alt="">
                @endif
                <div class="p-3 flex flex-col justify-between h-[calc(100%-12rem)]">
                    <div>
                        <a href="{{ route('shop.slug.get', ['slug' => $product['slug']]) }}"
                            class="font-semibold line-clamp-1">{{ $product['name'] }}</a>
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

                        {{-- <button x-data
                            @click='$store.cart.addItem({
                                id: {{ $product['id'] }},
                                name: @json($product['name']),
                                slug: @json($product['slug']),
                                price: {{ $product['regular_price'] }},
                                image: @json($product['image']),
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


                        </button> --}}

                    </div>
                </div>


                @auth
                    @php
                        $wishlistItem = collect($wishlists)->firstWhere('product_id', $product['id']);
                    @endphp

                    @if ($wishlistItem)
                        <form action="{{ route('wishlist.id.delete', $wishlistItem['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                            <button type="submit" class="btn btn-ghost btn-square absolute top-1 right-1">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-6 text-primary">
                                    <path
                                        d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('wishlist.post') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                            <button type="submit" class="btn btn-ghost btn-square absolute top-1 right-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6 text-primary">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>

                            </button>
                        </form>
                    @endif
                @endauth




            </div>
        @endforeach
    </div>
    @include('components.web_footer')
@endsection

@push('script')

    @if (session('clear_cart'))
        <script>
            console.log('Clear cart triggered from backend');
            localStorage.removeItem('cart_state');
        </script>
    @endif

@endpush
