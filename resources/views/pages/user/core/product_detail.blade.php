@extends('layouts.web')

@section('head')
    <!-- Page Title & Meta -->
    <title>{{ $product['name'] }} | {{ config('app.name') }}</title>
    <meta name="description" content="{{ $product['short_description'] }}">
    <meta name="keywords" content="{{ implode(',', $product['tags'] ?? []) }}">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow">

    <!-- Open Graph (Facebook / LinkedIn) -->
    <meta property="og:ttile" content="{{ $product['name'] }}" />
    <meta property="og:description" content="{{ $product['short_description'] }}" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ $product['image'] }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $product['name'] }}">
    <meta name="twitter:description" content="{{ $product['short_description'] }}">
    <meta name="twitter:image" content="{{ $product['image'] }}">
@endsection

@section('web_content')
    @include('components.shop_navbar')
    <div class="p-6 lg:p-7 mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="flex flex-col justify-start items-center lg:p-10">
                <div class="overflow-hidden p-5">
                    @if (!empty($product['image']))
                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                            class="w-[300px] h-auto object-cover rounded">
                    @else
                        <img src="{{ asset('assets/images/computer_accessories.png') }}" alt="{{ $product['name'] }}"
                            class="w-[300px] h-auto object-cover rounded">
                    @endif
                </div>

                @if (!empty($product['image_gallery']))
                    <div class="self-start">
                        <p class="font-semibold text-sm">Image Gallery</p>
                        <div class="mt-3 flex gap-2 flex-wrap">
                            @foreach ($product['image_gallery'] as $index => $gallery)
                                <div onclick="image_gallery_{{ $index }}.showModal()">
                                    <img src="{{ $gallery['image'] }}" alt="{{ $gallery['label'] ?? 'Gallery Image' }}"
                                        class="border border-base-300 rounded cursor-pointer"
                                        style="width:60px; height:60px; object-fit:contain;">
                                    <dialog id="image_gallery_{{ $index }}" class="modal">
                                        <div class="modal-box max-h-[80vh]">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">{{ $gallery['label'] }}</p>
                                            <div class="mt-4 space-y-2">
                                                <img src="{{ $gallery['image'] }}" alt="{{ $gallery['label'] }}"
                                                    id="modalImage" class="w-full h-auto">
                                            </div>
                                            <div class="modal-action mt-3">
                                                <form method="dialog">
                                                    <button class="btn lg:btn-md">Close</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif



                <div class="modal fade " id="imageModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered ">
                        <div class="modal-content z-[2000]">
                            <div class="flex flex-row justify-between items-center pt-3 px-4">
                                <p class="text-lg font-semibold mb-0" id="modalTitle">Image Detail</p>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="py-2 px-4 modal-body">
                                <img src="" alt="" id="modalImage" class="w-full h-auto">
                            </div>
                            <div class="flex flex-row justify-end items-center gap-2 pe-3 pb-3">
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2 lg:p-10" x-data="productVariantData()">

                <p class="text-xl font-semibold mb-0 mt-5">{{ $product['name'] }}</p>

                <p class="font-semibold">
                    Price -
                    @if ($product['sale_price'])
                        <span
                            class="text-gray-500 line-through ms-2">${{ number_format($product['regular_price'], 2) }}</span>
                        <span
                            class="text-muted -decoration-line-through ms-2">${{ number_format($product['sale_price'], 2) }}</span>
                    @else
                        <span x-show="variantPrice" x-text="`$${variantPrice}`"></span>
                        <span
                            x-show="!variantPrice">${{ number_format($product['sale_price'] ?? $product['regular_price'], 2) }}</span>
                    @endif

                    @if ($product['enable_stock'] && empty($product['product_variants_selectors']))
                        <span class="{{ $product['stock'] > 0 ? 'text-green-800' : 'text-red-800' }} text-sm ml-2">
                            {{ $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    @endif
                </p>

                @if (!empty($product['product_variants_selectors']))
                    <div x-init="init()" class="mt-4 space-y-2 w-full">

                        @foreach ($product['product_variants_selectors'] as $key => $values)
                            <div class="w-[200px]">
                                <label class="block text-sm font-medium capitalize mb-1">{{ $key }}</label>
                                <select name="variant_{{ $key }}" class="select select-bordered w-full"
                                    x-model="selectedValues['{{ $key }}']">
                                    @foreach ($values as $value)
                                        <option value="{{ $value }}">{{ ucfirst($value) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach

                        <template x-if="loadingStock">
                            <p class="text-sm text-gray-500 mt-2">Checking stock...</p>
                        </template>

                        <template x-if="!loadingStock && variantStock !== null">
                            <p class="text-sm mt-2" :class="variantStock > 0 ? 'text-green-600' : 'text-red-600'">
                                <span x-text="variantStock > 0 ? `In stock (${variantStock})` : 'Out of stock'"></span>
                            </p>
                        </template>



                        <template x-if="stockError">
                            <p class="text-sm text-red-500 mt-2" x-text="stockError"></p>
                        </template>
                    </div>
                @endif



                <template x-if="Object.keys(selectors).length > 0">
                    <div class="mt-3 flex flex-col gap-2 flex-wrap">
                        <template
                            x-if="Object.keys(selectedValues).length === 0 || Object.values(selectedValues).some(v => !v)">
                            <p class="text-sm text-yellow-600 font-medium">
                                ⚠️ Please choose variant options first.
                            </p>
                        </template>

                        <div class="flex gap-2 flex-wrap"
                            x-show="!loadingStock && variantStock !== null && variantStock > 0 && !stockError && Object.values(selectedValues).every(v => v)"
                            x-transition>

                            <div class="join join-horizontal" x-data>
                                <div class="w-10">
                                    <input class="input join-item -z-10" name="quantity"
                                        :value="$store.cart.items[{{ $product['id'] }}] ?
                                            $store.cart.items[{{ $product['id'] }}].quantity :
                                            0"
                                        min="1" max="{{ $product['stock'] }}" readonly />
                                </div>

                                <button type="button" class="btn join-item"
                                    @click='$store.cart.addItem({
                                            id: {{ $product['id'] }},
                                            variant_id : variantId,
                                            name: @json($product['name']),
                                            slug : @json($product['slug']),
                                            sku: variantSku,
                                            price: variantPrice,
                                            image: @json($product['image'])
                                        })'>
                                    Add to Cart
                                </button>
                            </div>


                            @auth
                                @php
                                    $wishlistItem = collect($wishlists)->firstWhere('product_id', $product['id']);
                                @endphp

                                @if ($wishlistItem)
                                    <form action="{{ route('wishlist.id.delete', $wishlistItem['id']) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-square">
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
                                        <button type="submit" class="btn btn-square">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6 text-primary">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </template>

                <template x-if="Object.keys(selectors).length === 0">
                    <div class="mt-3 flex gap-2 flex-wrap" x-transition>
                        @if (($product['enable_stock'] && $product['stock'] > 0) || !$product['enable_stock'])
                            <div class="join join-horizontal" x-data>
                                <div class="w-10">
                                    <input class="input join-item -z-10" name="quantity"
                                        :value="$store.cart.items[{{ $product['id'] }}] ?
                                            $store.cart.items[{{ $product['id'] }}].quantity :
                                            0"
                                        min="1" max="{{ $product['stock'] }}" readonly />
                                </div>

                                <button type="button" class="btn join-item"
                                    @click='$store.cart.addItem({
                                        id: {{ $product['id'] }},
                                        variant_id : null,
                                        name: @json($product['name']),
                                        slug : @json($product['slug']),
                                        sku: @json($product['sku']),
                                        price: {{ $product['regular_price'] }},
                                        image: @json($product['image'])
                                    })'>
                                    Add to Cart
                                </button>
                            </div>
                        @endif
                    </div>
                </template>

                <div class="mt-3">
                    <p class='font-semibold'>Properties</p>
                    @if ($product['category'])
                        <p class="text-sm">
                            Category -
                            <span>{{ $product['category']['name'] }}</span>
                        </p>
                    @endif
                </div>

                <div class="mt-3">
                    <p class="font-semibold mb-1">Description</p>
                    <p class="text-sm" style="white-space: pre-line;">{{ $product['long_description'] }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function productVariantData() {
            return {
                product_id: {{ $product['id'] }},
                selectors: @json($product['product_variants_selectors']),
                variants: @json($product['product_variants']),
                selectedValues: {},
                variantPrice: null,
                variantId: null,
                variantSku: null,
                variantStock: null,
                loadingStock: false,
                stockError: '',

                init() {
                    Object.keys(this.selectors).forEach(key => {
                        this.selectedValues[key] = this.selectors[key][0];
                    });

                    // Fetch initial stock
                    this.fetchVariantStock();

                    // Watch each selected value for changes
                    Object.keys(this.selectors).forEach(key => {
                        this.$watch(`selectedValues.${key}`, () => {
                            this.fetchVariantStock();
                        });
                    });
                },

                async fetchVariantStock() {
                    try {
                        if (this.loadingStock) return;
                        this.loadingStock = true;
                        console.log('Fetching Variant Stock...');

                        console.log(this.selectedValues);

                        const response = await axios.post(
                            '/product/variant/check-variant-stock', {
                                product_id: this.product_id,
                                selected_values: this.selectedValues
                            }, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );

                        this.variantStock = response.data.data?.stock ?? null;
                        this.variantId = response.data.data?.id ?? null;
                        this.variantSku = response.data.data?.sku ?? null;
                        this.variantPrice = response.data.data?.price ?? null;
                        this.stockError = '';
                    } catch (error) {
                        this.variantStock = null;
                        this.variantId = null;
                        this.variantSku = null;
                        this.variantPrice = null;
                        this.stockError = error.response?.data?.message ?? 'Failed to fetch variant stock.';
                    } finally {
                        this.loadingStock = false;
                    }
                }
            };
        }

        window.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
                tooltipTriggerEl))

            const imageModal = document.getElementById('imageModal')
            imageModal.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget
                const imgSrc = trigger.getAttribute('data-bs-image')
                const imgLabel = trigger.getAttribute('data-bs-label')

                const modalImage = document.getElementById('modalImage')
                modalImage.src = imgSrc
                modalImage.alt = imgLabel

                const modalTitle = imageModal.querySelector('#modalTitle')
                modalTitle.textContent = imgLabel || 'Image Detail'
            })
        });
    </script>
@endpush
