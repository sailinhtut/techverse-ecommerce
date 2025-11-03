<?php

namespace App\Order\Controllers;

use App\Auth\Models\Address;
use App\Inventory\Models\Product;
use App\Inventory\Services\CouponService;
use App\Order\Models\Order;
use App\Order\Models\OrderProduct;
use App\Order\Services\OrderService;
use App\Payment\Models\Invoice;
use App\Payment\Models\PaymentMethod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController
{
    public function viewUserCheckOutPage()
    {
        try {
            $user = auth()->user();

            $default_shipping_address = Address::where('user_id', $user->id)
                ->where('is_default_shipping', true)
                ->latest()
                ->first();

            $default_billing_address = Address::where('user_id', $user->id)
                ->where('is_default_billing', true)
                ->latest()
                ->first();

            // $payment_methods = PaymentMethod::where('enabled', true)->get();

            return view('pages.user.core.checkout', [
                'default_shipping_address' => $default_shipping_address,
                'default_billing_address' => $default_billing_address,

            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewUserOrderHistory()
    {
        try {
            if (!auth()->check()) {
                abort(403, 'Unauthenticated. Please Log in');
            }

            $orders = OrderService::getOrdersByUserId(auth()->id());

            return view('pages.user.dashboard.order_history', [
                'orders' => $orders
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewUserOrderHistoryDetail(Request $request, $id)
    {
        try {
            if (!auth()->check()) {
                abort(403, 'Unauthenticated. Please Log in');
            }

            $order = Order::find($id);

            if (!$order) abort(404, 'No Order Found');


            return view('pages.user.dashboard.order_history_detail', [
                'order' => $order->jsonResponse(['products', 'shippingMethod', 'paymentMethod'])
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminOrderListPage()
    {
        try {
            $orders = OrderService::getOrders();

            return view('pages.admin.dashboard.order.order_list', [
                'orders' => $orders
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminOrderDetailPage(Request $request, $id)
    {
        try {
            $order = OrderService::getOrder(intval($id));

            return view('pages.admin.dashboard.order.order_detail', [
                'order' => $order
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createOrder(Request $request)
    {
        try {
            $user = auth()->user();

            $validated = $request->validate([
                'cart_items' => 'required|array|min:1',
                'cart_items.*.id' => 'required|integer|exists:products,id',
                'cart_items.*.variant_id' => 'nullable|integer|exists:product_variants,id',
                'cart_items.*.name' => 'required|string|max:150',
                'cart_items.*.sku' => 'required|string|max:150',
                'cart_items.*.price' => 'required|numeric|min:0',
                'cart_items.*.quantity' => 'required|integer|min:1',
                'cart_items.*.tax' => 'nullable|numeric|min:0',
                'cart_items.*.shipping_cost' => 'nullable|numeric|min:0',
                'cart_items.*.discount' => 'nullable|numeric|min:0',
                'shipping_cost_total' => 'required|numeric',
                'tax_cost_total' => 'required|numeric',
                'discount_total' => 'nullable|numeric',
                'coupon_code' => 'nullable|string',

                'shipping_address' => 'required|array',
                'billing_address' => 'required|array',

                'payment_method_id' => 'required|exists:payment_methods,id',
                'shipping_method_id' => 'required|exists:shipping_methods,id',

                'shipping_address.recipient_name' => 'required|string|max:150',
                'shipping_address.phone' => 'nullable|string|max:20',
                'shipping_address.street_address' => 'required|string|max:255',
                'shipping_address.city' => 'required|string|max:100',
                'shipping_address.state' => 'nullable|string|max:100',
                'shipping_address.postal_code' => 'nullable|string|max:20',
                'shipping_address.country' => 'required|string|max:100',
                'shipping_address.latitude' => 'nullable|numeric|between:-90,90',
                'shipping_address.longitude' => 'nullable|numeric|between:-180,180',

                'billing_address.recipient_name' => 'required|string|max:150',
                'billing_address.phone' => 'nullable|string|max:20',
                'billing_address.street_address' => 'required|string|max:255',
                'billing_address.city' => 'required|string|max:100',
                'billing_address.state' => 'nullable|string|max:100',
                'billing_address.postal_code' => 'nullable|string|max:20',
                'billing_address.country' => 'required|string|max:100',
                'billing_address.latitude' => 'nullable|numeric|between:-90,90',
                'billing_address.longitude' => 'nullable|numeric|between:-180,180',
            ], [
                'cart_items.required' => 'Cart cannot be empty.',
                'cart_items.*.id.exists' => 'One or more products do not exist.',
                'shipping_address.required' => 'Shipping address is required.',
                'billing_address.required' => 'Billing address is required.',
                'shipping_address.recipient_name.required' => 'Shipping recipient name cannot be empty.',
                'billing_address.recipient_name.required' => 'Billing recipient name cannot be empty.',
                'shipping_address.street_address.required' => 'Shipping address must have a street address.',
                'billing_address.street_address.required' => 'Billing address must have a street address.',
            ]);

            $cart_items = collect($validated['cart_items']);
            $shipping_address = $validated['shipping_address'];
            $billing_address = $validated['billing_address'];

            DB::beginTransaction();

            if (!$user->addresses()->where('is_default_shipping', true)->exists()) {
                Address::create(array_merge($shipping_address, [
                    'label' => $shipping_address['recipient_name'],
                    'user_id' => $user->id,
                    'is_default_shipping' => true,
                    'type' => 'shipping',
                ]));
            }

            if (!$user->addresses()->where('is_default_billing', true)->exists()) {
                Address::create(array_merge($billing_address, [
                    'label' => $billing_address['recipient_name'],
                    'user_id' => $user->id,
                    'is_default_billing' => true,
                    'type' => 'billing',
                ]));
            }

            $order_subtotal = $cart_items->sum(fn($i) => $i['price'] * $i['quantity']);

            $order_discount_total = $validated['discount_total'] ?? 0;
            $order_coupon_code = $validated['coupon_code'] ?? null;

            $order_tax_total =  $validated['tax_cost_total'];
            $order_shipping_total = $validated['shipping_cost_total'];
            $order_grand_total = $order_subtotal - $order_discount_total + $order_tax_total + $order_shipping_total;

            $today = now();
            $order_number = sprintf('ORD-%d-%d-%d-%04d', $today->year, $today->month, $today->day, (Order::max('id') ?? 0) + 1);

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $order_number,
                'status' => 'pending',
                'currency' => 'USD',
                'subtotal' => $order_subtotal,
                'discount_total' => $order_discount_total,
                'coupon_code' => $order_coupon_code,
                'tax_total' => $order_tax_total,
                'shipping_total' => $order_shipping_total,
                'grand_total' => $order_grand_total,
                'shipping_address' => $shipping_address,
                'billing_address' => $billing_address,
                'shipping_method_id' => $validated['shipping_method_id'],
                'payment_method_id' => $validated['payment_method_id'],
            ]);

            foreach ($cart_items as $item) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'sku' => $item['sku'],
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'tax' => $item['tax'] ?? 0,
                    'subtotal' => ($item['price'] * $item['quantity']) - ($item['discount'] ?? 0) + ($item['tax'] ?? 0),
                ]);
            }


            $invoice = Invoice::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'invoice_number' => sprintf('INV-%s', $order->order_number),
                    'subtotal' => $order->subtotal,
                    'discount_total' => $order->discount_total,
                    'tax_total' => $order->tax_total,
                    'shipping_total' => $order->shipping_total,
                    'grand_total' => $order->grand_total,
                    'status' => 'unpaid',
                    'issued_at' => now(),
                ]
            );

            if ($order_coupon_code) {
                $applied =  CouponService::applyCoupon($order_coupon_code);
                if (!$applied) abort(400, 'Coupon Code Is Invalid');
            }

            $consumed = OrderService::consumeOrderProductQuantity($order->id);
            if (!$consumed) {
                abort(400, 'Stock is not sufficient for order');
            }


            DB::commit();

            return redirect()->route(
                'order_detail.id.get',
                $order->id
            )->with('success', 'Order created successfully')->with('clear_cart', true)->with('clear_cart', true);

            // return redirect()->route('shop.get')->with('success', 'Order created successfully')->with('clear_cart', true);
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrors($e);
        }
    }

    public function updateOrder(Request $request, $id)
    {
        try {
            $order = Order::find($id);

            if (!$order) abort(404, "No order found [ID:{$id}]");

            $validated = $request->validate([
                'status' => 'nullable|string|in:pending,processing,shipped,delivered,cancelled,refunded',
                'subtotal' => 'nullable|numeric|min:0',
                'discount_total' => 'nullable|numeric|min:0',
                'tax_total' => 'nullable|numeric|min:0',
                'shipping_total' => 'nullable|numeric|min:0',
                'grand_total' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string',
                'coupon_code' => 'nullable|string',

                'shipping_address.recipient_name' => 'nullable|string|max:150',
                'shipping_address.phone' => 'nullable|string|max:20',
                'shipping_address.street_address' => 'nullable|string|max:255',
                'shipping_address.city' => 'nullable|string|max:100',
                'shipping_address.state' => 'nullable|string|max:100',
                'shipping_address.postal_code' => 'nullable|string|max:20',
                'shipping_address.country' => 'nullable|string|max:100',

                'billing_address.recipient_name' => 'nullable|string|max:150',
                'billing_address.phone' => 'nullable|string|max:20',
                'billing_address.street_address' => 'nullable|string|max:255',
                'billing_address.city' => 'nullable|string|max:100',
                'billing_address.state' => 'nullable|string|max:100',
                'billing_address.postal_code' => 'nullable|string|max:20',
                'billing_address.country' => 'nullable|string|max:100',
            ]);

            $order->update([
                'currency' => $validated['currency'] ?? $order->currency,
                'status' => $validated['status'] ?? $order->status,
                'subtotal' => $validated['subtotal'] ?? $order->subtotal,
                'discount_total' => $validated['discount_total'] ?? $order->discount_total,
                'tax_total' => $validated['tax_total'] ?? $order->tax_total,
                'shipping_total' => $validated['shipping_total'] ?? $order->shipping_total,
                'grand_total' => $validated['grand_total'] ?? $order->grand_total,

                'shipping_address' => [
                    'recipient_name' => $validated['shipping_address']['recipient_name'] ?? $order->shipping_address['recipient_name'] ?? null,
                    'phone' => $validated['shipping_address']['phone'] ?? $order->shipping_address['phone'] ?? null,
                    'street_address' => $validated['shipping_address']['street_address'] ?? $order->shipping_address['street_address'] ?? null,
                    'city' => $validated['shipping_address']['city'] ?? $order->shipping_address['city'] ?? null,
                    'state' => $validated['shipping_address']['state'] ?? $order->shipping_address['state'] ?? null,
                    'postal_code' => $validated['shipping_address']['postal_code'] ?? $order->shipping_address['postal_code'] ?? null,
                    'country' => $validated['shipping_address']['country'] ?? $order->shipping_address['country'] ?? null,
                ],

                'billing_address' => [
                    'recipient_name' => $validated['billing_address']['recipient_name'] ?? $order->billing_address['recipient_name'] ?? null,
                    'phone' => $validated['billing_address']['phone'] ?? $order->billing_address['phone'] ?? null,
                    'street_address' => $validated['billing_address']['street_address'] ?? $order->billing_address['street_address'] ?? null,
                    'city' => $validated['billing_address']['city'] ?? $order->billing_address['city'] ?? null,
                    'state' => $validated['billing_address']['state'] ?? $order->billing_address['state'] ?? null,
                    'postal_code' => $validated['billing_address']['postal_code'] ?? $order->billing_address['postal_code'] ?? null,
                    'country' => $validated['billing_address']['country'] ?? $order->billing_address['country'] ?? null,
                ],
            ]);

            return redirect()
                ->back()
                ->with('success', 'Order updated successfully!');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteOrder(Request $request, $id)
    {
        try {
            $order = Order::find($id);
            if (!$order) abort(404, 'No order found');
            $order->delete();

            return redirect()->back()->with('success', 'Order is deleted');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteAdminOrder(Request $request, $id)
    {
        try {
            $order = Order::find($id);
            if (!$order) abort(404, 'No order found');
            $order->delete();

            return redirect()->route('admin.dashboard.order.get')->with('success', 'Order is deleted');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
