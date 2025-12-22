@php
    $site_currency = getParsedTemplate('site_currency');
@endphp
@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-3 lg:p-5 min-h-screen">

        <div class="mb-4">
            <button onclick="history.back()" class="btn btn-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </button>
        </div>

        <div class="w-fit max-w-full text-sm bg-base-300 rounded px-2 overflow-x-auto ">
            <div class="breadcrumbs text-sm my-0 py-1">
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard.order.transaction.get') }}" class="btn btn-xs btn-ghost">
                            Transactions
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.dashboard.order.transaction.id.get', ['id' => $transaction['id']]) }}"
                            class="btn btn-xs btn-ghost">
                            Transaction #{{ $transaction['id'] }}
                        </a>
                    </li>
                </ul>

            </div>
        </div>


        <p class="text-lg font-semibold mt-5 mb-3">Transaction Detail</p>

        <div class="tabs tabs-box bg-base-100 shadow-none">
            <input type="radio" name="active_tab" class="tab" aria-label="General" checked="checked" />
            <div class="tab-content">
                <div class="w-full flex flex-col items-start gap-3">
                    <div class="w-full border border-base-300 rounded-box p-3 mt-3">
                        <p class="font-semibold">Actions</p>
                        <div class="w-full flex flex-wrap gap-3 mt-3">
                            @if ($transaction['order_id'])
                                <a href="{{ route('admin.dashboard.order.id.get', ['id' => $transaction['order_id']]) }}"
                                    class="btn btn-primary">
                                    See Order</a>
                            @endif
                            @if ($transaction['invoice_id'])
                                <a href="{{ route('admin.dashboard.order.invoice.id.get', ['id' => $transaction['invoice_id']]) }}"
                                    class="btn btn-primary">
                                    See Invoice</a>
                            @endif
                            @if ($transaction['payment_id'])
                                <a href="{{ route('admin.dashboard.order.payment.id.get', ['id' => $transaction['payment_id']]) }}"
                                    class="btn btn-primary">
                                    See Payment</a>
                            @endif
                        </div>
                    </div>

                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">Transaction Information</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-sm">Transaction ID</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $transaction['id'] }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Order ID</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $transaction['order_id'] ?? 'No Order Found' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Invoice ID</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $transaction['invoice_id'] ?? 'No Invoice Found' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Payment ID</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $transaction['payment_id'] ?? 'No Payment Found' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">User ID</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $transaction['user_id'] ?? 'No User Found' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Amount</label>
                                <input type="text" name="currency"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $transaction['amount'] }}" required>
                            </div>

                            <div>
                                @php
                                    $color = match ($transaction['status']) {
                                        'succeeded' => 'bg-success',
                                        'cancelled' => 'bg-error',
                                        'refunded' => 'bg-warning',
                                        default => 'bg-ghost',
                                    };
                                @endphp
                                <label class="text-sm">Status</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none {{ $color }}"
                                    value="{{ ucfirst($transaction['status']) }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Created At</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $transaction['created_at'] ? \Carbon\Carbon::parse($transaction['created_at'])->format('Y-m-d h:i A') : '-' }}"
                                    readonly>
                            </div>
                            <div>
                                <label class="text-sm">Created At</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $transaction['updated_at'] ? \Carbon\Carbon::parse($transaction['updated_at'])->format('Y-m-d h:i A') : '-' }}"
                                    readonly>
                            </div>

                        </div>
                    </div>


                </div>
            </div>



        </div>

    </div>
@endsection
