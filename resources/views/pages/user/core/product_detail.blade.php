@php
    $site_name = getParsedTemplate('site_name');
    $site_currency = getParsedTemplate('site_currency');
    $site_logo = getSiteLogoURL();
@endphp

@extends('layouts.web')

@section('head')
    <!-- Page Title & Meta -->
    <title>{{ $product['name'] }} | {{ $site_name }}</title>
    <meta name="description" content="{{ $product['short_description'] }}">
    <meta name="keywords" content="{{ implode(',', $product['tags'] ?? []) }}">
    <meta name="author" content="{{ $site_name }}">
    <meta name="robots" content="index, follow">

    <!-- Open Graph (Facebook / LinkedIn) -->
    <meta property="og:ttile" content="{{ $product['name'] }}" />
    <meta property="og:description" content="{{ $product['short_description'] }}" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ $product['image'] }}" />
    <meta property="og:site_name" content="{{ $site_name }}" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $product['name'] }}">
    <meta name="twitter:description" content="{{ $product['short_description'] }}">
    <meta name="twitter:image" content="{{ $product['image'] }}">
@endsection

@section('web_content')
    @include('components.shop_navbar')
    <div class="p-4 md:p-5 lg:p-7 mx-auto" x-data="productVariantData()">
        <div class="mb-4">
            <button onclick="history.back()" class="btn btn-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </button>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="flex flex-col justify-start items-center lg:p-10">
                <div class="overflow-hidden  w-full h-[200px] lg:h-[300px]">
                    <img :src='variantProduct?.image ?? product.image ??
                        @json(asset('assets/images/computer_accessories.png'))'
                        alt="{{ $product['name'] }}" class="w-full h-full object-contain rounded">
                </div>

                @if (!empty($product['image_gallery']))
                    <div class="self-start mt-3">
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

            <div class="flex flex-col lg:p-10">
                <p class="text-xl font-semibold mb-0 mt-3">{{ $product['name'] }}</p>

                @if ($product['is_promotion'] && $product['promotion_end_time'])
                    <div x-data="countdownTimer('{{ $product['promotion_end_time'] }}')" class="mt-3 italic text-sm text-amber-300 flex items-center gap-1">
                        <span x-text="display"></span>
                    </div>
                @endif

                <p class="font-semibold" x-show="productType=='simple'" x-cloak>
                    Price 
                    @if ($product['sale_price'])
                        <span
                            class="text-gray-500 line-through">{{ number_format($product['regular_price'], 2) }}</span>
                        <span
                            class="text-muted -decoration-line-through">{{ number_format($product['sale_price'], 2) }}</span>
                        {{ $site_currency }}
                    @else
                        <span>{{ number_format($product['sale_price'] ?? $product['regular_price'], 2) }}
                            {{ $site_currency }}</span>
                    @endif

                    @if ($product['enable_stock'])
                        <span class="{{ $product['stock'] > 0 ? 'text-green-800' : 'text-red-800' }} text-sm ml-2">
                            {{ $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    @endif
                </p>


                <p class="font-semibold" x-show="productType=='variable' && variantProduct" x-cloak>
                    Price 
                    <span x-show='variantProduct.sale_price'>
                        <span class="text-gray-500 line-through" x-text='`${variantProduct.regular_price}`'></span>
                        <span class="text-muted -decoration-line-through"
                            x-text='`${variantProduct.sale_price}`'></span>
                    </span>

                    <span x-show='!variantProduct.sale_price' x-text='`${variantProduct.regular_price}`'></span>
                    {{ $site_currency }}
                </p>


                <div x-init="init()" class="mt-3 space-y-2 w-full" x-show="productType=='variable'" x-cloak>
                    @foreach ($product['product_variants_selectors'] as $key => $values)
                        <div class="w-[200px]">
                            <label class="block text-sm font-medium capitalize mb-1">{{ $key }}</label>
                            <select name="variant_{{ $key }}" class="select select-bordered w-full"
                                x-model="selectedValues['{{ $key }}']">
                                <option value="">None</option>
                                @foreach ($values as $value)
                                    <option value="{{ $value }}">{{ ucfirst($value) }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach

                    <p x-cloak x-show="loadingStock" class="text-sm text-gray-500">Checking stock...</p>

                    <p x-cloak x-show="!loadingStock && variantProduct && variantProduct.enable_stock" class="text-sm"
                        :class="variantProduct.stock > 0 ? 'text-green-600' : 'text-red-600'">
                        <span
                            x-text="variantProduct.stock > 0 ? `In stock (${variantProduct.stock})` : 'Out of stock'"></span>
                    </p>

                    <p x-cloak x-show="stockError" class="text-sm text-red-500" x-text="stockError"></p>

                </div>

                <div class="mt-3 flex flex-col gap-2 flex-wrap" x-cloak x-show="productType=='variable'" x-transition>
                    <div class="flex gap-2 flex-wrap" x-cloak x-show="!loadingStock && !stockError && variantProduct"
                        x-transition>

                        <div class="join join-horizontal"
                            x-show="(variantProduct.enable_stock && variantProduct.stock >0 ) || !variantProduct.enable_stock">
                            <div class="max-w-12 min-w-10">
                                <input class="input join-item -z-10" name="quantity"
                                    :value="$store.cart.getQuantity(product_id, variantProduct?.id ?? null)" min="1"
                                    :max="variantProduct.stock" readonly />
                            </div>

                            <button type="button" class="btn join-item" @click="addItemToCart()"
                                :disabled="addingToCart">
                                <div class="flex items-center gap-2">
                                    <span x-text="addingToCart ? 'Adding...' : 'Add to Cart'"></span>
                                    <template x-if="addingToCart">
                                        <span class="loading loading-spinner loading-sm"></span>
                                    </template>
                                </div>
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

                <div class="mt-3 flex gap-2 flex-wrap" x-cloak x-show="productType=='simple'">
                    @if (($product['enable_stock'] && $product['stock'] > 0) || !$product['enable_stock'])
                        <div class="join join-horizontal" x-data>
                            <div class="max-w-12 min-w-10">
                                <input class="input join-item -z-10" name="quantity"
                                    :value="$store.cart.getQuantity(product_id, variantProduct?.id ?? null)" min="1"
                                    max="{{ $product['stock'] }}" readonly />
                            </div>

                            <button type="button" class="btn join-item" @click="addItemToCart()"
                                :disabled="addingToCart">
                                <div class="flex items-center gap-2">
                                    <span x-text="addingToCart ? 'Adding...' : 'Add to Cart'"></span>
                                    <template x-if="addingToCart">
                                        <span class="loading loading-spinner loading-sm"></span>
                                    </template>
                                </div>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="mt-3 flex flex-col gap-1">
                    <p class='font-semibold'>Properties</p>

                    <p class="text-sm" x-show="variantProduct?.sku || product.sku">
                        SKU -
                        <span x-text="variantProduct?.sku ?? product.sku ?? 'None'"></span>
                    </p>

                    @if ($product['product_type'] && $product['product_type'] == 'variable')
                        <p class="text-sm">
                            Type -
                            <span>{{ $product['product_type'] == 'variable' ? 'Variable Product' : 'Simple Product' }}</span>
                        </p>
                    @endif
                    @if (isset($product['category']))
                        <p class="text-sm">
                            Category -
                            <span>{{ $product['category']['name'] }}</span>
                        </p>
                    @endif
                    @if (isset($product['brand']))
                        <p class="text-sm">
                            Brand -
                            <span>{{ $product['brand']['name'] }}</span>
                        </p>
                    @endif
                    @if (isset($product['width']))
                        <p class="text-sm">
                            Width -
                            <span>{{ $product['width'] }}cm</span>
                        </p>
                    @endif
                    @if (isset($product['length']))
                        <p class="text-sm">
                            Length -
                            <span>{{ $product['length'] }}cm</span>
                        </p>
                    @endif
                    @if (isset($product['height']))
                        <p class="text-sm">
                            Height -
                            <span>{{ $product['height'] }}cm</span>
                        </p>
                    @endif
                    @if (isset($product['weight']))
                        <p class="text-sm">
                            Weight -
                            <span>{{ $product['weight'] }}kg</span>
                        </p>
                    @endif
                </div>

                <div class="mt-3">
                    <p class="text-sm" style="white-space: pre-line;">{{ $product['short_description'] }}</p>
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
            </div>
        </div>

        <div class="mx-auto w-full lg:w-2/3 mt-5">
            <div class="tabs tabs-box bg-base-100 shadow-none justify-start lg:justify-center px-0"
                x-data='{ productType: @json($product['product_type'] ?? 'simple') }'>
                <input type="radio" name="active_tab" class="tab" aria-label="Description" checked="checked" />
                <div class="tab-content">
                    <div class="mt-5 min-h-[300px]">
                        @if (!empty($product['long_description']))
                            <div class="prose !text-justify w-full overflow-auto">{!! $product['long_description'] !!}</div>
                        @else
                            <p class="mt-20 text-sm text-gray-500 italic text-center">No Description Available</p>
                        @endif
                    </div>
                </div>

                <input type="radio" name="active_tab" class="tab" aria-label="Specification" />
                <div class="tab-content">
                    <div class="mt-5 min-h-[300px]">
                        @if (!empty($product['specifications']))
                            <table class="overflow-x-auto table table-zebra w-full border border-base-300">
                                <tbody>
                                    @foreach ($product['specifications'] as $spec)
                                        <tr>
                                            <th class="w-1/3">{{ $spec['key'] }}</th>
                                            <td>{{ $spec['value'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="mt-20 text-sm text-gray-500 italic text-center">No Specification Available</p>
                        @endif
                    </div>
                </div>

                @if ($product['enable_review'] ?? true)
                    <input type="radio" name="active_tab" class="tab" aria-label="Review & Rating" />
                    <div class="tab-content" x-data="{ rating: 0, comment: '', image: null, submitting: false }">
                        <div class="w-full flex flex-col items-start mt-5">
                            <p class="text-lg font-semibold">Product Rating</p>
                            <p class="mt-3 text-sm font-semibold">Product Name: {{ $product['name'] }}</p>
                            <p class="text-sm">{{ $product['short_description'] }}</p>



                            <p class="mt-3 text-sm font-semibold">
                                Overall Rating: {{ number_format($product['overall_review'] ?? 0, 1) }}/5.0
                            </p>

                            <div class="rating rating-sm rating-half mt-1 px-0 ">
                                <input type="radio" name="overall_rating_{{ $product['id'] }}" class="rating-hidden"
                                    disabled />
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="overall_rating_{{ $product['id'] }}"
                                        value="{{ $i - 0.5 }}" class="mask mask-star-2 mask-half-1 bg-amber-500"
                                        disabled @checked($product['overall_review'] >= $i - 0.5) />
                                    <input type="radio" name="overall_rating_{{ $product['id'] }}"
                                        value="{{ $i }}" class="mask mask-star-2 mask-half-2 bg-amber-500"
                                        disabled @checked($product['overall_review'] >= $i) />
                                @endfor
                            </div>

                            <div class="mt-5 w-full">
                                <p class="text-lg font-semibold mb-3">Write a Review</p>
                                <form
                                    x-on:submit.prevent="
                                submitting = true;
                                $refs.submitBtn.disabled = true;
                                $refs.submitBtn.innerText = 'Submitting...';
                                $el.submit();
                            "
                                    action='{{ route('shop.review.post') }}' method='POST'
                                    enctype='multipart/form-data'
                                    class="space-y-4 border border-base-300 rounded-box p-3 bg-base-100">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">

                                    <div>
                                        <label class="block text-sm font-medium mb-2">Your Rating</label>
                                        <div class="rating rating-sm rating-half">
                                            <input type="radio" x-model="rating" name="rating"
                                                class="rating-hidden" />
                                            <input type="radio" x-model="rating" name="rating" value="0.5"
                                                class="mask mask-star-2 mask-half-1 bg-amber-500" aria-label="0.5 star" />
                                            <input type="radio" x-model="rating" name="rating" value="1"
                                                class="mask mask-star-2 mask-half-2 bg-amber-500" aria-label="1 star" />
                                            <input type="radio" x-model="rating" name="rating" value="1.5"
                                                class="mask mask-star-2 mask-half-1 bg-amber-500" aria-label="1.5 star"
                                                checked="checked" />
                                            <input type="radio" x-model="rating" name="rating" value="2"
                                                class="mask mask-star-2 mask-half-2 bg-amber-500" aria-label="2 star" />
                                            <input type="radio" x-model="rating" name="rating" value="2.5"
                                                class="mask mask-star-2 mask-half-1 bg-amber-500" aria-label="2.5 star" />
                                            <input type="radio" x-model="rating" name="rating" value="3"
                                                class="mask mask-star-2 mask-half-2 bg-amber-500" aria-label="3 star" />
                                            <input type="radio" x-model="rating" name="rating" value="3.5"
                                                class="mask mask-star-2 mask-half-1 bg-amber-500" aria-label="3.5 star" />
                                            <input type="radio" x-model="rating" name="rating" value="4"
                                                class="mask mask-star-2 mask-half-2 bg-amber-500" aria-label="4 star" />
                                            <input type="radio" x-model="rating" name="rating" value="4.5"
                                                class="mask mask-star-2 mask-half-1 bg-amber-500" aria-label="4.5 star" />
                                            <input type="radio" x-model="rating" name="rating" value="5"
                                                class="mask mask-star-2 mask-half-2 bg-amber-500" aria-label="5 star" />
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1" x-show="rating > 0">
                                            Selected: <span x-text="rating"></span> Star(s)
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2">Your Review</label>
                                        <textarea name="comment" x-model="comment" placeholder="Share your thoughts about this product..."
                                            class="textarea textarea-sm textarea-bordered w-full h-32 resize-none" required></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2">Upload Image (Optional)</label>
                                        <input type="file" name="image" accept="image/*"
                                            x-on:change="image = $event.target.files[0] ? $event.target.files[0].name : null"
                                            class="file-input file-input-sm file-input-bordered" />
                                        <p class="text-xs text-gray-500 mt-1" x-show="image">
                                            Selected: <span x-text="image"></span>
                                        </p>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" x-ref="submitBtn" class="btn btn-primary"
                                        x-bind:disabled="submitting">
                                        Submit Review
                                    </button>
                                </form>
                            </div>



                            <div x-data="productReviews({{ $product['id'] }})" x-init="fetchReviews()" class="mt-10 w-full">
                                <p class="text-lg font-semibold mb-4">Customer Reviews</p>

                                <template x-if="loading && reviews.length === 0">
                                    <p class="mt-3 text-sm text-gray-500 italic">Loading reviews...</p>
                                </template>

                                <template x-if="!loading && reviews.length === 0">
                                    <p class="mt-3 text-gray-500 text-sm italic">No reviews yet.</p>
                                </template>

                                <div class="flex flex-col gap-5 w-full" x-show="reviews.length > 0">
                                    <template x-for="review in reviews" :key="review.id">
                                        <div class="card border border-base-300 bg-base-100 p-5 rounded-box lg:w-[600px]">
                                            <div class="flex items-start gap-4">
                                                <div class="w-10 h-10">
                                                    <img x-show="review.user?.profile" :src="review.user.profile"
                                                        class="border border-base-300 rounded-full w-10 h-10 object-cover">
                                                    <div x-show="!review.user?.profile"
                                                        class="bg-gray-500 text-white rounded-full w-10 h-10 flex items-center justify-center text-lg font-semibold"
                                                        x-text="review.user?.name ? review.user.name[0].toUpperCase() : 'U'">
                                                    </div>
                                                </div>

                                                <div class="flex-1">
                                                    <div class="flex justify-between items-start">
                                                        <div class="flex flex-col gap-2">
                                                            <div
                                                                class="flex flex-col md:flex-row gap-2 items-start md:items-center">
                                                                <h3
                                                                    class="font-semibold text-sm flex flex-col md:flex-row items-start md:items-center gap-2">
                                                                    <span
                                                                        x-text="review.user?.name ?? 'Unknown User'"></span>
                                                                </h3>
                                                                <template x-if="review.is_approved">
                                                                    <span
                                                                        data-tip="This user actually bought {{ $product['name'] }}."
                                                                        class="tooltip cursor-default bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full flex items-center gap-1">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="h-3 w-3" fill="none"
                                                                            viewBox="0 0 24 24" stroke="currentColor"
                                                                            stroke-width="2">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                d="M5 13l4 4L19 7" />
                                                                        </svg> Buyer Verified
                                                                    </span>
                                                                </template>
                                                            </div>

                                                            <div class="mt-1 rating rating-xs rating-half">
                                                                <template x-for="rate in [0.5,1,1.5,2,2.5,3,3.5,4,4.5,5]"
                                                                    :key="rate">
                                                                    <input disabled type="radio"
                                                                        :name="'rating-' + review.id"
                                                                        :value="rate"
                                                                        class="mask mask-star-2 cursor-default bg-amber-500"
                                                                        :class="(rate % 1 !== 0) ? 'mask-half-1' : 'mask-half-2'"
                                                                        :checked="parseFloat(review.rating) === rate"
                                                                        :aria-label="rate + ' star'" />
                                                                </template>
                                                            </div>
                                                            {{-- <div class="mt-1 rating rating-sm rating-half">
                                                            @foreach (['0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5'] as $rate)
                                                                <input disabled type="radio" name="rating-{{ $review['id'] }}"
                                                                    value="{{ $rate }}"
                                                                    class="mask mask-star-2 cursor-default {{ strpos($rate, '.5') !== false ? 'mask-half-1' : 'mask-half-2' }} bg-amber-500"
                                                                    @checked((float) $review['rating'] == (float) $rate)
                                                                    aria-label="{{ $rate }} star" />
                                                            @endforeach
                                                        </div> --}}
                                                        </div>

                                                        <div
                                                            class="flex flex-col md:flex-row md:items-start items-end gap-2">
                                                            <span class="text-xs text-gray-500"
                                                                x-text="dayjs(review.created_at).fromNow()"></span>
                                                            <button
                                                                x-show="review.user && review.user.id == {{ auth()->id() ?? -1 }}"
                                                                type="button"
                                                                class="btn btn-square btn-sm btn-outline btn-error"
                                                                @click="document.getElementById('review_delete_modal_' + review.id).showModal()"><svg
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor" class="size-4">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                                </svg>
                                                            </button>

                                                            <dialog :id="`review_delete_modal_${review.id}`"
                                                                class="modal">
                                                                <div class="modal-box">
                                                                    <form method="dialog">
                                                                        <button
                                                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                                    </form>

                                                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                                                    <p class="text-sm mb-4">Are you sure you want to delete
                                                                        this review by
                                                                        <span class="text-error"
                                                                            x-text="review.user?.name ?? 'Guest'"></span>?
                                                                    </p>

                                                                    <div class="modal-action">
                                                                        <form method="dialog"><button
                                                                                class="btn">Cancel</button></form>

                                                                        <form method="POST"
                                                                            :action="`/shop/review/${review.id}`">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-error">Delete</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </dialog>

                                                        </div>
                                                    </div>

                                                    <p class="text-sm leading-relaxed mt-1 whitespace-pre-line"
                                                        x-text="review.comment"></p>

                                                    <div class="mt-3" x-show="review.image">
                                                        <img :src="review.image"
                                                            class="w-[100px] rounded-lg border border-base-300 mt-3 object-cover"
                                                            @click="document.getElementById(`review_image_detail_${review.id}`).showModal()" />
                                                        <dialog :id="`review_image_detail_${review.id}`" class="modal">
                                                            <div class="modal-box max-h-[80vh]">
                                                                <form method="dialog">
                                                                    <button
                                                                        class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                                </form>
                                                                <p class="text-lg font-semibold py-0"
                                                                    x-text="review.user?.name ?? 'Guest'">
                                                                </p>
                                                                <p class="text-sm mt-3" x-text="review.comment"></p>
                                                                <div class="mt-4 space-y-2">
                                                                    <img :src="review.image" alt="Product Review Image"
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

                                                    <div class="mt-4 flex flex-col gap-2 lg:w-[500px]">
                                                        <template x-for="reply in review.replies" :key="reply.id">
                                                            <div class="flex items-start gap-3">
                                                                <div class="w-8 h-8">
                                                                    
                                                                    <template x-if="reply.user?.profile">
                                                                        <img :src="reply.user.profile"
                                                                            class="w-8 h-8 rounded-full border border-base-300 object-cover">
                                                                    </template>
                                                                    <template x-if="!reply.user?.profile">
                                                                        <div class="bg-gray-400 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-semibold"
                                                                            x-text="reply.user?.name ? reply.user.name[0].toUpperCase() : 'U'">
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                                <div
                                                                    class="flex-1 bg-base-200 border border-base-300 rounded-lg px-3 py-2">
                                                                    <div class="flex justify-between items-center mb-0.5">
                                                                        <span class="font-medium text-xs"
                                                                            x-text="reply.user?.name ?? 'Unknown User'"></span>
                                                                        <div class="flex items-start gap-2">
                                                                            <span class="text-[11px] text-gray-500"
                                                                                x-text="dayjs(reply.created_at).fromNow()"></span>
                                                                            <button
                                                                                x-show="reply.user && reply.user.id == {{ auth()->id() ?? -1 }}"
                                                                                type="button"
                                                                                class="btn btn-square btn-xs btn-outline btn-error"
                                                                                @click="document.getElementById('reply_delete_modal_' + reply.id).showModal()"><svg
                                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="1.5"
                                                                                    stroke="currentColor" class="size-3">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                                                </svg>
                                                                            </button>

                                                                            <dialog :id="`reply_delete_modal_${reply.id}`"
                                                                                class="modal">
                                                                                <div class="modal-box">
                                                                                    <form method="dialog">
                                                                                        <button
                                                                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                                                    </form>

                                                                                    <p class="text-lg font-semibold">
                                                                                        Confirm
                                                                                        Delete</p>
                                                                                    <p class="text-sm mb-4">Are you sure
                                                                                        you
                                                                                        want to delete
                                                                                        this reply by
                                                                                        <span class="text-error"
                                                                                            x-text="reply.user?.name ?? 'Guest'"></span>
                                                                                        ?
                                                                                    </p>

                                                                                    <div class="modal-action">
                                                                                        <form method="dialog"><button
                                                                                                class="btn">Cancel</button>
                                                                                        </form>

                                                                                        <form method="POST"
                                                                                            :action="`/shop/review/${review.id}/reply/${reply.id}`">
                                                                                            @csrf
                                                                                            @method('DELETE')
                                                                                            <button type="submit"
                                                                                                class="btn btn-error">Delete</button>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </dialog>

                                                                        </div>
                                                                    </div>
                                                                    <p class="text-sm" x-text="reply.reply"></p>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        @auth
                                                            <form method="POST" :action="`/shop/review/${review.id}/reply`"
                                                                class="flex items-center gap-2 mt-2">
                                                                @csrf
                                                                <input type="hidden" name="review_id"
                                                                    :value="review.id">
                                                                <input name="reply" type="text"
                                                                    placeholder="Write a reply..."
                                                                    class="input input-sm flex-1 text-sm" required>
                                                                <button type="submit" class="btn btn-sm">Reply</button>
                                                            </form>
                                                        @else
                                                            <p class="text-xs text-gray-500 italic">Login to reply.</p>
                                                        @endauth

                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </template>
                                </div>

                                <div class="flex mt-6">
                                    <button x-show="!finished" @click="loadMore" :disabled="loading"
                                        class="btn btn-ghost btn-sm">
                                        <template x-if="!loading">
                                            <span>See More Reviews</span>
                                        </template>
                                        <template x-if="loading">
                                            <span class="loading loading-spinner text-primary loading-xs mr-2"></span>
                                        </template>
                                    </button>
                                </div>

                                <template x-if="finished && reviews.length > 0">
                                    <p class="text-gray-500 text-xs mt-3">All Reviews Loaded.</p>
                                </template>



                            </div>
                        </div>

                    </div>
                @endif


            </div>


            @if (!empty($product['cross_sell_products']))
                <div class="mt-8">
                    <h2 class="font-semibold mb-3">You May Also Like</h2>
                    <div x-data="scrollController()" class="relative">
                        <div x-ref="scrollContainer"
                            class="flex gap-2 justify-start lg:gap-4 overflow-auto hidden-scrollbar py-2">
                            @foreach ($product['cross_sell_products'] as $cross)
                                <a href="{{ url('/shop/' . $cross['slug']) }}"
                                    class="w-[140px] md:w-[160px] grow-0 shrink-0 bg-base-100 border border-base-300 shadow-sm rounded-lg overflow-hidden hover:shadow-md transition flex flex-col">
                                    <img src="{{ $cross['image'] ?? asset('assets/images/computer_accessories.png') }}"
                                        alt="{{ $cross['name'] }}" class="w-full h-24 lg:h-32 p-2 object-contain">
                                    <div class="p-2 flex flex-col flex-1 justify-between">
                                        <p class="font-semibold text-sm line-clamp-1">{{ $cross['name'] }}</p>
                                        <p class="text-xs text-gray-500 line-clamp-2 mt-1">
                                            {{ $cross['short_description'] ?? '' }}</p>
                                        <div class="mt-2 flex items-center gap-1 text-sm">
                                            @if ($cross['sale_price'])
                                                <span
                                                    class="text-gray-400 line-through">{{ number_format($cross['regular_price'], 2) }}</span>
                                                <span
                                                    class="text-primary">{{ number_format($cross['sale_price'], 2) }} {{ $site_currency }}</span>
                                            @else
                                                <span>{{ number_format($cross['regular_price'], 2) }} {{ $site_currency }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        {{-- <div class="absolute flex justify-between w-full transform -translate-y-1/2 top-1/2">
                        <button class="btn btn-circle" @click="prev()">❮</button>
                        <button class="btn btn-circle" @click="next()">❯</button>
                    </div> --}}
                        <div class="mt-3 w-full flex justify-end gap-3">
                            <button class="btn btn-circle" @click="prev()">❮</button>
                            <button class="btn btn-circle" @click="next()">❯</button>
                        </div>
                    </div>

                </div>
            @endif


            @if (!empty($product['up_sell_products']))
                <div class="mt-8">
                    <h2 class="font-semibold mb-3">Similar and Alternatives</h2>
                    <div x-data="scrollController()" class="relative">
                        <div x-ref="scrollContainer"
                            class="flex gap-2 justify-start lg:gap-4 overflow-auto hidden-scrollbar py-2">
                            @foreach ($product['up_sell_products'] as $up)
                                <a href="{{ url('/shop/' . $up['slug']) }}"
                                    class="w-[140px] md:w-[160px] grow-0 shrink-0 bg-base-100 border border-base-300 shadow-sm rounded-lg overflow-hidden hover:shadow-md transition flex flex-col">
                                    <img src="{{ $up['image'] ?? asset('assets/images/computer_accessories.png') }}"
                                        alt="{{ $up['name'] }}" class="w-full h-24 lg:h-32 p-2 object-contain">
                                    <div class="p-2 flex flex-col flex-1 justify-between">
                                        <p class="font-semibold text-sm line-clamp-1">{{ $up['name'] }}</p>
                                        <p class="text-xs text-gray-500 line-clamp-2 mt-1">
                                            {{ $up['short_description'] ?? '' }}</p>
                                        <div class="mt-2 flex items-center gap-1 text-sm">
                                            @if ($up['sale_price'])
                                                <span
                                                    class="text-gray-400 line-through">{{ number_format($up['regular_price'], 2) }}</span>
                                                <span
                                                    class="text-primary">{{ number_format($up['sale_price'], 2) }} {{ $site_currency }}</span>
                                            @else
                                                <span>{{ number_format($up['regular_price'], 2) }} {{ $site_currency }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-3 w-full flex justify-end gap-3">
                            <button class="btn btn-circle" @click="prev()">❮</button>
                            <button class="btn btn-circle" @click="next()">❯</button>
                        </div>
                    </div>

                </div>
            @endif

            <div class="w-full h-[50px]"></div>

        </div>

    </div>

    @include('components.web_footer')

@endsection

@push('script')
    <script>
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

        function scrollController() {
            return {
                scrollAmount: 200,
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
                }
            }
        }

        function productVariantData() {
            return {
                product: @json($product),
                product_id: {{ $product['id'] }},
                productType: @json($product['product_type'] ?? 'simple'),
                selectors: @json($product['product_variants_selectors']),
                variants: @json($product['product_variants']),
                selectedValues: {},
                variantProduct: @json($variant ?? null),
                loadingStock: false,
                stockError: '',

                addingToCart: false,

                init() {
                    Object.keys(this.selectors).forEach(key => {
                        this.selectedValues[key] = this.selectors[key][0];
                    });

                    if (!this.variantProduct) {
                        this.fetchVariantStock();
                    }

                    Object.keys(this.selectors).forEach(key => {
                        this.$watch(`selectedValues.${key}`, () => {
                            this.fetchVariantStock();
                        });
                    });
                },

                async addItemToCart() {
                    if (this.addingToCart) return;
                    this.addingToCart = true;


                    const added = await this.$store.cart.addItem({
                        product_id: this.product_id,
                        variant_id: this.variantProduct?.id ?? null,
                        variant_combination: this.variantProduct?.combination ?? null,
                        quantity: 1,
                    });

                    this.addingToCart = false;
                },

                async fetchVariantStock() {
                    try {
                        if (this.loadingStock) return;
                        this.loadingStock = true;
                        console.log('Fetching Variant Stock...');

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

                        this.variantProduct = response.data.data ?? null;
                        this.stockError = '';
                    } catch (error) {
                        this.variantProduct = null;
                        this.stockError = error.response?.data?.message ?? 'Failed to fetch variant stock.';
                    } finally {
                        this.loadingStock = false;
                    }
                }
            };
        }

        function productReviews(productId) {
            return {
                reviews: [],
                pagination: {},
                loading: false,
                finished: false,

                async fetchReviews(page = 1, append = false) {
                    if (this.loading || this.finished) return;
                    this.loading = true;
                    try {
                        const res = await axios.get("{{ route('shop.product_id.reviews.get', $product['id']) }}", {
                            params: {
                                page
                            },
                        });
                        const data = res.data;
                        if (append) {
                            this.reviews.push(...data.data);
                        } else {
                            this.reviews = data.data;
                        }
                        this.pagination = data;

                        this.finished = data.current_page >= data.last_page;
                    } catch (err) {
                        console.error('Error loading reviews:', err);
                        this.reviews = [];
                    } finally {
                        this.loading = false;
                    }
                },

                loadMore() {
                    if (this.pagination.next_page_url) {
                        this.fetchReviews(this.pagination.current_page + 1, true);
                    }
                },
            };
        }
    </script>
@endpush
