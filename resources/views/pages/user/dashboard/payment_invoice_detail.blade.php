@php
    $site_currency = getParsedTemplate('site_currency');
@endphp
@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')

    <div class="max-w-4xl mx-auto px-4 py-6 lg:py-10">

        {{-- 🔙 Back --}}
        <button onclick="history.back()" class="btn btn-ghost btn-sm mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </button>

        {{-- 🧾 Invoice Header --}}
        <div class="flex flex-col lg:flex-row justify-between lg:items-start pb-4">
            <div>
                <h1 class="text-xl font-semibold">Invoice #{{ $invoice['invoice_number'] }}</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Issued on: {{ $invoice['created_at']->format('d/m/Y') }}
                </p>
            </div>

            <div class="mt-3 lg:mt-0">
                @php
                    $statusColor = match ($invoice['status']) {
                        'unpaid' => 'bg-red-100 text-red-700',
                        'paid' => 'bg-green-100 text-green-700',
                        'refunded' => 'bg-yellow-100 text-yellow-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                    {{ ucfirst($invoice['status']) }}
                </span>
            </div>
        </div>

        <div class="flex flex-row flex-wrap gap-3">
            <a href="{{ route('order_detail.id.get', $invoice['order_id']) }}" class="btn btn-sm w-fit">
                See Order
            </a>

            <form
                action="{{ route('order.id.invoice.id.download.get', [
                    'order_id' => $invoice['order_id'],
                    'invoice_id' => $invoice['id'],
                ]) }}"
                method="GET" x-data="{ submitting: false }" @submit="submitting=true">
                <button type="submit" class="btn btn-sm w-fit" :disabled="submitting">
                    <span x-show="submitting" class="loading loading-spinner loading-sm mr-2"></span>
                    <span x-show="submitting">Downloading</span>
                    <span x-show="!submitting">
                        Download Invoice
                    </span>
                </button>
            </form>
        </div>

        {{-- 🛍 Order Summary / Details --}}
        <div class="mt-5 bg-base-200 rounded-box p-5 mb-8">
            <h2 class="text-lg font-semibold mb-3">Invoice Summary</h2>

            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>{{ number_format($invoice['subtotal'], 2) }} {{ $site_currency }}</span>
                </div>

                @if ($invoice['discount_total'] > 0)
                    <div class="flex justify-between">
                        <span>Discount</span>
                        <span>- {{ number_format($invoice['discount_total'], 2) }} {{ $site_currency }}</span>
                    </div>
                @endif

                <div class="flex justify-between">
                    <span>Tax</span>
                    <span>+ {{ number_format($invoice['tax_total'], 2) }} {{ $site_currency }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Shipping</span>
                    <span>+ {{ number_format($invoice['shipping_total'], 2) }} {{ $site_currency }}</span>
                </div>

                <div class="flex justify-between font-semibold border-t border-base-300 pt-2 mt-2">
                    <span>Total</span>
                    <span class="text-lg">{{ number_format($invoice['grand_total'], 2) }} {{ $site_currency }}</span>
                </div>
            </div>
        </div>


    </div>

    @include('components.web_footer')
@endsection
