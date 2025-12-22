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
                        <a href="{{ route('admin.dashboard.order.payment.get') }}" class="btn btn-xs btn-ghost">
                            Payments
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.dashboard.order.payment.id.get', ['id' => $payment['id']]) }}"
                            class="btn btn-xs btn-ghost">
                            Payment #{{ $payment['id'] }}
                        </a>
                    </li>
                </ul>

            </div>
        </div>


        <p class="text-lg font-semibold mt-5 mb-3">Payment Detail</p>

        <div class="tabs tabs-box bg-base-100 shadow-none">
            <input type="radio" name="active_tab" class="tab" aria-label="General" checked="checked" />
            <div class="tab-content">
                <div class="w-full flex flex-col items-start gap-3">
                    <div class="w-full border border-base-300 rounded-box p-3 mt-3">
                        <p class="font-semibold">Actions</p>
                        <div class="w-full flex flex-wrap gap-3 mt-3">
                            @if ($payment['order_id'])
                                <a href="{{ route('admin.dashboard.order.id.get', ['id' => $payment['order_id']]) }}"
                                    class="btn btn-primary">
                                    See Order</a>
                            @endif
                            @if ($payment['invoice_id'])
                                <a href="{{ route('admin.dashboard.order.invoice.id.get', ['id' => $payment['invoice_id']]) }}"
                                    class="btn btn-primary">
                                    See Invoice</a>
                            @endif
                        </div>
                    </div>

                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">Payment Information</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-sm">Payment ID</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $payment['id'] }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Order ID</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $payment['order_id'] ?? 'No Order Found' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Invoice ID</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $payment['invoice_id'] ?? 'No Invoice Found' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Amount</label>
                                <input type="text" name="currency"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $payment['amount'] }}" required>
                            </div>
                            <div>
                                <label class="text-sm">Created At</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $payment['created_at'] ? \Carbon\Carbon::parse($payment['created_at'])->format('Y-m-d h:i A') : '-' }}"
                                    readonly>
                            </div>
                            <div>
                                <label class="text-sm">Created At</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $payment['updated_at'] ? \Carbon\Carbon::parse($payment['updated_at'])->format('Y-m-d h:i A') : '-' }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="radio" name="active_tab" class="tab" aria-label="Transactions" />
            <div class="tab-content">
                <div class="w-full flex flex-col items-start gap-3 mt-3">
                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">Transaction</p>
                        <div class="overflow-auto lg:overflow-visible">
                            <table class="table table-sm border border-base-300 lg:table-fixed">
                                <thead>
                                    <tr>
                                        <th class="w-10">No</th>
                                        <th>Transaction ID</th>
                                        <th>Payment ID</th>
                                        <th>Invoice Number</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('admin.dashboard.order.transaction.id.get', ['id' => $transaction['id']]) }}"
                                                    class="cursor-pointer hover:underline">
                                                    Transaction
                                                    #{{ $transaction['id'] }}
                                                </a>
                                            </td>
                                            <td>
                                                @if ($transaction['payment_id'])
                                                    <a href="{{ route('admin.dashboard.order.payment.id.get', ['id' => $transaction['payment_id']]) }}"
                                                        class="cursor-pointer hover:underline">
                                                        Payment
                                                        #{{ $transaction['payment_id'] }}
                                                    </a>
                                                @else
                                                    No Payment Found
                                                @endif
                                            </td>
                                            <td>
                                                @if ($transaction['invoice_id'])
                                                    <a href="{{ route('admin.dashboard.order.invoice.id.get', ['id' => $transaction['invoice_id']]) }}"
                                                        class="cursor-pointer hover:underline">
                                                        {{ $transaction['invoice']['invoice_number'] ?? 'No Invoice Found' }}
                                                    </a>
                                                @else
                                                    No Invoice Found
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $color = match ($transaction['status']) {
                                                        'succeeded' => 'badge-success',
                                                        'cancelled' => 'badge-error',
                                                        'refunded' => 'badge-warning',
                                                        default => 'badge-ghost',
                                                    };
                                                @endphp
                                                <div
                                                    class="badge {{ $color }} border border-base-300 text-sm capitalize">
                                                    {{ $transaction['status'] }}
                                                </div>
                                            </td>
                                            <td>{{ number_format($transaction['amount'], 2) }} {{ $site_currency }}
                                            </td>
                                            <td>{{ $transaction['created_at'] ? \Carbon\Carbon::parse($transaction['created_at'])->format('Y-m-d h:i A') : '-' }}
                                            </td>
                                            <td>
                                                <div tabindex="0" role="button" class="dropdown dropdown-left">
                                                    <div class="btn btn-square btn-sm btn-ghost">
                                                        <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                                    </div>
                                                    <ul tabindex="0"
                                                        class="menu dropdown-content bg-base-100 border border-base-300 w-42 rounded-box p-1 shadow-sm">
                                                        <li>
                                                            <button type="button" class="text-error"
                                                                onclick="document.getElementById('delete_transaction_modal_{{ $transaction['id'] }}').showModal()">
                                                                Delete
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>

                                            {{-- delete invoice --}}
                                            <dialog id="delete_transaction_modal_{{ $transaction['id'] }}" class="modal">
                                                <div class="modal-box">
                                                    <form method="dialog">
                                                        <button
                                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                    </form>
                                                    <p class="text-lg font-semibold py-0">Confirm Delete
                                                    </p>

                                                    <p class="py-2 mb-0 text-sm">
                                                        Are you sure you want to delete
                                                        <span class="italic text-error">Transaction
                                                            #{{ $transaction['id'] }}</span>
                                                        ?
                                                    </p>
                                                    <div class="modal-action mt-0">
                                                        <form method="dialog">
                                                            <button class="btn">Cancel</button>
                                                        </form>
                                                        <form method="POST"
                                                            action="{{ route('admin.dashboard.order.transaction.id.delete', ['id' => $transaction['id']]) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-error">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </dialog>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
