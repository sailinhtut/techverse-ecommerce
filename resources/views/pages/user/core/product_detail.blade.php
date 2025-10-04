@extends('layouts.web')

@section('head')
    <!-- Page Title & Meta -->
    <title>{{ $product['title'] }} | {{ config('app.name') }}</title>
    <meta name="description" content="{{ $product['short_description'] }}">
    <meta name="keywords" content="{{ implode(',', $product['tags'] ?? []) }}">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow">

    <!-- Open Graph (Facebook / LinkedIn) -->
    <meta property="og:title" content="{{ $product['title'] }}" />
    <meta property="og:description" content="{{ $product['short_description'] }}" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ $product['image'] }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $product['title'] }}">
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
                        <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}"
                            class="w-[300px] h-auto object-cover rounded">
                    @else
                        <img src="{{ asset('assets/images/computer_accessories.png') }}" alt="{{ $product['title'] }}"
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

            <div class="flex flex-col gap-2 lg:p-10">

                <p class="text-xl font-semibold mb-0 mt-5">{{ $product['title'] }}</p>

                <p class="font-semibold mb-1">
                    Price -
                    @if ($product['sale_price'])
                        <span
                            class="text-gray-500 line-through ms-2">${{ number_format($product['regular_price'], 2) }}</span>
                        <span
                            class="text-muted -decoration-line-through ms-2">${{ number_format($product['sale_price'], 2) }}</span>
                    @else
                        <span>${{ number_format($product['sale_price'] ?? $product['regular_price'], 2) }}</span>
                    @endif

                    <span class="{{ $product['stock'] > 0 ? 'text-green-800' : 'text-red-800' }} text-sm ml-2">
                        {{ $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' }}
                    </span>
                </p>

                @if ($product['stock'] > 0)
                    <div class="join join-horizontal" x-data>
                        <div class="w-10">
                            <input class="input join-item -z-10" name="quantity"
                                :value="$store.cart.items[{{ $product['id'] }}] ? $store.cart.items[{{ $product['id'] }}]
                                    .quantity : 0"
                                min="1" max="{{ $product['stock'] }}" readonly />
                        </div>

                        <button type="button" class="btn join-item"
                            @click='$store.cart.addItem({
                                id: {{ $product['id'] }},
                                title: @json($product['title']),
                                slug: @json($product['slug']),
                                price: {{ $product['regular_price'] }},
                                image: @json($product['image'])
                            })'>
                            Add to Cart
                        </button>
                    </div>
                @endif

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
        const html = document.documentElement;
        const originalScrollBehavior = html.style.scrollBehavior;

        html.style.scrollBehavior = 'auto';

        const scrollY = sessionStorage.getItem('shopScroll');
        if (scrollY) {
            window.scrollTo(0, parseInt(scrollY));
            sessionStorage.removeItem('shopScroll');
        }

        html.style.scrollBehavior = originalScrollBehavior;

        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', () => {
                sessionStorage.setItem('shopScroll', window.scrollY);
            });
        });
    </script>

    <script>
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
