@php
    $site_currency = getParsedTemplate('site_currency');
@endphp

@extends('layouts.user.user_dashboard')

@section('user_dashboard_content')
    <div class="p-3 lg:p-5">
        <p class="lg:text-lg font-semibold ">Order History</p>

        <div class="mt-3 card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th>Order Number</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    {{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}.
                                </td>

                                <td>
                                    <a href="{{ route('order_detail.id.get', $order['id']) }}"
                                        class="cursor-pointer hover:underline">
                                        {{ $order['order_number'] }}
                                    </a>
                                </td>



                                <td>
                                    @php
                                        $color = match ($order['status']) {
                                            'pending' => 'badge-warning',
                                            'processing' => 'badge-warning',
                                            'shipped' => 'badge-info',
                                            'delivered' => 'badge-info',
                                            'completed' => 'badge-success',
                                            'refunded' => 'badge-error',
                                            'cancelled' => 'badge-error',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div
                                        class="badge badge-sm {{ $color }} border border-base-300 text-xs capitalize">
                                        {{ $order['status'] }}
                                    </div>
                                </td>
                                <td>{{ number_format($order['grand_total'], 2) }} {{ $site_currency }}</td>
                                <td>{{ $order['created_at'] ? \Carbon\Carbon::parse($order['created_at'])->format('Y-m-d h:i A') : '-' }}
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
                                                    onclick="document.getElementById('detail_modal_{{ $order['id'] }}').showModal()">
                                                    View Details
                                                </button>
                                            </li>
                                            {{-- <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('deleteModal{{ $order['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li> --}}
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

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div class="md:col-span-2">
                                                    <label class="text-sm">Order ID</label>
                                                    <input type="text" class="input w-full border-base-300"
                                                        value="{{ $order['id'] }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Order Number</label>
                                                    <input type="text" class="input w-full border-base-300"
                                                        value="{{ $order['order_number'] }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Subtotal</label>
                                                    <input type="text" class="input w-full border-base-300"
                                                        value="{{ number_format($order['subtotal'], 2) }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Shipping Cost</label>
                                                    <input type="text" class="input w-full border-base-300"
                                                        value="{{ number_format($order['shipping_total'], 2) }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Tax Cost</label>
                                                    <input type="text" class="input w-full border-base-300"
                                                        value="{{ number_format($order['tax_total'], 2) }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Coupon Code</label>
                                                    <input type="text" class="input w-full border-base-300"
                                                        value="{{ $order['coupon_code'] ?? '-' }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Discount Total</label>
                                                    <input type="text" class="input w-full border-base-300"
                                                        value="{{ number_format($order['discount_total'], 2) }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Grand Total</label>
                                                    <input type="text" class="input w-full border-base-300"
                                                        value="{{ number_format($order['grand_total'], 2) }}" readonly>
                                                </div>
                                                <div>
                                                    @php
                                                        $order_color = match ($order['status']) {
                                                            'pending' => 'bg-warning',
                                                            'processing' => 'bg-warning',
                                                            'shipped' => 'bg-info',
                                                            'delivered' => 'bg-info',
                                                            'completed' => 'bg-success',
                                                            'refunded' => 'bg-error',
                                                            'cancelled' => 'bg-error',
                                                            default => 'bg-ghost',
                                                        };
                                                    @endphp
                                                    <label class="text-sm">Order Status</label>
                                                    <input type="text"
                                                        class="input w-full border-base-300 {{ $order_color }}"
                                                        value="{{ ucfirst($order['status']) }}" readonly>
                                                </div>

                                                <div>
                                                    <label class="text-sm">Created At</label>
                                                    <input type="text" class="input w-full border-base-300"
                                                        value="{{ \Carbon\Carbon::parse($order['created_at'])->format('Y-m-d h:i A') }}"
                                                        readonly>
                                                </div>

                                                <div>
                                                    <label class="text-sm">Updated At</label>
                                                    <input type="text" class="input w-full border-base-300"
                                                        value="{{ \Carbon\Carbon::parse($order['updated_at'])->format('Y-m-d h:i A') }}"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <p class="font-semibold">Ordered Products</p>
                                                <div class="overflow-x-auto mt-3">
                                                    <table class="table table-sm border border-base-300">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>SKU</th>
                                                                <th>Type</th>
                                                                <th>Unit Price</th>
                                                                <th>Qty</th>
                                                                <th>Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($order['products'] ?? [] as $item)
                                                                <tr>
                                                                    <td>{{ $item['name'] }}</td>
                                                                    <td>{{ $item['sku'] }}</td>
                                                                    <td>{{ $item['variant_id'] ? 'Variant Product' : 'Simple Product' }}
                                                                    </td>
                                                                    <td>{{ number_format($item['unit_price'], 2) }}
                                                                        {{ $site_currency }}</td>
                                                                    <td>{{ $item['quantity'] }}</td>
                                                                    <td>{{ number_format($item['subtotal'], 2) }}
                                                                        {{ $site_currency }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="modal-action mt-6">
                                                <form method="dialog">
                                                    <button class="btn w-full">Close</button>
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
