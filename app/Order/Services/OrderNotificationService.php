<?php

namespace App\Order\Services;

use App\Auth\Models\Notification;

class OrderNotificationService
{
    public static function sendOrderCreated($order, $user)
    {
        OrderMailService::sendOrderCreatedMail($order, $user);

        $site_logo = getSiteLogoURL();

        Notification::create([
            'user_id' => $user->id,
            'image' => $site_logo,
            'title' => "Order {$order->order_number} Created Successfully",
            'message' => "Your order has been created successfully. We are processing your order and will update you once it is shipped. Thank you for shopping with us!",
            'link' => route('order_detail.id.get', $order->id),
            'type' => 'order',
            'priority' => 'high',
        ]);
    }

    public static function sendOrderUpdated($order, $user)
    {
        OrderMailService::sendOrderUpdatedMail($order, $user);

        $site_logo = getSiteLogoURL();
        $status = ucfirst($order->status);

        Notification::create([
            'user_id' => $user->id,
            'image' => $site_logo,
            'title' => "Order {$order->order_number} Updated Successfully (Status: {$status})",
            'message' => "Your order has been updated successfully. Please check the details of your order for more information.",
            'link' => route('order_detail.id.get', $order->id),
            'type' => 'order',
            'priority' => 'high',
        ]);
    }
}
