@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')

    <div class="max-w-6xl mx-auto p-6" x-data="checkoutData()" x-init="calculateTotals()">
        <h1 class="text-2xl font-semibold mb-6">Checkout</h1>

        <form method="POST" action="{{ route('checkout.post') }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">
                    <section x-init="$watch('shipping', (value) => {
                        console.log('Shipping Address Updated and Recalculating...');
                        calculateTotals();
                    })">
                        <h2 class="text-lg font-semibold mb-3">Shipping Address</h2>
                        <div class="space-y-3">
                            <input x-model="shipping.recipient_name" name="shipping_address[recipient_name]"
                                placeholder="Full Name" class="input input-bordered w-full" />
                            <input x-model="shipping.phone" name="shipping_address[phone]" placeholder="Phone"
                                class="input input-bordered w-full" />
                            <input x-model="shipping.street_address" name="shipping_address[street_address]"
                                placeholder="Street Address" class="input input-bordered w-full" />
                            <input x-model="shipping.city" name="shipping_address[city]" placeholder="City"
                                class="input input-bordered w-full" />
                            <input x-model="shipping.state" name="shipping_address[state]" placeholder="State"
                                class="input input-bordered w-full" />
                            <input x-model="shipping.postal_code" name="shipping_address[postal_code]"
                                placeholder="Postal Code" class="input input-bordered w-full" />
                            <input x-model.debounce.3000ms="shipping.country" name="shipping_address[country]"
                                placeholder="Country" class="input input-bordered w-full" />
                        </div>
                    </section>

                    <section x-init="$watch('billing', (value) => {
                        console.log('Billing Address Updated and Recalculating...');
                        calculateTotals();
                    })">
                        <h2 class="text-lg font-semibold mb-3">Billing Address</h2>
                        <div class="space-y-3">
                            <input x-model="billing.recipient_name" name="billing_address[recipient_name]"
                                placeholder="Full Name" class="input input-bordered w-full" />
                            <input x-model="billing.phone" name="billing_address[phone]" placeholder="Phone"
                                class="input input-bordered w-full" />
                            <input x-model="billing.street_address" name="billing_address[street_address]"
                                placeholder="Street Address" class="input input-bordered w-full" />
                            <input x-model="billing.city" name="billing_address[city]" placeholder="City"
                                class="input input-bordered w-full" />
                            <input x-model="billing.state" name="billing_address[state]" placeholder="State"
                                class="input input-bordered w-full" />
                            <input x-model="billing.postal_code" name="billing_address[postal_code]"
                                placeholder="Postal Code" class="input input-bordered w-full" />
                            <input x-model.debounce.2000ms="billing.country" name="billing_address[country]"
                                placeholder="Country" class="input input-bordered w-full" />
                        </div>
                    </section>

                    <section x-init="$watch('selectedShipping', (value) => {
                        console.log('Shipping Method Changed and Recalculating...');
                        calculateTotals();
                    })">
                        <h2 class="text-lg font-semibold mb-3">Shipping Method</h2>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer border p-3 rounded-lg hover:bg-base-200">
                                <input type="radio" x-model="selectedShipping" value="standard" class="radio" />
                                <div>
                                    <p class="font-medium">Standard Shipping</p>
                                    <p class="text-sm opacity-70">$10.00 - Delivered in 3–5 days</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer border p-3 rounded-lg hover:bg-base-200">
                                <input type="radio" x-model="selectedShipping" value="express" class="radio" />
                                <div>
                                    <p class="font-medium">Express Shipping</p>
                                    <p class="text-sm opacity-70">$20.00 - Delivered in 1–2 days</p>
                                </div>
                            </label>
                        </div>
                    </section>

                    <section>
                        <label class="block mb-1 font-medium">Order Note</label>
                        <textarea x-model="order_note" name="order_note" class="textarea textarea-bordered w-full" rows="3"
                            placeholder="Write any notes for your order..."></textarea>
                    </section>

                </div>

                <div class="lg:col-span-1 space-y-6">

                    <section class="border rounded-lg p-4">
                        <h2 class="text-lg font-semibold mb-3">Your Cart</h2>
                        <template x-for="(item, index) in cartItems" :key="item.id">
                            <div class="flex justify-between items-center border-b py-2">
                                {{-- 'cart_items' => 'required|array|min:1',
                                'cart_items.*.id' => 'required|integer|exists:products,id',
                                'cart_items.*.name' => 'required|string|max:150',
                                'cart_items.*.slug' => 'required|string|max:150',
                                'cart_items.*.price' => 'required|numeric|min:0',
                                'cart_items.*.quantity' => 'required|integer|min:1',
                                'cart_items.*.tax' => 'nullable|numeric|min:0',
                                'cart_items.*.shipping_cost' => 'nullable|numeric|min:0',
                                'cart_items.*.discount' => 'nullable|numeric|min:0', --}}
                                <input type="hidden" :name="`cart_items[${index}][id]`" :value="item.id">
                                <input type="hidden" :name="`cart_items[${index}][name]`" :value="item.name">
                                <input type="hidden" :name="`cart_items[${index}][slug]`" :value="item.slug">
                                <input type="hidden" :name="`cart_items[${index}][price]`" :value="item.price">
                                <input type="hidden" :name="`cart_items[${index}][quantity]`" :value="item.quantity">
                                <input type="hidden" :name="`cart_items[${index}][tax]`" :value="item.tax">
                                <input type="hidden" :name="`cart_items[${index}][shipping_cost]`"
                                    :value="item.shipping_cost">
                                <input type="hidden" :name="`cart_items[${index}][discount]`" :value="item.discount">
                                <div>
                                    <p class="font-medium" x-text="item.name"></p>
                                    <p class="text-sm text-gray-500">Qty: <span x-text="item.quantity"></span></p>
                                </div>
                                <div class="text-right">
                                    <p>$<span x-text="(item.price * item.quantity).toFixed(2)"></span></p>
                                </div>
                            </div>
                        </template>
                    </section>

                    <section class="border rounded-lg p-4 space-y-2">
                        <h2 class="text-lg font-semibold mb-3">Order Summary</h2>

                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>$<span x-text="subtotal.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tax</span>
                            <span>$<span x-text="tax.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Discount</span>
                            <span>− $<span x-text="discount.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span>$<span x-text="shipping_cost.toFixed(2)"></span></span>
                        </div>
                        <hr>
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Total</span>
                            <span>$<span x-text="grand_total.toFixed(2)"></span></span>
                        </div>
                    </section>

                    <div class="mt-6">
                        <button type="submit" class="btn btn-primary w-full">Place Order</button>
                    </div>

                </div>
            </div>
        </form>
    </div>
