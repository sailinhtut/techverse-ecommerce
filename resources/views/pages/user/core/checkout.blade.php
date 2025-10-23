@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')

    <div class="max-w-6xl mx-auto p-6" x-data="checkoutData()" x-init="calculateTotals()">
        <h1 class="text-2xl font-semibold mb-6">Checkout</h1>

        <form method="POST" action="{{ route('checkout.post') }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">
                    <section>
                        <h2 class="text-lg font-semibold mb-3">Shipping Address</h2>
                        <div class="space-y-3">
                            <input x-model.debounce.1000ms="shipping.recipient_name" name="shipping_address[recipient_name]"
                                placeholder="Full Name" class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="shipping.phone" name="shipping_address[phone]"
                                placeholder="Phone" class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="shipping.street_address" name="shipping_address[street_address]"
                                placeholder="Street Address" class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="shipping.city" name="shipping_address[city]" placeholder="City"
                                class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="shipping.state" name="shipping_address[state]"
                                placeholder="State" class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="shipping.postal_code" name="shipping_address[postal_code]"
                                placeholder="Postal Code" class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="shipping.country" name="shipping_address[country]"
                                placeholder="Country" class="input input-bordered w-full" />
                        </div>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold mb-3">Billing Address</h2>
                        <div class="space-y-3">
                            <input x-model.debounce.1000ms="billing.recipient_name" name="billing_address[recipient_name]"
                                placeholder="Full Name" class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="billing.phone" name="billing_address[phone]" placeholder="Phone"
                                class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="billing.street_address" name="billing_address[street_address]"
                                placeholder="Street Address" class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="billing.city" name="billing_address[city]" placeholder="City"
                                class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="billing.state" name="billing_address[state]" placeholder="State"
                                class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="billing.postal_code" name="billing_address[postal_code]"
                                placeholder="Postal Code" class="input input-bordered w-full" />
                            <input x-model.debounce.1000ms="billing.country" name="billing_address[country]"
                                placeholder="Country" class="input input-bordered w-full" />
                        </div>
                    </section>

                    {{-- Shipping Options --}}
                    <section>
                        <h2 class="text-lg font-semibold mb-3">Shipping Method</h2>

                        <input type="hidden" name="shipping_method_id" :value="selectedShippingId" required>

                        <template x-if="loadingShippingMethods">
                            <div class="space-y-2 animate-pulse">
                                <div class="h-12 bg-gray-200 rounded-lg w-full"></div>
                                <div class="h-12 bg-gray-200 rounded-lg w-full"></div>
                                <div class="h-12 bg-gray-200 rounded-lg w-full"></div>
                            </div>
                        </template>

                        <div x-show="!loadingShippingMethods" class="space-y-2">
                            <p x-cloak x-show="shipping_methods.length === 0 && shipping_method_error" class="text-red-500"
                                x-text="shipping_method_error"></p>
                            <p x-cloak x-show="shipping_methods.length === 0 && !shipping_method_error"
                                class="text-red-500">No
                                Available Shipping Method
                            </p>
                            <template x-show="shipping_methods" x-for="method in shipping_methods" :key="method.method.id">
                                <label class="flex flex-col cursor-pointer border p-3 rounded-lg hover:bg-base-200"
                                    :class="selectedPaymentId === method.method.id ? 'border-primary bg-base-100' :
                                        'border-gray-200'">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" name="shipping_method_id" :value="method.method.id"
                                            class="radio mt-1" x-model="selectedShippingId" />
                                        <div class="flex flex-col">
                                            <span class="font-medium" x-text="method.method.name"></span>
                                            <span class="text-sm opacity-70"><span
                                                    x-text="`${method.total_cost > 0 ? method.total_cost : ''}${method.total_cost ? '$ - ' : ''}`"></span>

                                                <span x-text="method.method.description"></span></span>
                                        </div>
                                    </div>

                                </label>
                            </template>
                        </div>
                    </section>

                    {{-- Payment Options --}}
                    <section>
                        <h2 class="text-lg font-semibold mb-3">Payment Method</h2>

                        <input type="hidden" name="payment_method_id" :value="selectedPaymentId" required>

                        <template x-if="loadingPaymentMethods">
                            <div class="space-y-2 animate-pulse">
                                <div class="h-12 bg-gray-200 rounded-lg w-full"></div>
                                <div class="h-12 bg-gray-200 rounded-lg w-full"></div>
                                <div class="h-12 bg-gray-200 rounded-lg w-full"></div>
                            </div>
                        </template>

                        <div x-show="!loadingPaymentMethods" class="space-y-2">
                            <p x-cloak x-show="payment_methods.length === 0 && payment_method_error" class="text-red-500"
                                x-text="payment_method_error">
                            </p>
                            <p x-cloak x-show="payment_methods.length === 0 && !payment_method_error"
                                class="text-red-500">No
                                Available Payment Method
                            </p>
                            <template x-show="payment_methods.length > 0" x-for="method in payment_methods"
                                :key="method.id">
                                <label class="flex flex-col cursor-pointer border p-3 rounded-lg hover:bg-base-200"
                                    :class="selectedPaymentId === method.id ? 'border-primary bg-base-100' : 'border-gray-200'">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" name="payment_method_id" :value="method.id"
                                            class="radio mt-1" x-model="selectedPaymentId" />
                                        <div class='flex flex-col'>
                                            <span class="font-medium" x-text="method.name"></span>
                                            <span class="text-sm opacity-70 mt-1" x-text="method.description"></span>
                                        </div>

                                    </div>

                                </label>
                            </template>
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
                                'cart_items.*.discount' => 'nullable|numeric|min:0', --}}
                                <input type="hidden" :name="`cart_items[${index}][id]`" :value="item.id">
                                <input type="hidden" :name="`cart_items[${index}][variant_id]`" :value="item.variant_id">
                                <input type="hidden" :name="`cart_items[${index}][name]`" :value="item.name">
                                <input type="hidden" :name="`cart_items[${index}][sku]`" :value="item.sku">
                                <input type="hidden" :name="`cart_items[${index}][price]`" :value="item.price">
                                <input type="hidden" :name="`cart_items[${index}][quantity]`" :value="item.quantity">
                                <input type="hidden" :name="`cart_items[${index}][tax]`" :value="item.tax_total_cost">
                                <input type="hidden" :name="`cart_items[${index}][discount]`" :value="item.discount">
                                <div>
                                    <p class="font-medium" x-text="item.name"></p>
                                    <p class="font-medium" x-text="item.variant_id"></p>
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
                        <div x-show="discount > 0" class="flex justify-between">
                            <span>Discount</span>
                            <span>− $<span x-text="discount.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <input type="hidden" name="shipping_cost_total" :value="shipping_cost.toFixed(2)">
                            <span>$<span x-text="shipping_cost.toFixed(2)"></span></span>
                        </div>
                        <div class="">
                            <span x-show="tax_calculation_error" x-text="`Tax Error: ${tax_calculation_error}`"
                                class="text-xs w-3/5 text-error"></span>
                        </div>
                        <div x-show="!tax_calculation_error" class="flex justify-between">
                            <span>Tax</span>
                            <input type="hidden" name="tax_cost_total" :value="tax_total_cost.toFixed(2)">
                            <div x-show="loadingTaxTotalCost" class="loading loading-spinner-sm"></div>
                            <span x-show="!loadingTaxTotalCost">$<span x-text="tax_total_cost.toFixed(2)"></span></span>
                        </div>
                        <hr>
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Total</span>
                            <span>$<span x-text="grand_total.toFixed(2)"></span></span>
                        </div>
                    </section>

                    <div class="mt-6">
                        <button type="submit" class="btn btn-primary w-full" :disabled="!can_place_order">Place
                            Order</button>
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

                loadingTaxTotalCost: false,
                tax_total_cost: 0,
                tax_calculation_error: '',

                selectedShippingId: '',
                shipping_methods: [],
                loadingShippingMethods: false,
                shipping_method_error: '',

                selectedPaymentId: '',
                payment_methods: [],
                loadingPaymentMethods: false,
                payment_method_error: '',

                order_note: '',
                subtotal: 0,
                discount: 0,
                shipping_cost: 0,
                grand_total: 0,

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
                init() {
                    this.loadPaymentMethods();
                    this.loadShippingMethods();
                    this.calculateTaxTotalCost();

                    this.$watch('shipping', (value) => {
                        this.loadShippingMethods();
                        this.calculateTaxTotalCost();
                    });

                    this.$watch('billing', (value) => {
                        this.loadPaymentMethods();
                    });

                    this.$watch('selectedShippingId', (value) => {
                        this.calculateTotals();
                    });
                },
                get can_place_order() {
                    return this.selectedShippingId && !this.shipping_method_error && this.shipping_methods.length > 0 &&
                        this.selectedPaymentId && !this
                        .payment_method_error && this.payment_methods.length > 0 && !this.tax_calculation_error;
                },
                calculateTotals() {
                    const selectedShippingMethod = this.shipping_methods.find(
                        (e) => e.method.id == this.selectedShippingId
                    );
                    this.shipping_cost = selectedShippingMethod ? selectedShippingMethod.total_cost : 0;

                    this.subtotal = Object.values(this.cartItems).reduce((sum, i) => sum + i.price * i.quantity, 0);
                    this.discount = 0;
                    this.grand_total = (this.subtotal + this.tax_total_cost + this.shipping_cost) - this.discount;
                },
                async calculateTaxTotalCost() {
                    try {
                        if (this.loadingTaxTotalCost) return;
                        console.log('Fetching Tax Total Cost...');
                        this.loadingTaxTotalCost = true;
                        const cart_items = Object.values(this.cartItems).map((e) => {
                            return {
                                'id': e.id,
                                'quantity': e.quantity,
                            }
                        });

                        const response = await axios.post(
                            '/calculate-tax', {
                                cart_items: cart_items,
                                shipping_address: this.shipping
                            }, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );
                        this.tax_total_cost = response.data.data.total_cost ?? [];
                        this.tax_calculation_error = '';

                    } catch (error) {
                        this.tax_total_cost = 0;
                        this.tax_calculation_error = error.response.data.message ?? 'Unexpected Error Occured';
                    } finally {
                        this.loadingTaxTotalCost = false;
                        this.calculateTotals();
                    }
                },
                async loadShippingMethods() {
                    try {
                        if (this.loadingShippingMethods) return;
                        console.log('Fetching Shipping Methods...');
                        this.loadingShippingMethods = true;
                        const cart_items = Object.values(this.cartItems).map((e) => {
                            return {
                                'id': e.id,
                                'variant_id': e.variant_id,
                                'price' : e.price,
                                'quantity': e.quantity,
                            }
                        });

                        const response = await axios.post(
                            '/filter-shipping-method', {
                                cart_items: cart_items,
                                shipping_address: this.shipping
                            }, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );
                        this.shipping_methods = response.data.data ?? [];
                        this.shipping_method_error = '';
                        console.log(response.data);
                    } catch (error) {
                        this.shipping_methods = [];
                        this.selectedShippingId = '';
                        this.shipping_method_error = error.response.data.message ?? 'Unexpected Error Occured';
                    } finally {
                        this.loadingShippingMethods = false;
                    }
                },
                async loadPaymentMethods() {
                    if (this.loadingPaymentMethods) return;
                    console.log('Fetching Payment Methods...');
                    try {
                        this.loadingPaymentMethods = true;
                        const product_id_list = Object.values(this.cartItems).map(e => e.id);
                        const response = await axios.post(
                            '/filter-payment-method', {
                                product_ids: product_id_list,
                            }, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );
                        this.payment_methods = response.data.data ?? [];
                        this.payment_method_error = '';
                    } catch (error) {
                        this.payment_methods = [];
                        this.selectedPaymentId = '';
                        this.payment_method_error = error.response.data.message ?? 'Unexpected Error Occured';
                    } finally {
                        this.loadingPaymentMethods = false;
                    }
                },

            };
        }
    </script>
@endpush
