@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Order List</p>

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
                                    <a href="{{ route('admin.dashboard.order.id.get', ['id' => $order['id']]) }}"
                                        class="cursor-pointer hover:underline">
                                        {{ $order['order_number'] }}
                                    </a>
                                </td>

                                <td>{{ $order['created_at'] ? \Carbon\Carbon::parse($order['created_at'])->format('Y-m-d H:i') : '-' }}</td>

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
                                                    onclick="document.getElementById('delete_modal_{{ $order['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    <dialog id="detail_modal_{{ $order['id'] }}" class="modal">
                                        <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>

                                            <h3 class="text-lg font-semibold text-center mb-3">
                                                Order #{{ $order['order_number'] }}
                                            </h3>

                                            {{-- Order Basic Info --}}
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                                                <div>
                                                    <label class="text-sm">Order ID</label>
                                                    <input type="text" value="{{ $order['id'] }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Date</label>
                                                    <input type="text"
                                                        value="{{ $order['created_at'] ? \Carbon\Carbon::parse($order['created_at'])->format('Y-m-d H:i') : ''}}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Status</label>
                                                    <input type="text" value="{{ ucfirst($order['status']) }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Subtotal</label>
                                                    <input type="text"
                                                        value="${{ number_format($order['subtotal'], 2) }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Discount</label>
                                                    <input type="text"
                                                        value="- ${{ number_format($order['discount_total'], 2) }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Coupon Code</label>
                                                    <input type="text"
                                                        value="{{ $order['coupon_code'] ?? 'None'}}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Tax</label>
                                                    <input type="text"
                                                        value="+ ${{ number_format($order['tax_total'], 2) }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Shipping</label>
                                                    <input type="text"
                                                        value="+ ${{ number_format($order['shipping_total'], 2) }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Grand Total</label>
                                                    <input type="text"
                                                        value="${{ number_format($order['grand_total'], 2) }}" readonly
                                                        class="input w-full font-semibold cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                            </div>

                                            <div class="divider my-3"></div>

                                            {{-- Ordered Products Table --}}
                                            <p class="font-semibold mb-2">Ordered Products</p>
                                            <div class="overflow-x-auto mb-3">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Qty</th>
                                                            <th>SKU</th>
                                                            <th>Type</th>
                                                            <th>Unit Price</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($order['products'] ?? [] as $item)
                                                            <tr>
                                                                <td>{{ $item['name'] }}</td>
                                                                <td>{{ $item['quantity'] }}</td>
                                                                <td>{{ $item['sku'] }}</td>
                                                                <td>{{ $item['variant_id'] ? 'Variant Product' : 'Simple Product' }}
                                                                </td>
                                                                <td>${{ number_format($item['unit_price'], 2) }}</td>
                                                                <td>${{ number_format($item['subtotal'], 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="divider my-3"></div>

                                            {{-- Shipping & Billing Addresses --}}
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                                {{-- Shipping --}}
                                                <div class="bg-base-200 rounded-box p-3">
                                                    <p class="font-semibold mb-1">Shipping Address</p>
                                                    @php $s = $order['shipping_address'] ?? []; @endphp
                                                    <input type="text" value="{{ $s['recipient_name'] ?? '-' }}"
                                                        readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $s['street_address'] ?? '-' }}"
                                                        readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text"
                                                        value="{{ $s['city'] ?? '' }} {{ $s['state'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $s['postal_code'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $s['country'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $s['phone'] ?? '' }}" readonly
                                                        class="input w-full text-xs cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>

                                                {{-- Billing --}}
                                                <div class="bg-base-200 rounded-box p-3">
                                                    <p class="font-semibold mb-1">Billing Address</p>
                                                    @php $b = $order['billing_address'] ?? []; @endphp
                                                    <input type="text" value="{{ $b['recipient_name'] ?? '-' }}"
                                                        readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $b['street_address'] ?? '-' }}"
                                                        readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text"
                                                        value="{{ $b['city'] ?? '' }} {{ $b['state'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $b['postal_code'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $b['country'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $b['phone'] ?? '' }}" readonly
                                                        class="input w-full text-xs cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                            </div>

                                            <div class="divider my-3"></div>

                                            {{-- Payment Methods --}}
                                            <p class="font-semibold mb-2">Payment Method</p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="text-sm">Name</label>
                                                    <input type="text" value="{{ $order['payment_method']['name'] }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Description</label>
                                                    <input type="text"
                                                        value="{{ $order['payment_method']['description'] ?? '-' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                @if (empty($order['payment_method']))
                                                    <div class="md:col-span-2 text-gray-500 italic">No payment method
                                                        available.</div>
                                                @endif
                                            </div>

                                            {{-- Modal Action --}}
                                            <div class="modal-action mt-6">
                                                <form method="dialog">
                                                    <button class="btn btn-primary w-full">Close</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>



                                    {{-- DELETE DIALOG --}}
                                    <dialog id="delete_modal_{{ $order['id'] }}" class="modal">
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