@endsection

@push('script')
    <script>
        function checkoutData() {
            return {
                cartItems: Alpine.store('cart').items,
                shipping: {
                    label: 'Shitment',
                    recipient_name: '{{ $default_shipping_address->recipient_name ?? '' }}',
                    phone: '{{ $default_shipping_address->phone ?? '' }}',
                    street_address: '{{ $default_shipping_address->street_address ?? '' }}',
                    city: '{{ $default_shipping_address->city ?? '' }}',
                    state: '{{ $default_shipping_address->state ?? '' }}',
                    postal_code: '{{ $default_shipping_address->postal_code ?? '' }}',
                    country: '{{ $default_shipping_address->country ?? '' }}'
                },
                billing: {
                    label: '{{ $default_billing_address->label ?? '' }}',
                    recipient_name: '{{ $default_billing_address->recipient_name ?? '' }}',
                    phone: '{{ $default_billing_address->phone ?? '' }}',
                    street_address: '{{ $default_billing_address->street_address ?? '' }}',
                    city: '{{ $default_billing_address->city ?? '' }}',
                    state: '{{ $default_billing_address->state ?? '' }}',
                    postal_code: '{{ $default_billing_address->postal_code ?? '' }}',
                    country: '{{ $default_billing_address->country ?? '' }}'
                },
                selectedShipping: 'standard',
                order_note: '',
                subtotal: 0,
                tax: 0,
                discount: 0,
                shipping_cost: 0,
                grand_total: 0,

                calculateTotals() {

                    this.subtotal = Object.values(this.cartItems).reduce((sum, i) => sum + i.price * i.quantity, 0);
                    this.tax = Object.values(this.cartItems).reduce((sum, i) => sum + (i.tax ?? 0), 0);
                    this.discount = Object.values(this.cartItems).reduce((sum, i) => sum + (i.discount ?? 0), 0);
                    this.shipping_cost = this.selectedShipping === 'express' ? 20 : 10;
                    this.grand_total = this.subtotal - this.discount + this.tax + this.shipping_cost;
                },
            };
        }
    </script>
@endpush
