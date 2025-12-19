@php
    $site_currency = getParsedTemplate('site_currency');
@endphp

@extends('layouts.user.user_dashboard')

@section('user_dashboard_content')
    <div class="p-3 lg:p-5">
        <p class="lg:text-lg font-semibold">Payment Invoice</p>
        <div class="mt-3 card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="w-[150px]">Invoice Number</th>
                            <th class="w-[150px]">Order Number</th>
                            <th class="w-[150px]">Status</th>
                            <th class="w-[150px]">Total</th>
                            <th class="w-[150px]">Download</th>
                            <th class="w-[150px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td>{{ $loop->iteration + ($invoices->currentPage() - 1) * $invoices->perPage() }}.</td>
                                {{-- <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $invoice['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">
                                        {{ $invoice['invoice_number'] }}</p>
                                </td> --}}

                                <td>
                                    <a href="{{ route('payment.id.get', $invoice['id']) }}"
                                        class="cursor-pointer hover:underline">
                                        {{ $invoice['invoice_number'] }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('order_detail.id.get', $invoice['order_id']) }}"
                                        class="cursor-pointer hover:underline">
                                        {{ $invoice['order']['order_number'] }}
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $color = match ($invoice['status']) {
                                            'unpaid' => 'badge-error',
                                            'paid' => 'badge-success',
                                            'refunded' => 'badge-warning',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div class="badge {{ $color }} badge-outline capitalize">
                                        {{ $invoice['status'] }}
                                    </div>
                                </td>
                                <td>{{ number_format($invoice['grand_total'], 2) }} {{ $site_currency }}</td>
                                <td>
                                    <form
                                        action="{{ route('order.id.invoice.id.download.get', [
                                            'order_id' => $invoice['order_id'],
                                            'invoice_id' => $invoice['id'],
                                        ]) }}"
                                        method="GET" x-data="{ submitting: false }" @submit="submitting=true">
                                        <button type="submit" class="btn btn-sm w-fit" :disabled="submitting">
                                            <span x-show="submitting"
                                                class="loading loading-spinner loading-sm mr-2"></span>
                                            <span x-show="submitting">Downloading</span>
                                            <span x-show="!submitting">
                                                Download Invoice
                                            </span>
                                        </button>
                                    </form>
                                    {{-- <a href="{{ route('order.id.invoice.id.download.get', [
                                        'order_id' => $invoice['order_id'],
                                        'invoice_id' => $invoice['id'],
                                    ]) }}"
                                        class="btn btn-sm">
                                        Download Invoice
                                    </a> --}}
                                </td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button
                                                    onclick="document.getElementById('detail_modal_{{ $invoice['id'] }}').showModal()">
                                                    View Details
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $invoice['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            {{-- Invoice Detail Modal --}}
                            <dialog id="detail_modal_{{ $invoice['id'] }}" class="modal">
                                <div class="modal-box max-h-[85vh] max-w-2xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">
                                        Invoice #{{ $invoice['invoice_number'] }}
                                    </h3>

                                    <div class="text-sm space-y-2">
                                        <p><strong>ID:</strong> {{ $invoice['id'] }}</p>
                                        <p><strong>Order:</strong> {{ $invoice['order']['order_number'] ?? '-' }}</p>
                                        <p><strong>Status:</strong>
                                            <span
                                                class="badge {{ $color }} badge-outline">{{ ucfirst($invoice['status']) }}</span>
                                        </p>
                                        <p><strong>Subtotal:</strong>{{ number_format($invoice['subtotal'], 2) }} {{ $site_currency }}</p>
                                        <p><strong>Discount:</strong> -{{ number_format($invoice['discount_total'], 2) }} {{ $site_currency }}
                                        </p>
                                        <p><strong>Tax:</strong> +{{ number_format($invoice['tax_total'], 2) }} {{ $site_currency }}</p>
                                        <p><strong>Shipping:</strong> +{{ number_format($invoice['shipping_total'], 2) }} {{ $site_currency }}
                                        </p>
                                        <p><strong>Grand Total:</strong> <span
                                                class="font-semibold">{{ number_format($invoice['grand_total'], 2) }} {{ $site_currency }}</span>
                                        </p>
                                        <p><strong>Issued At:</strong> {{ $invoice['issued_at'] }}</p>
                                        <p><strong>Due At:</strong> {{ $invoice['due_at'] ?? '-' }}</p>
                                    </div>

                                    <div class="modal-action mt-6">
                                        <form method="dialog">
                                            <button class="btn">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>

                            <dialog id="delete_modal_{{ $invoice['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                    <p class="py-2 mb-0 text-sm">
                                        Are you sure you want to delete
                                        <span class="italic text-error">Invoice
                                            #{{ $invoice['id'] }}</span>
                                        ?
                                    </p>
                                    <div class="modal-action mt-0">
                                        <form method="dialog">
                                            <button class="btn">Cancel</button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.payment.invoice.id.delete', ['id' => $invoice['id']]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="flex justify-between items-center py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $invoices->firstItem() }}</span> –
                        <span class="font-semibold">{{ $invoices->lastItem() }}</span> of
                        <span class="font-semibold">{{ $invoices->total() }}</span> results
                    </div>
                    <div class="join">
                        @if ($invoices->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $invoices->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif
                        @for ($i = 1; $i <= $invoices->lastPage(); $i++)
                            <a href="{{ $invoices->url($i) }}"
                                class="join-item btn btn-sm {{ $invoices->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if ($invoices->hasMorePages())
                            <a href="{{ $invoices->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
