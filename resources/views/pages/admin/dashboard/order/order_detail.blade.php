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
                        <a href="{{ route('admin.dashboard.order.get') }}" class="btn btn-xs btn-ghost">
                            Orders
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.dashboard.order.id.get', ['id' => $order['id']]) }}"
                            class="btn btn-xs btn-ghost">
                            {{ $order['order_number'] }}
                        </a>
                    </li>
                </ul>

            </div>
        </div>


        <p class="text-lg font-semibold mt-5 mb-3">Order Detail</p>


        <div class="tabs tabs-box bg-base-100 shadow-none">
            <input type="radio" name="active_tab" class="tab" aria-label="General" checked="checked" />
            <div class="tab-content">
                <div class="w-full flex flex-col items-start gap-3">
                    <div class="w-full border border-base-300 rounded-box p-3 mt-3">
                        <p class="font-semibold">Actions</p>
                        <div class="w-full flex flex-wrap gap-3 mt-3">
                            @if (!$order['stock_consumed'])
                                <button onclick="consume_stock_dialog.showModal()" type="submit" class="btn btn-success">
                                    Consume Order Stock</button>
                            @else
                                <button onclick="refund_stock_dialog.showModal()" type="submit" class="btn btn-error">
                                    Refund Order Stock</button>
                            @endif
                        </div>

                        @if (!$order['stock_consumed'])
                            <dialog id="consume_stock_dialog" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold py-0">Consume Order Stock Quantity</p>

                                    <p class="py-2 mb-0 text-sm">
                                        Are you sure you want to consume
                                        <span class="font-bold">Order #{{ $order['order_number'] }}</span>
                                        ?
                                    </p>
                                    <div class="modal-action mt-0">
                                        <form method="dialog">
                                            <button class="btn">Cancel</button>
                                        </form>
                                        <form
                                            action="{{ route('admin.dashboard.order.id.consume-order-stock.post', ['id' => $order['id']]) }}"
                                            method="POST" x-data="{ submitting: false }" @submit="submitting=true">
                                            @csrf
                                            <button type="submit" class="btn btn-success" :disabled="submitting">
                                                <span x-show="submitting"
                                                    class="loading loading-spinner loading-sm mr-2"></span>
                                                <span x-show="submitting">Updating</span>
                                                <span x-show="!submitting">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                                    </svg>
                                                    Consume Stock
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
                        @else
                            <dialog id="refund_stock_dialog" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold py-0">Refund Order Stock Quantity</p>

                                    <p class="py-2 mb-0 text-sm">
                                        Are you sure you want to refund
                                        <span class="font-bold">Order #{{ $order['order_number'] }}</span>
                                        ?
                                    </p>
                                    <div class="modal-action mt-0">
                                        <form method="dialog">
                                            <button class="btn">Cancel</button>
                                        </form>
                                        <form
                                            action="{{ route('admin.dashboard.order.id.refund-order-stock.post', ['id' => $order['id']]) }}"
                                            method="POST" x-data="{ submitting: false }" @submit="submitting=true">
                                            @csrf
                                            <button type="submit" class="btn btn-error" :disabled="submitting">
                                                <span x-show="submitting"
                                                    class="loading loading-spinner loading-sm mr-2"></span>
                                                <span x-show="submitting">Refunding</span>
                                                <span x-show="!submitting">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                                    </svg>
                                                    Refund Stock
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.dashboard.order.id.post', $order['id']) }}"
                        class="w-full border border-base-300 rounded-box p-5" x-data="{ submitting: false }"
                        @submit="submitting=true">
                        @csrf
                        @method('POST')
                        <p class="font-semibold mb-2">Order Infomation</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-sm">Order ID</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['id'] }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Order Number</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['order_number'] }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Currency</label>
                                <input type="text" name="currency"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $site_currency }}" required>
                            </div>
                            <div>
                                <label class="text-sm">Created At</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['created_at'] }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Status</label>
                                <select name="status" class="select w-full" required>
                                    @foreach (['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded'] as $status)
                                        <option value="{{ $status }}" @selected($order['status'] === $status)>
                                            {{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-5" :disabled="submitting">
                            <span x-show="submitting" class="loading loading-spinner loading-sm mr-2"></span>
                            <span x-show="submitting">Updating Order</span>
                            <span x-show="!submitting">
                                Update Order
                            </span>
                        </button>
                    </form>

                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">Payment Summary</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-sm">Subtotal</label>
                                <input type="number" step="0.01" name="subtotal"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['subtotal'] }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Discount</label>
                                <input type="number" step="0.01" name="discount_total"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['discount_total'] }}" readonly>
                            </div>

                            <div>
                                <label class="text-sm">Coupon Code</label>
                                <input type="text" name="coupon_code"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['coupon_code'] ?? 'None' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Tax</label>
                                <input type="number" step="0.01" name="tax_total"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['tax_total'] }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Shipping</label>
                                <input type="number" step="0.01" name="shipping_total"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['shipping_total'] }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Grand Total</label>
                                <input type="number" step="0.01" name="grand_total"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['grand_total'] }}" readonly>
                            </div>
                        </div>


                    </div>

                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">User Information</p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-sm">Full Name</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['user']['name'] }}" readonly>
                            </div>

                            <div>
                                <label class="text-sm">Email</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['user']['email'] }}" readonly>
                            </div>

                            <div>
                                <label class="text-sm">Role</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['user']['role']['display_name'] ?? 'N/A' }}" readonly>
                            </div>

                            <div>
                                <label class="text-sm">Phone (Primary)</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['user']['phone_one'] ?? '-' }}" readonly>
                            </div>

                            <div>
                                <label class="text-sm">Phone (Secondary)</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['user']['phone_two'] ?? '-' }}" readonly>
                            </div>

                            <div>
                                <label class="text-sm">Date of Birth</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['user']['date_of_birth'] ?? '-' }}" readonly>
                            </div>

                            <div>
                                <label class="text-sm">Email Verified At</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['user']['email_verified_at'] ? \Carbon\Carbon::parse($order['user']['email_verified_at'])->format('Y-m-d h:i A') : 'Not Verified' }}"
                                    readonly>
                            </div>

                            <div>
                                <label class="text-sm">Account Created</label>
                                <input type="text"
                                    class="input w-full focus:outline-none focus:ring-0 focus-border-base-300 cursor-default select-none"
                                    value="{{ $order['user']['created_at'] ? \Carbon\Carbon::parse($order['user']['created_at'])->format('Y-m-d h:i A') : '' }}"
                                    readonly>
                            </div>

                            <div>
                                <label class="text-sm">Permissions</label>
                                <textarea readonly rows="2"
                                    class="textarea w-full focus:outline-none focus:ring-0 focus-border-base-300 cursor-default select-none">{{ implode(', ', $order['user']['role']['permissions']) ?: 'No Permissions' }}
                                </textarea>
                            </div>
                        </div>
                    </div>

                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">Shipping Address</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm">Recipient Name</label>
                                <input type="text" name="shipping_address[recipient_name]"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['shipping_address']['recipient_name'] ?? '' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Phone</label>
                                <input type="text" name="shipping_address[phone]"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['shipping_address']['phone'] ?? '' }}">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-sm">Street Address</label>
                                <input type="text" name="shipping_address[street_address]"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['shipping_address']['street_address'] ?? '' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">City</label>
                                <input type="text" name="shipping_address[city]"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['shipping_address']['city'] ?? '' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">State</label>
                                <input type="text" name="shipping_address[state]"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['shipping_address']['state'] ?? '' }}">
                            </div>
                            <div>
                                <label class="text-sm">Postal Code</label>
                                <input type="text" name="shipping_address[postal_code]"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['shipping_address']['postal_code'] ?? '' }}">
                            </div>
                            <div>
                                <label class="text-sm">Country</label>
                                <input type="text" name="shipping_address[country]"
                                    class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                    value="{{ $order['shipping_address']['country'] ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">Billing Address</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm">Recipient Name</label>
                                <input type="text" name="billing_address[recipient_name]"
                                    class="input input-bordered w-full"
                                    value="{{ $order['billing_address']['recipient_name'] ?? '' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">Phone</label>
                                <input type="text" name="billing_address[phone]" class="input input-bordered w-full"
                                    value="{{ $order['billing_address']['phone'] ?? '' }}">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-sm">Street Address</label>
                                <input type="text" name="billing_address[street_address]"
                                    class="input input-bordered w-full"
                                    value="{{ $order['billing_address']['street_address'] ?? '' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">City</label>
                                <input type="text" name="billing_address[city]" class="input input-bordered w-full"
                                    value="{{ $order['billing_address']['city'] ?? '' }}" readonly>
                            </div>
                            <div>
                                <label class="text-sm">State</label>
                                <input type="text" name="billing_address[state]" class="input input-bordered w-full"
                                    value="{{ $order['billing_address']['state'] ?? '' }}">
                            </div>
                            <div>
                                <label class="text-sm">Postal Code</label>
                                <input type="text" name="billing_address[postal_code]"
                                    class="input input-bordered w-full"
                                    value="{{ $order['billing_address']['postal_code'] ?? '' }}">
                            </div>
                            <div>
                                <label class="text-sm">Country</label>
                                <input type="text" name="billing_address[country]" class="input input-bordered w-full"
                                    value="{{ $order['billing_address']['country'] ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">Payment Method</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm">Name</label>
                                <input type="text" value="{{ $order['payment_method']['name'] ?? '-' }}" readonly
                                    class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                            </div>
                            <div>
                                <label class="text-sm">Description</label>
                                <input type="text" value="{{ $order['payment_method']['description'] ?? '-' }}"
                                    readonly
                                    class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                            </div>

                            @if (empty($order['payment_method']))
                                <div class="md:col-span-2 text-gray-500 italic">No payment method available.</div>
                            @endif
                        </div>


                    </div>

                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">Shipping Method</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm">Name</label>
                                <input type="text" value="{{ $order['shipping_method']['name'] ?? '-' }}" readonly
                                    class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                            </div>
                            <div>
                                <label class="text-sm">Description</label>
                                <input type="text" value="{{ $order['shipping_method']['description'] ?? '-' }}"
                                    readonly
                                    class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                            </div>

                            @if (empty($order['shipping_method']))
                                <div class="md:col-span-2 text-gray-500 italic">No shipping method available.</div>
                            @endif
                        </div>
                    </div>

                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">Ordered Products</p>
                        <div class="overflow-x-auto">
                            <table class="table table-sm border border-base-300">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>SKU</th>
                                        <th>Type</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order['products'] ?? [] as $item)
                                        <tr>
                                            <td>
                                                @if (isset($item['product']['slug']))
                                                    <a href="{{ route('shop.slug.get', ['slug' => $item['product']['slug'], 'variant' => $item['variant_id']]) }}"
                                                        class="cursor-pointer hover:underline">
                                                        {{ $item['name'] }}
                                                    </a>
                                                @else
                                                    <div class="tooltip tooltip-right"
                                                        data-tip="{{ $item['name'] }} is not existed">
                                                        <p class="cursor-pointer hover:underline">
                                                            {{ $item['name'] }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $item['sku'] }}</td>
                                            <td>{{ $item['variant_id'] ? 'Variant Product' : 'Simple Product' }}</td>
                                            <td>{{ $item['quantity'] }}</td>
                                            <td>{{ number_format($item['unit_price'], 2) }} {{ $site_currency }}</td>
                                            <td>{{ number_format($item['subtotal'], 2) }} {{ $site_currency }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <p class="font-semibold mt-10">Critical Actions <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="size-5 inline">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                        </svg>
                    </p>

                    <div class="flex gap-3 mb-20">
                        <button class="btn btn-sm btn-outline btn-error"
                            onclick="document.getElementById('delete_modal_{{ $order['id'] }}').showModal()">
                            Delete Order
                        </button>
                    </div>

                    <dialog id="delete_modal_{{ $order['id'] }}" class="modal">
                        <div class="modal-box">
                            <form method="dialog">
                                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
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
                                    action="{{ route('admin.dashboard.order.id.delete', ['id' => $order['id']]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-error">Delete</button>
                                </form>
                            </div>
                        </div>
                    </dialog>



                </div>
            </div>

            <input type="radio" name="active_tab" class="tab" aria-label="Invoices" />
            <div class="tab-content">
                <div class="w-full flex flex-col items-start gap-3">
                    <div class="w-full border border-base-300 rounded-box p-3 mt-3">
                        <p class="font-semibold">Actions</p>
                        <div class="w-full flex flex-wrap gap-3 mt-3">
                            <button onclick="create_invoice_dialog.showModal()" type="submit" class="btn btn-primary">
                                Create Invoice</button>
                        </div>

                        {{-- create invoice dialog --}}
                        <dialog id="create_invoice_dialog" class="modal">
                            <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                <form method="dialog">
                                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                </form>

                                <h3 class="text-lg font-semibold text-center mb-3">Create Invoice</h3>

                                <form method="POST"
                                    action="{{ route('admin.dashboard.order.id.create-invoice.post', $order['id']) }}"
                                    x-data="{ submitting: false }" @submit="submitting=true">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="flex flex-col gap-1 md:col-span-2">
                                            <label>Order Number</label>
                                            <input type="text" class="input w-full"
                                                value="{{ $order['order_number'] }}" readonly>
                                        </div>

                                        <input type="hidden" name="order_id" value="{{ $order['id'] }}">

                                        <div class="flex flex-col gap-1 md:col-span-2">
                                            <label>Issue Amount (%)</label>
                                            <select name="issue_amount_percentage" class="select w-full">
                                                <option value="10">
                                                    10% Total
                                                </option>
                                                <option value="25">
                                                    25% Total
                                                </option>
                                                <option value="50">
                                                    50% Total
                                                </option>
                                                <option value="75">
                                                    75% Total
                                                </option>
                                                <option value="100" selected>
                                                    100% Total
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-action mt-3">
                                        <button type="submit" class="w-fit btn btn-primary" :disabled="submitting">
                                            <span x-show="submitting"
                                                class="loading loading-spinner loading-sm mr-2"></span>
                                            <span x-show="submitting">Creating</span>
                                            <span x-show="!submitting">
                                                Create Invoice
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </dialog>

                    </div>
                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">Order Invoices</p>
                        <div class="overflow-auto lg:overflow-visible">
                            <table class="table table-sm border border-base-300 lg:table-fixed">
                                <thead>
                                    <tr>
                                        <th class="w-10">No</th>
                                        <th>Invoice Number</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('admin.dashboard.order.invoice.id.get', ['id' => $invoice['id']]) }}"
                                                    class="cursor-pointer hover:underline">
                                                    {{ $invoice['invoice_number'] }}
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
                                                <div
                                                    class="badge {{ $color }} border border-base-300 text-sm capitalize">
                                                    {{ $invoice['status'] }}
                                                </div>
                                            </td>
                                            <td>{{ number_format($invoice['grand_total'], 2) }} {{ $site_currency }}</td>
                                            <td>{{ $invoice['created_at'] ? \Carbon\Carbon::parse($invoice['created_at'])->format('Y-m-d h:i A') : '-' }}
                                            </td>
                                            <td>
                                                <div tabindex="0" role="button" class="dropdown dropdown-left">
                                                    <div class="btn btn-square btn-sm btn-ghost">
                                                        <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                                    </div>
                                                    <ul tabindex="0"
                                                        class="menu dropdown-content bg-base-100 border border-base-300 w-42 rounded-box p-1 shadow-sm">
                                                        <li>
                                                            @if ($invoice['status'] === 'paid')
                                                                <button type="button" class="text-error"
                                                                    onclick="document.getElementById('cancel_payment_dialog_{{ $invoice['id'] }}').showModal()">
                                                                    Cancel Payment
                                                                </button>
                                                                <button type="button" class="text-error"
                                                                    onclick="document.getElementById('refund_payment_dialog_{{ $invoice['id'] }}').showModal()">
                                                                    Refund Payment
                                                                </button>
                                                            @elseif($invoice['status'] === 'unpaid')
                                                                <button type="button" class="text-success"
                                                                    onclick="document.getElementById('complete_payment_dialog_{{ $invoice['id'] }}').showModal()">
                                                                    Complete Payment
                                                                </button>
                                                            @else
                                                            @endif
                                                        </li>
                                                        <li>
                                                            <a
                                                                href="{{ route('order.id.invoice.id.download.get', [
                                                                    'order_id' => $invoice['order_id'],
                                                                    'invoice_id' => $invoice['id'],
                                                                ]) }}">
                                                                Download Invoice
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="text-error"
                                                                onclick="document.getElementById('delete_invoice_modal_{{ $invoice['id'] }}').showModal()">
                                                                Delete
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>

                                            {{-- complete payment --}}
                                            <dialog id="complete_payment_dialog_{{ $invoice['id'] }}" class="modal"
                                                x-data="{ submitting: false }" @submit="submitting=true">
                                                <div class="modal-box">
                                                    <form method="dialog">
                                                        <button
                                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                    </form>
                                                    <p class="text-lg font-semibold py-0">Complete Order Payment</p>

                                                    <p class="py-2 mb-0 text-sm">
                                                        Are you sure you want to complete payment for
                                                        <span class="font-bold">Order
                                                            #{{ $invoice['invoice_number'] }}</span>
                                                        ?
                                                    </p>
                                                    <div class="modal-action mt-0">
                                                        <form method="dialog">
                                                            <button class="btn">Cancel</button>
                                                        </form>
                                                        <form
                                                            action="{{ route('admin.dashboard.order.id.complete-invoice-payment.post', ['id' => $order['id']]) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="invoice_id"
                                                                value="{{ $invoice['id'] }}">
                                                            <button type="submit" class="btn btn-success"
                                                                :disabled="submitting">
                                                                <span x-show="submitting"
                                                                    class="loading loading-spinner loading-sm mr-2"></span>
                                                                <span x-show="submitting">Updating</span>
                                                                <span x-show="!submitting">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                                                    </svg>
                                                                    Complete Payment
                                                                </span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </dialog>

                                            {{-- cancel payment --}}
                                            <dialog id="cancel_payment_dialog_{{ $invoice['id'] }}" class="modal"
                                                x-data="{ submitting: false }" @submit="submitting=true">
                                                <div class="modal-box">
                                                    <form method="dialog">
                                                        <button
                                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                    </form>
                                                    <p class="text-lg font-semibold py-0">Cancel Order Payment</p>

                                                    <p class="py-2 mb-0 text-sm">
                                                        Are you sure you want to cancel payment for
                                                        <span class="font-bold">Invoice
                                                            #{{ $invoice['invoice_number'] }}</span>
                                                        ?
                                                    </p>
                                                    <div class="modal-action mt-0">
                                                        <form method="dialog">
                                                            <button class="btn">Cancel</button>
                                                        </form>
                                                        <form
                                                            action="{{ route('admin.dashboard.order.id.cancel-invoice-payment.post', ['id' => $order['id']]) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="invoice_id"
                                                                value="{{ $invoice['id'] }}">
                                                            <button type="submit" class="btn btn-error"
                                                                :disabled="submitting">
                                                                <span x-show="submitting"
                                                                    class="loading loading-spinner loading-sm mr-2"></span>
                                                                <span x-show="submitting">Updating</span>
                                                                <span x-show="!submitting">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                                                    </svg>
                                                                    Cancel Payment
                                                                </span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </dialog>

                                            {{-- refund payment --}}
                                            <dialog id="refund_payment_dialog_{{ $invoice['id'] }}" class="modal"
                                                x-data="{ submitting: false }" @submit="submitting=true">
                                                <div class="modal-box">
                                                    <form method="dialog">
                                                        <button
                                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                    </form>
                                                    <p class="text-lg font-semibold py-0">Refund Order Payment</p>

                                                    <p class="py-2 mb-0 text-sm">
                                                        Are you sure you want to refund payment for
                                                        <span class="font-bold">Invoice
                                                            #{{ $invoice['invoice_number'] }}</span>
                                                        ?
                                                    </p>
                                                    <div class="modal-action mt-0">
                                                        <form method="dialog">
                                                            <button class="btn">Cancel</button>
                                                        </form>
                                                        <form
                                                            action="{{ route('admin.dashboard.order.id.refund-invoice-payment.post', ['id' => $order['id']]) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="invoice_id"
                                                                value="{{ $invoice['id'] }}">
                                                            <button type="submit" class="btn btn-error"
                                                                :disabled="submitting">
                                                                <span x-show="submitting"
                                                                    class="loading loading-spinner loading-sm mr-2"></span>
                                                                <span x-show="submitting">Updating</span>
                                                                <span x-show="!submitting">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                                                    </svg>
                                                                    Refund Payment
                                                                </span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </dialog>

                                            {{-- delete invoice --}}
                                            <dialog id="delete_invoice_modal_{{ $invoice['id'] }}" class="modal">
                                                <div class="modal-box">
                                                    <form method="dialog">
                                                        <button
                                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                    </form>
                                                    <p class="text-lg font-semibold py-0">Confirm Delete
                                                    </p>

                                                    <p class="py-2 mb-0 text-sm">
                                                        Are you sure you want to delete
                                                        <span class="italic text-error">Invoice
                                                            #{{ $invoice['invoice_number'] }}</span>
                                                        ?
                                                    </p>
                                                    <div class="modal-action mt-0">
                                                        <form method="dialog">
                                                            <button class="btn">Cancel</button>
                                                        </form>
                                                        <form method="POST"
                                                            action="{{ route('admin.dashboard.order.invoice.id.delete', ['id' => $invoice['id']]) }}">
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


            <input type="radio" name="active_tab" class="tab" aria-label="Payments" />
            <div class="tab-content">
                <div class="w-full flex flex-col items-start gap-3 mt-3">
                    <div class="w-full border border-base-300 rounded-box p-5">
                        <p class="font-semibold mb-2">Invoice Payments</p>
                        <div class="overflow-auto lg:overflow-visible">
                            <table class="table table-sm border border-base-300 lg:table-fixed">
                                <thead>
                                    <tr>
                                        <th class="w-10">No</th>
                                        <th>Payment ID</th>
                                        <th>Invoice Number</th>
                                        <th>Amount</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('admin.dashboard.order.payment.id.get', ['id' => $payment['id']]) }}"
                                                    class="cursor-pointer hover:underline">
                                                    Payment #{{ $payment['id'] }}
                                                </a>
                                            </td>
                                            <td>
                                                @if ($payment['invoice_id'])
                                                    <a href="{{ route('admin.dashboard.order.invoice.id.get', ['id' => $payment['invoice_id']]) }}"
                                                        class="cursor-pointer hover:underline">
                                                        {{ $payment['invoice']['invoice_number'] ?? 'No Invoice Found' }}
                                                    </a>
                                                @else
                                                    No Payment Found
                                                @endif
                                            </td>
                                            <td>{{ number_format($payment['amount'], 2) }} {{ $site_currency }}
                                            </td>
                                            <td>{{ $payment['created_at'] ? \Carbon\Carbon::parse($payment['created_at'])->format('Y-m-d h:i A') : '-' }}
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
                                                                onclick="document.getElementById('delete_invoice_modal_{{ $payment['id'] }}').showModal()">
                                                                Delete
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>

                                            {{-- delete invoice --}}
                                            <dialog id="delete_payment_modal_{{ $payment['id'] }}" class="modal">
                                                <div class="modal-box">
                                                    <form method="dialog">
                                                        <button
                                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                    </form>
                                                    <p class="text-lg font-semibold py-0">Confirm Delete
                                                    </p>

                                                    <p class="py-2 mb-0 text-sm">
                                                        Are you sure you want to delete
                                                        <span class="italic text-error">Payment
                                                            #{{ $payment['id'] }}</span>
                                                        ?
                                                    </p>
                                                    <div class="modal-action mt-0">
                                                        <form method="dialog">
                                                            <button class="btn">Cancel</button>
                                                        </form>
                                                        <form method="POST"
                                                            action="{{ route('admin.dashboard.order.payment.id.delete', ['id' => $payment['id']]) }}">
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
                                                    No Invoice Found
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
                                            <dialog id="delete_transaction_modal_{{ $transaction['id'] }}"
                                                class="modal">
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
