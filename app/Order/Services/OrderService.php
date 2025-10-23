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


    public static function getOrders()
    {
        try {
            $orders = Order::orderBy('id', 'desc')->paginate(20);

            $orders->getCollection()->transform(function ($order) {
                return $order->jsonResponse(['products', 'shippingMethod', 'paymentMethod']);
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

            return $found_order->jsonResponse(['products', 'shippingMethod', 'paymentMethod','user']);
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
}
