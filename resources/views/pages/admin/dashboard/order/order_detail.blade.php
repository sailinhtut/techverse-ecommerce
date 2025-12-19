@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">

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


        <p class="text-lg font-semibold mt-5">Order Detail</p>

        <div class="flex flex-wrap gap-3 mt-2">
            {{-- <button class="btn btn-sm">See {{ $order['order_number'] }} Invoice</button> --}}

            @foreach ($invoices as $invoice)
                <a href="{{ route('admin.dashboard.payment.invoice.id.get', ['id' => $invoice['id']]) }}"
                    class="btn btn-sm w-fit">
                    See {{ $invoice['invoice_number'] }}
                </a>
            @endforeach

            <div class="tooltip" data-tip="Mark Invoice Paid,Create Payment Record,Create Successful Payment Transaction">
                <button onclick="complete_payment_dialog.showModal()" type="submit" class="btn btn-sm text-success"><svg
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                    Complete Payment</button>
            </div>

        </div>

        <form method="POST" action="{{ route('admin.dashboard.order.id.post', $order['id']) }}"
            class="border border-base-300 rounded-box p-5 mt-5">
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
                        value="{{ $order['currency'] }}" required>
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
                        @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'] as $status)
                            <option value="{{ $status }}" @selected($order['status'] === $status)>
                                {{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-5">Update Order</button>
        </form>


        <div class="border border-base-300 rounded-box p-5 mt-5">
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

        <div class="border border-base-300 rounded-box p-5 mt-5">
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




        <div class="border border-base-300 rounded-box p-5 mt-5">
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


        <div class="border border-base-300 rounded-box p-5 mt-5">
            <p class="font-semibold mb-2">Billing Address</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm">Recipient Name</label>
                    <input type="text" name="billing_address[recipient_name]" class="input input-bordered w-full"
                        value="{{ $order['billing_address']['recipient_name'] ?? '' }}" readonly>
                </div>
                <div>
                    <label class="text-sm">Phone</label>
                    <input type="text" name="billing_address[phone]" class="input input-bordered w-full"
                        value="{{ $order['billing_address']['phone'] ?? '' }}">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm">Street Address</label>
                    <input type="text" name="billing_address[street_address]" class="input input-bordered w-full"
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
                    <input type="text" name="billing_address[postal_code]" class="input input-bordered w-full"
                        value="{{ $order['billing_address']['postal_code'] ?? '' }}">
                </div>
                <div>
                    <label class="text-sm">Country</label>
                    <input type="text" name="billing_address[country]" class="input input-bordered w-full"
                        value="{{ $order['billing_address']['country'] ?? '' }}" readonly>
                </div>
            </div>
        </div>

        <div class="border border-base-300 rounded-box p-5 mt-5">
            <p class="font-semibold mb-2">Payment Method</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm">Name</label>
                    <input type="text" value="{{ $order['payment_method']['name'] ?? '-' }}" readonly
                        class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                </div>
                <div>
                    <label class="text-sm">Description</label>
                    <input type="text" value="{{ $order['payment_method']['description'] ?? '-' }}" readonly
                        class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                </div>

                @if (empty($order['payment_method']))
                    <div class="md:col-span-2 text-gray-500 italic">No payment method available.</div>
                @endif
            </div>


        </div>

        <div class="border border-base-300 rounded-box p-5 mt-5">
            <p class="font-semibold mb-2">Shipping Method</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm">Name</label>
                    <input type="text" value="{{ $order['shipping_method']['name'] ?? '-' }}" readonly
                        class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                </div>
                <div>
                    <label class="text-sm">Description</label>
                    <input type="text" value="{{ $order['shipping_method']['description'] ?? '-' }}" readonly
                        class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                </div>

                @if (empty($order['shipping_method']))
                    <div class="md:col-span-2 text-gray-500 italic">No shipping method available.</div>
                @endif
            </div>
        </div>

        <div class="border border-base-300 rounded-box p-5 mt-5">
            <p class="font-semibold mb-2">Ordered Products</p>
            <div class="overflow-x-auto">
                <table class="table table-sm">
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
                                        <div class="tooltip tooltip-right" data-tip="{{ $item['name'] }} is not existed">
                                            <p class="cursor-pointer hover:underline">
                                                {{ $item['name'] }}
                                            </p>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $item['sku'] }}</td>
                                <td>{{ $item['variant_id'] ? 'Variant Product' : 'Simple Product' }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>${{ number_format($item['unit_price'], 2) }}</td>
                                <td>${{ number_format($item['subtotal'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <p class="font-semibold mt-10">Critical Actions <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 inline">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
            </svg>
        </p>
        <div class="flex gap-3 mt-3 mb-20">
            <button class="btn btn-sm btn-outline btn-error"
                onclick="document.getElementById('delete_modal_{{ $order['id'] }}').showModal()">
                Delete Order
            </button>
        </div>


        <dialog id="complete_payment_dialog" class="modal">
            <div class="modal-box">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <p class="text-lg font-semibold py-0">Complete Order Payment</p>

                <p class="py-2 mb-0 text-sm">
                    Are you sure you want to complete
                    <span class="font-bold">Order #{{ $order['order_number'] }}</span>
                    ?
                </p>
                <div class="modal-action mt-0">
                    <form method="dialog">
                        <button class="btn">Cancel</button>
                    </form>
                    <form action="{{ route('admin.dashboard.order.id.pay.post', ['id' => $order['id']]) }}"
                        method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success"><svg xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                            </svg>
                            Complete Payment</button>
                    </form>
                </div>
            </div>
        </dialog>



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
                    <form method="POST" action="{{ route('admin.dashboard.order.id.delete', ['id' => $order['id']]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">Delete</button>
                    </form>
                </div>
            </div>
        </dialog>






    </div>
@endsection
