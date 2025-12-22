<?php

namespace App\Order\Services;

use App\Order\Models\Order;
use Exception;

class OrderService
{
    public static function getOrdersByUserId(int $user_id)
    {
        try {
            $orders = Order::where('user_id', $user_id)->orderBy('id', 'desc')->paginate(20);

            $orders->getCollection()->transform(function ($order) {
                return $order->jsonResponse(['products', 'shippingMethod', 'billingMethod']);
            });

            return $orders;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function getOrder(int $order_id)
    {
        try {
            $found_order = Order::find($order_id);

            if (!$found_order) abort(404, 'No order found');

            return $found_order->jsonResponse(['products', 'shippingMethod', 'paymentMethod', 'user']);
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function createOrder()
    {
        try {
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function updateOrder()
    {
        try {
        } catch (Exception $e) {
            throw ($e);
        }
    }


    public static function deleteOrder()
    {
        try {
        } catch (Exception $e) {
            throw ($e);
        }
    }

//     public static function consumeOrderProductQuantity(int $order_id)
//     {
//         try {
//             $order = Order::with('products.product.productVariants')->find($order_id);
// 
//             if (!$order) {
//                 throw new Exception('Order not found.');
//             }
// 
//             foreach ($order->products as $orderProduct) {
//                 $product = $orderProduct->product;
//                 $variantId = $orderProduct->variant_id ?? null;
//                 $quantity = (int) $orderProduct->quantity;
// 
//                 if ($variantId) {
//                     $variant = $product->productVariants->firstWhere('id', $variantId);
//                     if ($variant && $variant->enable_stock && $variant->stock !== null) {
//                         if ($variant->stock < $quantity) {
//                             throw new Exception("Insufficient stock for variant ID {$variantId}, {$product->name}");
//                         }
// 
//                         $variant->stock -= $quantity;
//                         $variant->save();
//                     }
//                 } else if ($product->enable_stock) {
//                     if ($product->stock < $quantity) {
//                         throw new Exception("Insufficient stock for  {$product->name}");
//                     }
// 
//                     $product->stock -= $quantity;
//                     $product->save();
//                 }
//             }
// 
//             return true;
//         } catch (Exception $e) {
//             throw $e;
//         }
//     }
}
