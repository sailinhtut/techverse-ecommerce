@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')

    <div class="max-w-5xl mx-auto px-4 py-6 lg:py-10" x-data>
        {{-- 🔙 Back --}}
        <button onclick="history.back()" class="btn btn-ghost btn-sm mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Orders
        </button>

        {{-- 🧾 Header --}}
        <div class="flex flex-col lg:flex-row justify-between lg:items-center mb-8 border-b pb-4">
            <div>
                <h1 class="text-box font-semibold">Order #{{ $order['order_number'] }}</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Placed on {{ $order['created_at']->format('F d, Y h:i A') }}
                </p>
            </div>
            <div class="mt-3 lg:mt-0">
                @php
                    $statusColor = match ($order['status']) {
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'processing' => 'bg-blue-100 text-blue-700',
                        'completed' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                    {{ ucfirst($order['status']) }}
                </span>
            </div>
        </div>

        {{-- 🛍️ Ordered Items --}}
        <div class="space-y-4 mb-8">
            @foreach ($order['products'] as $item)
                <div
                    class="flex flex-col sm:flex-row gap-4 p-4 rounded-box border border-base-300 hover:shadow-sm transition">

                    <a href="{{ url('/shop/' . ($item['product']['slug'] ?? '')) }}"
                        class="w-24 h-24 shrink-0 overflow-hidden rounded-box bg-base-200">

                        <img src="{{ $item['product']['image'] ?? asset('assets/images/computer_accessories.png') }}"
                            alt="{{ $item['product']['name'] ?? 'Product' }}" class="w-full h-full object-cover">
                    </a>

                    <div class="flex flex-col justify-between w-full">
                        <div>
                            <div class="flex justify-between items-start gap-2">
                                <a href="{{ url('/shop/' . ($item['product']['slug'] ?? '')) }}"
                                    class="font-medium text-base hover:underline">
                                    {{ $item['product']['name'] ?? 'Unnamed Product' }}
                                </a>
                                <p class="text-sm font-semibold">${{ number_format($item['unit_price'], 2) }}</p>
                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                SKU: {{ $item['sku'] ?? '-' }}
                                @if ($item['sku'])
                                    <span> | {{ $item['sku'] }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="flex justify-between items-center mt-2">
                            <p class="text-sm text-gray-600">Qty: {{ $item['quantity'] }}</p>
                            <p class="font-medium text-sm">
                                Subtotal: ${{ number_format($item['subtotal'], 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 💳 Summary Card --}}
        <div class="bg-base-200 rounded-box p-5 mb-8">
            <h2 class="text-lg font-semibold mb-3">Order Summary</h2>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>${{ number_format($order['subtotal'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Discount</span>
                    <span>- ${{ number_format($order['discount_total'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tax</span>
                    <span>+ ${{ number_format($order['tax_total'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Shipping</span>
                    <span>+ ${{ number_format($order['shipping_total'], 2) }}</span>
                </div>

                <div class="flex justify-between font-semibold border-t border-base-300 pt-2 mt-2">
                    <span>Total</span>
                    <span class="text-lg">${{ number_format($order['grand_total'], 2) }}</span>
                </div>
            </div>

            <div class="mt-3 text-sm text-gray-600">
                <p>Payment: {{ $order['payment_method']['name'] ?? 'N/A' }}</p>
                <p>Shipping: {{ $order['shipping_method']['name'] ?? 'N/A' }}</p>
                @if ($order['coupon_code'])
                    <p>Coupon: <span class="font-medium">{{ $order['coupon_code'] }}</span></p>
                @endif
            </div>
        </div>

        {{-- 🏠 Shipping & Billing --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <div class="bg-base-200 rounded-box p-5">
                <h2 class="font-semibold mb-2">Shipping Address</h2>
                @php $s = $order['shipping_address'] ?? []; @endphp
                <p>{{ $s['recipient_name'] ?? '-' }}</p>
                <p>{{ $s['street_address'] ?? '-' }}</p>
                <p>{{ $s['city'] ?? '' }} {{ $s['state'] ?? '' }} {{ $s['postal_code'] ?? '' }}</p>
                <p>{{ $s['country'] ?? '' }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $s['phone'] ?? '' }}</p>
            </div>

            <div class="bg-base-200 rounded-box p-5">
                <h2 class="font-semibold mb-2">Billing Address</h2>
                @php $b = $order['billing_address'] ?? []; @endphp
                <p>{{ $b['recipient_name'] ?? '-' }}</p>
                <p>{{ $b['street_address'] ?? '-' }}</p>
                <p>{{ $b['city'] ?? '' }} {{ $b['state'] ?? '' }} {{ $b['postal_code'] ?? '' }}</p>
                <p>{{ $b['country'] ?? '' }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $b['phone'] ?? '' }}</p>
            </div>
        </div>

        {{-- ⭐ Review section placeholder (optional next step) --}}
        {{-- You can inject verified-review form here --}}
    </div>

    @include('components.web_footer')
@endsection

@push('script')
    <script>
        document.addEventListener('alpine:init', function() {
            Alpine.store('cart').syncCartItems();
        })
    </script>
@endpush
