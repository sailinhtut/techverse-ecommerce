<?php

namespace App\Order\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderMailService
{
    public static function sendOrderCreatedMail($order, $user)
    {
        try {
            Mail::send('emails.order.order_created', ['order' => $order, 'user' => $user], function ($message) use ($order, $user) {
                $message->to($user->email, $user->name)
                    ->subject('Order Confirmation - Order ' . $order->id);
            });
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function sendOrderUpdatedMail($order, $user)
    {
        try {
            $status = ucfirst($order->status);
            Mail::send(
                'emails.order.order_updated',
                ['order' => $order, 'user' => $user],
                function ($message) use ($order, $user, $status) {
                    $message->to($user->email, $user->name)
                        ->subject("Order Update – {$order->order_number} ({$status})");
                }
            );
        } catch (Exception $e) {
            Log::error('Order update mail failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
