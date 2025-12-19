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
                        <a href="{{ route('admin.dashboard.payment.invoice.get') }}" class="btn btn-xs btn-ghost">
                            Invoices
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.dashboard.payment.invoice.id.get', ['id' => $invoice['id']]) }}"
                            class="btn btn-xs btn-ghost">
                            {{ $invoice['invoice_number'] }}
                        </a>
                    </li>
                </ul>

            </div>
        </div>


        <p class="text-lg font-semibold mt-5">Invoice Detail</p>

        <div class="flex flex-wrap gap-3 mt-2">
            <a href="{{ route('admin.dashboard.order.id.get', ['id' => $invoice['order']['id']]) }}" class="btn btn-sm">See
                {{ $invoice['order']['order_number'] }} Order</a>
        </div>

        <div class="border border-base-300 rounded-box p-5 mt-5">
            <p class="font-semibold mb-2">Order Information</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-sm">Order ID</label>
                    <input type="text"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['id'] }}" readonly>
                </div>
                <div>
                    <label class="text-sm">Order Number</label>
                    <input type="text"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['order_number'] }}" readonly>
                </div>
                <div>
                    <label class="text-sm">Currency</label>
                    <input type="text" name="currency"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['currency'] }}" required>
                </div>
                <div>
                    <label class="text-sm">Created At</label>
                    <input type="text"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['created_at'] }}" readonly>
                </div>
                <div>
                    <label class="text-sm">Status</label>
                    <input type="text"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ ucfirst($invoice['order']['status']) }}" readonly>
                </div>
            </div>
        </div>


        <div class="border border-base-300 rounded-box p-5 mt-5">
            <p class="font-semibold mb-2">Payment Summary</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm">Subtotal</label>
                    <input type="number" step="0.01" name="subtotal"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['subtotal'] }}" readonly>
                </div>
                <div>
                    <label class="text-sm">Discount</label>
                    <input type="number" step="0.01" name="discount_total"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['discount_total'] }}" readonly>
                </div>

                <div>
                    <label class="text-sm">Coupon Code</label>
                    <input type="text" name="coupon_code"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['coupon_code'] ?? 'None' }}" readonly>
                </div>
                <div>
                    <label class="text-sm">Tax</label>
                    <input type="number" step="0.01" name="tax_total"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['tax_total'] }}" readonly>
                </div>
                <div>
                    <label class="text-sm">Shipping</label>
                    <input type="number" step="0.01" name="shipping_total"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['shipping_total'] }}" readonly>
                </div>
                <div>
                    <label class="text-sm">Grand Total</label>
                    <input type="number" step="0.01" name="grand_total"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['grand_total'] }}" readonly>
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
                        value="{{ $invoice['order']['user']['name'] }}" readonly>
                </div>

                <div>
                    <label class="text-sm">Email</label>
                    <input type="text"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['user']['email'] }}" readonly>
                </div>

                <div>
                    <label class="text-sm">Role</label>
                    <input type="text"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['user']['role']['display_name'] ?? 'N/A' }}" readonly>
                </div>

                <div>
                    <label class="text-sm">Phone (Primary)</label>
                    <input type="text"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['user']['phone_one'] ?? '-' }}" readonly>
                </div>

                <div>
                    <label class="text-sm">Phone (Secondary)</label>
                    <input type="text"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['user']['phone_two'] ?? '-' }}" readonly>
                </div>

                <div>
                    <label class="text-sm">Date of Birth</label>
                    <input type="text"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['user']['date_of_birth'] ?? '-' }}" readonly>
                </div>

                <div>
                    <label class="text-sm">Email Verified At</label>
                    <input type="text"
                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['user']['email_verified_at'] ? \Carbon\Carbon::parse($invoice['order']['user']['email_verified_at'])->format('Y-m-d h:i A') : 'Not Verified' }}"
                        readonly>
                </div>

                <div>
                    <label class="text-sm">Account Created</label>
                    <input type="text"
                        class="input w-full focus:outline-none focus:ring-0 focus-border-base-300 cursor-default select-none"
                        value="{{ $invoice['order']['user']['created_at'] ? \Carbon\Carbon::parse($invoice['order']['user']['created_at'])->format('Y-m-d h:i A') : '' }}"
                        readonly>
                </div>

                <div>
                    <label class="text-sm">Permissions</label>
                    <textarea readonly rows="2"
                        class="textarea w-full focus:outline-none focus:ring-0 focus-border-base-300 cursor-default select-none">{{ implode(', ', $invoice['order']['user']['role']['permissions']) ?: 'No Permissions' }}
                    </textarea>
                </div>
            </div>
        </div>

        <div class="border border-base-300 rounded-box p-5 mt-5">
            <p class="font-semibold mb-2">Payment Method</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm">Name</label>
                    <input type="text" value="{{ $invoice['order']['payment_method']['name'] ?? '-' }}" readonly
                        class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                </div>
                <div>
                    <label class="text-sm">Description</label>
                    <input type="text" value="{{ $invoice['order']['payment_method']['description'] ?? '-' }}"
                        readonly
                        class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                </div>

                @if (empty($invoice['order']['payment_method']))
                    <div class="md:col-span-2 text-gray-500 italic">No payment method available.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
