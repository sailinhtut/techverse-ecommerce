@extends('layouts.user.user_dashboard')

@section('user_dashboard_content')
    <div class="p-3 lg:p-5">
        <p class="lg:text-lg font-semibold ">Order History</p>

        <div class="card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="w-[150px]">Order Number</th>
                            <th class="w-[150px]">Date</th>
                            <th class="w-[150px]">Status</th>
                            <th class="w-[150px]">Total</th>
                            <th class="w-[150px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    {{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}.
                                </td>

                                <td>
                                    <div onclick="document.getElementById('detail_modal_{{ $order['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">
                                        {{ $order['order_number'] }}
                                    </div>
                                </td>

                                <td>{{ $order['created_at']->format('Y-m-d H:i') }}</td>

                                <td>
                                    @php
                                        $color = match ($order['status']) {
                                            'pending' => 'badge-warning',
                                            'processing' => 'badge-info',
                                            'completed' => 'badge-success',
                                            'cancelled' => 'badge-error',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div class="badge {{ $color }} badge-outline capitalize">
                                        {{ $order['status'] }}
                                    </div>
                                </td>

                                <td>${{ number_format($order['grand_total'], 2) }}</td>

                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button
                                                    onclick="document.getElementById('detail_modal_{{ $order['id'] }}').showModal()">
                                                    View Details
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('deleteModal{{ $order['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    <dialog id="detail_modal_{{ $order['id'] }}" class="modal">
                                        <div class="modal-box max-h-[85vh] max-w-2xl">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>

                                            <h3 class="text-lg font-semibold text-center mb-3">
                                                Order #{{ $order['order_number'] }}
                                            </h3>

                                            <div class="text-sm space-y-2">
                                                <p><strong>ID:</strong> {{ $order['id'] }}</p>
                                                <p><strong>Date:</strong> {{ $order['created_at']->format('Y-m-d H:i') }}
                                                </p>
                                                <p><strong>Status:</strong>
                                                    <span class="badge {{ $color }} badge-outline">
                                                        {{ ucfirst($order['status']) }}
                                                    </span>
                                                </p>
                                                <p><strong>Subtotal:</strong> ${{ number_format($order['subtotal'], 2) }}
                                                </p>
                                                <p><strong>Discount:</strong>
                                                    -${{ number_format($order['discount_total'], 2) }}</p>
                                                <p><strong>Tax:</strong> +${{ number_format($order['tax_total'], 2) }}</p>
                                                <p><strong>Shipping:</strong>
                                                    +${{ number_format($order['shipping_total'], 2) }}</p>
                                                <p><strong>Grand Total:</strong>
                                                    <span
                                                        class="font-semibold">${{ number_format($order['grand_total'], 2) }}</span>
                                                </p>

                                                {{-- Divider --}}
                                                <div class="divider my-3"></div>

                                                {{-- Products Table --}}
                                                <p class="font-semibold">Ordered Products</p>
                                                <div class="overflow-x-auto">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Qty</th>
                                                                <th>Unit Price</th>
                                                                <th>Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($order['products'] ?? [] as $item)
                                                                <tr>
                                                                    <td>{{ $item['name'] }}</td>
                                                                    <td>{{ $item['quantity'] }}</td>
                                                                    <td>${{ number_format($item['unit_price'], 2) }}</td>
                                                                    <td>${{ number_format($item['subtotal'], 2) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- Divider --}}
                                                <div class="divider my-3"></div>

                                                {{-- Shipping and Billing --}}
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="bg-base-200 rounded-box p-3">
                                                        <p class="font-semibold mb-1">Shipping Address</p>
                                                        @php $s = $order['shipping_address'] ?? []; @endphp
                                                        <p>{{ $s['recipient_name'] ?? '-' }}</p>
                                                        <p>{{ $s['street_address'] ?? '-' }}</p>
                                                        <p>{{ $s['city'] ?? '' }} {{ $s['state'] ?? '' }}</p>
                                                        <p>{{ $s['postal_code'] ?? '' }}</p>
                                                        <p>{{ $s['country'] ?? '' }}</p>
                                                        <p class="text-xs text-gray-500 mt-1">{{ $s['phone'] ?? '' }}</p>
                                                    </div>

                                                    <div class="bg-base-200 rounded-box p-3">
                                                        <p class="font-semibold mb-1">Billing Address</p>
                                                        @php $b = $order['billing_address'] ?? []; @endphp
                                                        <p>{{ $b['recipient_name'] ?? '-' }}</p>
                                                        <p>{{ $b['street_address'] ?? '-' }}</p>
                                                        <p>{{ $b['city'] ?? '' }} {{ $b['state'] ?? '' }}</p>
                                                        <p>{{ $b['postal_code'] ?? '' }}</p>
                                                        <p>{{ $b['country'] ?? '' }}</p>
                                                        <p class="text-xs text-gray-500 mt-1">{{ $b['phone'] ?? '' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-action mt-6">
                                                <form method="dialog">
                                                    <button class="btn btn-primary w-full">Close</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>

                                    {{-- DELETE DIALOG --}}
                                    <dialog id="deleteModal{{ $order['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to delete
                                                <span class="italic text-error">Order #{{ $order['order_number'] }}</span>
                                                ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn">Cancel</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('order_history.delete', ['id' => $order['id']]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-error">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $orders->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $orders->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $orders->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($orders->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $orders->url(1) }}"
                            class="join-item btn btn-sm {{ $orders->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $orders->currentPage() - 1);
                            $end = min($orders->lastPage() - 1, $orders->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $orders->url($i) }}"
                                class="join-item btn btn-sm {{ $orders->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($orders->lastPage() > 1)
                            <a href="{{ $orders->url($orders->lastPage()) }}"
                                class="join-item btn btn-sm {{ $orders->currentPage() === $orders->lastPage() ? 'btn-active' : '' }}">
                                {{ $orders->lastPage() }}
                            </a>
                        @endif

                        @if ($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
