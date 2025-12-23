<?php

namespace App\Order\Controllers;

use App\Auth\Models\Address;
use App\Cart\Models\Cart;
use App\Inventory\Models\Coupon;
use App\Inventory\Models\Product;
use App\Inventory\Models\ProductInventoryLog;
use App\Inventory\Models\ProductVariant;
use App\Inventory\Services\CouponService;
use App\Order\Exports\OrdersExport;
use App\Order\Models\Invoice;
use App\Order\Models\Order;
use App\Order\Models\OrderProduct;
use App\Order\Models\Payment;
use App\Order\Models\Transaction;
use App\Order\Services\OrderNotificationService;
use App\Order\Services\OrderService;
use App\Shipping\Services\ShippingMethodService;
use App\Tax\Services\TaxRateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
                abort(403, 'Please log in to continue. Please Log in');
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
                abort(403, 'Please log in to continue. Please Log in');
            }

            $order = Order::find($id);

            if (!$order) abort(404, 'No Order Found');

            $invoices = Invoice::where('order_id', $order->id)->get();
            $invoices = $invoices->map(function ($invoice) {
                return $invoice->jsonResponse(['payment']);
            });


            return view('pages.user.dashboard.order_history_detail', [
                'order' => $order->jsonResponse(
                    ['products', 'shippingMethod', 'paymentMethod'],
                ),
                'invoices' => $invoices
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminOrderListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $date_limit = $request->get('date_limit', null);
            $order_status = $request->get('order_status', null);

            $is_archived = $request->get('is_archived', null);

            $query = Order::query();

            if (!is_null($is_archived) || $is_archived === 'true') {
                $query->where('archived', true);
            } else {
                $query->where('archived', false);
            }

            if ($date_limit) {
                $now = now();
                if (preg_match('/last_(\d+)_(day|month|year)/', $date_limit, $matches)) {
                    $amount = (int) $matches[1];
                    $unit = $matches[2];

                    $fromDate = match ($unit) {
                        'day' => $now->copy()->subDays($amount)->startOfDay(),
                        'month' => $now->copy()->subMonths($amount)->startOfDay(),
                        'year' => $now->copy()->subYears($amount)->startOfDay(),
                        default => null,
                    };

                    if ($fromDate) {
                        $query->where('created_at', '>=', $fromDate);
                    }
                }
            }

            if ($order_status) {
                $query->where('status', $order_status);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }

            switch ($sortBy) {
                case 'last_updated':
                    $query->orderBy('updated_at', $orderBy)
                        ->orderBy('id', $orderBy);
                    break;

                case 'last_created':
                    $query->orderBy('created_at', $orderBy)->orderBy('id', $orderBy);
                    break;

                default:
                    $query->orderBy('updated_at', 'desc')
                        ->orderBy('id', 'desc');
            }


            $orders = $query->paginate($perPage);
            $orders->appends(request()->query());

            $orders->getCollection()->transform(function ($order) {
                return $order->jsonResponse(['products', 'shippingMethod', 'paymentMethod']);
            });

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
            $order = OrderService::getOrder((int) $id);

            if (!$order) {
                abort(404, 'No Order Found');
            }

            $invoices = Invoice::where('order_id', $id)->orderBy('id', 'desc')->get();
            $invoices = $invoices->map(fn($i) => $i->jsonResponse(['payments']));

            $payments = Payment::where('order_id', $id)->orderBy('id', 'desc')->get();
            $payments = $payments->map(fn($i) => $i->jsonResponse(['invoice', 'order']));

            $transactions = Transaction::where('order_id', $id)->orderBy('id', 'desc')->get();
            $transactions = $transactions->map(fn($i) => $i->jsonResponse(['user', 'invoice', 'order']));

            return view('pages.admin.dashboard.order.order_detail', [
                'order'        => $order,
                'invoices'     => $invoices,
                'payments'     => $payments,
                'transactions' => $transactions,
            ]);
        } catch (\Exception $e) {
            return handleErrors($e);
        }
    }


    public function viewAdminOrderExportPage(Request $request)
    {
        try {
            return view('pages.admin.dashboard.order.order_export', []);
        } catch (\Exception $e) {
            return handleErrors($e);
        }
    }



    public function createOrder(Request $request)
    {
        try {
            $user = auth()->user();

            $validated = $request->validate([
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
            ]);

            $cart = Cart::with('items.product')
                ->where('user_id', auth()->id())
                ->where('is_checked_out', false)
                ->first();

            if (!$cart || !$cart->items()->exists()) {
                return response()->json(['message' => "Your cart is empty"], 400);
            }

            $cart_items = $cart->items;
            $shipping_address = $validated['shipping_address'];
            $billing_address = $validated['billing_address'];
            $shipping_method_id = $validated['shipping_method_id'];
            $payment_method_id = $validated['payment_method_id'];

            // shipping cost calculation
            $matched_shipping_method = collect(ShippingMethodService::calculateShippingMethods($shipping_address, $cart_items))
                ->firstWhere('id', $shipping_method_id);

            if (!$matched_shipping_method) {
                abort(400, 'Shipping method not invalid');
            }
            $order_shipping_total = $matched_shipping_method['shipping_cost'];

            // coupon discount calculation
            $order_discount_total = 0;
            $order_coupon_code =  $validated['coupon_code'] ?? null;

            if (!empty($validated['coupon_code'])) {
                $coupon = Coupon::where('code', $order_coupon_code)->first();
                if ($coupon) {
                    $order_discount_total = $coupon->calculateDiscount($cart_items)['coupon_discount'] ?? 0;
                }
            }

            // tax calculation
            $order_tax_total =  TaxRateService::calculateTax($shipping_address, $cart_items);

            // grand total calculation
            $order_subtotal = $cart_items->sum('subtotal');
            $order_grand_total = $order_subtotal + $order_tax_total + $order_shipping_total;
            $order_grand_total = $order_grand_total - $order_discount_total;

            $today = now();
            $order_number = sprintf('ORD-%d-%d-%d-%04d', $today->year, $today->month, $today->day, (Order::max('id') ?? 0) + 1);

            DB::beginTransaction();

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
                'shipping_method_id' => $shipping_method_id,
                'payment_method_id' => $payment_method_id,
            ]);

            foreach ($cart_items as $item) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'sku' => $item['sku'],
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'discount' => 0,
                    'tax' => 0,
                    'subtotal' => $item['subtotal'] ?? 0
                ]);
            }


            $invoice = Invoice::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'invoice_number' => $this->generateInvoiceNumber($order),
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

            // saving addresses
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

            // clean cart
            $cart->delete();

            DB::commit();

            OrderNotificationService::sendOrderCreated($order, $user);

            return redirect()->route(
                'order_detail.id.get',
                $order->id
            )->with('success', 'Order created successfully');
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
                'status' => 'nullable|string|in:pending,processing,shipped,delivered,completed,cancelled,refunded',
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

            if ($order->wasChanged('status')) {
                $user = $order->user;
                OrderNotificationService::sendOrderUpdated($order, $user);
            }

            return redirect()
                ->back()
                ->with('success', 'Order updated successfully!');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createOrderInvoice(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'issue_amount_percentage' => 'required|in:10,25,50,75,100'
            ]);

            $order = Order::find($validated['order_id']);

            // if ($validated['issue_amount_percentage'] == 100) {
            //     $existing = Invoice::where('order_id', $order->id)->exists();
            //     if ($existing) {
            //         return abort(422,  '< 100% Invoice already exists for this order.');
            //     }
            // }

            $percentage = (int) $validated['issue_amount_percentage'] / 100;

            Invoice::create([
                'order_id'         => $order->id,
                'invoice_number'   => $this->generateInvoiceNumber($order),
                'subtotal'         => round($order->subtotal * $percentage, 2),
                'discount_total'   => round($order->discount_total * $percentage, 2),
                'tax_total'        => round($order->tax_total * $percentage, 2),
                'shipping_total'   => round($order->shipping_total * $percentage, 2),
                'grand_total'      => round($order->grand_total * $percentage, 2),
                'status'           => 'unpaid',
                'issued_at'        => now(),
            ]);

            return redirect()->back()->with('success', 'Invoice created successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function completeInvoicePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
            ]);

            DB::beginTransaction();

            $invoice = Invoice::lockForUpdate()->findOrFail($validated['invoice_id']);

            if ($invoice->status === 'paid') {
                return abort(422,  'Invoice is already paid.');
            }

            if ($invoice->status === 'refunded') {
                return abort(422,  'Refunded invoice cannot be paid. Create new invoice and complete payment.');
            }

            $payment = Payment::create([
                'order_id' => $invoice->order_id,
                'invoice_id' => $invoice->id,
                'amount'     => $invoice->grand_total,
            ]);

            Transaction::create([
                'order_id'   => $invoice->order_id,
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'user_id'    => auth()->id(),
                'status'     => 'succeeded',
                'amount'     => $payment->amount,
            ]);

            $invoice->update([
                'status' => 'paid',
            ]);

            DB::commit();

            return back()->with('success', 'Invoice payment completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return handleErrors($e);
        }
    }


    public function cancelInvoicePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
            ]);

            DB::beginTransaction();

            $invoice = Invoice::lockForUpdate()->findOrFail($validated['invoice_id']);

            $payments = Payment::where('invoice_id', $invoice->id)->get();

            if ($payments->isEmpty()) {
                return abort(422,  'No payments found for this invoice.');
            }

            foreach ($payments as $payment) {
                Transaction::create([
                    'order_id'   => $invoice->order_id,
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'user_id'    => auth()->id(),
                    'status'     => 'cancelled',
                    'amount'     => $payment->amount,
                ]);
            }

            Payment::where('invoice_id', $invoice->id)->delete();

            $invoice->update([
                'status' => 'unpaid',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Invoice payments cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return handleErrors($e);
        }
    }


    public function refundInvoicePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
            ]);

            DB::beginTransaction();

            $invoice = Invoice::lockForUpdate()->findOrFail($validated['invoice_id']);

            $payments = Payment::where('invoice_id', $invoice->id)->get();

            if ($payments->isEmpty()) {
                return abort(422, 'No payments found for this invoice.');
            }

            foreach ($payments as $payment) {
                Transaction::create([
                    'order_id'   => $invoice->order_id,
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'user_id'    => auth()->id(),
                    'status'     => 'refunded',
                    'amount'     => $payment->amount,
                ]);
            }

            Payment::where('invoice_id', $invoice->id)->delete();

            $invoice->update([
                'status' => 'refunded',
            ]);

            DB::commit();

            return back()->with('success', 'Invoice payments refunded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return handleErrors($e);
        }
    }


    public function consumeOrderStock(int $orderId)
    {
        try {
            DB::beginTransaction();

            $order = Order::with('products')
                ->lockForUpdate()
                ->findOrFail($orderId);

            foreach ($order->products as $item) {

                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                $variant = $item->variant_id
                    ? ProductVariant::lockForUpdate()->findOrFail($item->variant_id)
                    : null;

                $stockSource = $variant ?: $product;

                if (!$stockSource->enable_stock) {
                    continue;
                }

                $before = $stockSource->stock;
                $after  = $before - $item->quantity;

                if ($after < 0) {
                    abort(400, "Insufficient stock for {$item->name}");
                }

                $stockSource->update([
                    'stock' => $after,
                ]);

                ProductInventoryLog::create([
                    'product_id'     => $product->id,
                    'variant_id'     => $variant?->id,
                    'action_type'    => 'out',
                    'quantity'       => $item->quantity,
                    'stock_before'   => $before,
                    'stock_after'    => $after,
                    'reference_type' => 'order',
                    'reference_id'   => $order->id,
                    'note'           => 'Order stock consumption',
                    'created_by'     => auth()->id(),
                ]);
            }

            $order->update([
                'stock_consumed' => true
            ]);

            DB::commit();

            return back()->with('success', 'Order stock are consumed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            handleErrors($e);
        }
    }

    public function refundOrderStock(int $orderId)
    {
        try {
            DB::beginTransaction();

            $order = Order::with('products')
                ->lockForUpdate()
                ->findOrFail($orderId);

            foreach ($order->products as $item) {

                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                $variant = $item->variant_id
                    ? ProductVariant::lockForUpdate()->findOrFail($item->variant_id)
                    : null;

                $stockSource = $variant ?: $product;

                if (!$stockSource->enable_stock) {
                    continue;
                }

                $before = $stockSource->stock;
                $after  = $before + $item->quantity;

                $stockSource->update([
                    'stock' => $after,
                ]);

                ProductInventoryLog::create([
                    'product_id'     => $product->id,
                    'variant_id'     => $variant?->id,
                    'action_type'    => 'in',
                    'quantity'       => $item->quantity,
                    'stock_before'   => $before,
                    'stock_after'    => $after,
                    'reference_type' => 'order_refund',
                    'reference_id'   => $order->id,
                    'note'           => 'Order stock refund',
                    'created_by'     => auth()->id(),
                ]);
            }

            $order->update([
                'stock_consumed' => false
            ]);

            DB::commit();

            return back()->with('success', 'Order stock are refunded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            handleErrors($e);
        }
    }


    public function viewOrderInvoice(Request $request, int $order_id, int $invoice_id)
    {
        try {
            $order = Order::with(['products', 'user'])->findOrFail($order_id);
            $invoice = Invoice::findOrFail($invoice_id);
            $user = auth()->user();

            return view('pdf.order_invoice', [
                'order' => $order,
                'invoice' => $invoice,
                'user' => $user,
            ]);
        } catch (\Throwable $e) {
            return handleErrors($e);
        }
    }

    public function downloadOrderInvoice(Request $request, int $order_id, int $invoice_id)
    {
        try {
            $order = Order::with(['products', 'user'])->findOrFail($order_id);
            $invoice = Invoice::findOrFail($invoice_id);
            $user = auth()->user();

            $site_logo_url = getSiteLogoURL();
            $raw_site_logo = 'https://masterseller.shop/assets/images/app_logo.png';
            $imageData = file_get_contents($site_logo_url);

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($imageData);

            $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);

            $pdf = Pdf::loadView('pdf.order_invoice', [
                'order' => $order,
                'invoice' => $invoice,
                'user' => $user,
                'base64Image' => $base64Image
            ]);

            $fileName = 'invoice-' . $order->order_number . '-' . $invoice->id . '.pdf';

            return $pdf->download($fileName);
        } catch (\Throwable $e) {
            return handleErrors($e);
        }
    }


    public function exportOrders(Request $request)
    {
        try {
            $validated = $request->validate([
                'export_name'       => 'nullable|string',
                'export_start_date' => 'nullable|date',
                'export_end_date'   => 'nullable|date',
            ]);

            $startDate = !empty($validated['export_start_date'])
                ? Carbon::parse($validated['export_start_date'])->startOfDay()
                : null;

            $endDate = !empty($validated['export_end_date'])
                ? Carbon::parse($validated['export_end_date'])->endOfDay()
                : null;

            $query = Order::with([
                'paymentMethod',
                'shippingMethod',
            ]);

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->where('created_at', '>=', $startDate);
            } elseif ($endDate) {
                $query->where('created_at', '<=', $endDate);
            }

            $orders = $query->orderBy('created_at', 'asc')->get();

            $fileName = ($validated['export_name'] ?? 'orders_export')
                . '.xlsx';

            return Excel::download(
                new OrdersExport($orders),
                $fileName
            );
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function archiveOrder(Request $request, $id)
    {
        try {
            $order = Order::find($id);
            if (!$order) abort(404, 'No order found');
            $order->update(['archived' => true]);

            return redirect()->back()->with('success', 'Order is archived');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function archiveSelectedOrders(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No orders selected for archive.');
            }

            $orders = Order::whereIn('id', $ids)->get();

            foreach ($orders as $order) {
                $order->update(['archived' => true]);
            }

            return redirect()->back()->with('success', 'Selected orders archived successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while archiving selected orders.");
        }
    }

    public function archiveAllOrders()
    {
        try {
            $orders = Order::all();

            foreach ($orders as $order) {
                $order->update(['archived' => true]);
            }

            return redirect()->back()->with('success', 'All orders archived successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while archiving all orders.");
        }
    }

    public function unarchiveOrder(Request $request, $id)
    {
        try {
            $order = Order::find($id);
            if (!$order) abort(404, 'No order found');
            $order->update(['archived' => false]);

            return redirect()->back()->with('success', 'Order is unarchived');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function unarchiveSelectedOrders(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No orders selected for unarchive.');
            }

            $orders = Order::whereIn('id', $ids)->get();

            foreach ($orders as $order) {
                $order->update(['archived' => false]);
            }

            return redirect()->back()->with('success', 'Selected orders unarchived successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while unarchiving selected orders.");
        }
    }

    public function unarchiveAllOrders()
    {
        try {
            $orders = Order::all();

            foreach ($orders as $order) {
                $order->update(['archived' => false]);
            }

            return redirect()->back()->with('success', 'All orders unarchived successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while archiving all orders.");
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

    public function deleteSelectedOrders(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No orders selected for deletion.');
            }

            $orders = Order::whereIn('id', $ids)->get();

            foreach ($orders as $order) {
                $order->delete();
            }

            return redirect()->back()->with('success', 'Selected orders deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected orders.");
        }
    }


    public function deleteAllOrders()
    {
        try {
            $orders = Order::all();

            foreach ($orders as $order) {
                $order->delete();
            }

            return redirect()->back()->with('success', 'All orders deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all orders.");
        }
    }

    private function generateInvoiceNumber(Order $order): string
    {
        $count = Invoice::where('order_id', $order->id)->count() + 1;

        return sprintf(
            'INV-%s-%02d',
            $order->order_number,
            $count
        );
    }
}
