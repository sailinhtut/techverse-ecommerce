<?php


namespace App\Payment\Services;

use App\Payment\Models\PaymentMethod;
use Exception;

class PaymentMethodService
{
    public static function calculatePaymentMethods($cart_items)
    {
        try {
            $paymentMethodIdsPerProduct = $cart_items->map(function ($cartItem) {
                if (!$cartItem->product || !$cartItem->product->paymentMethods) return [];

                return $cartItem->product->paymentMethods->pluck('id')->toArray();
            })->filter(fn($ids) => !empty($ids));

            $commonPaymentMethodIds = $paymentMethodIdsPerProduct->reduce(function ($carry, $item) {
                return $carry === null ? $item : array_intersect($carry, $item);
            });

            if (!empty($commonPaymentMethodIds)) {
                $commonPaymentMethods = PaymentMethod::whereIn('id', $commonPaymentMethodIds)
                    ->where('enabled', true)
                    ->get()
                    ->map(fn($method) => $method->jsonResponse());

                return $commonPaymentMethods;
            }

            $allMethods = PaymentMethod::where('enabled', true)->get();

            $highPriorityFallback = $allMethods->filter(fn($m) => $m->priority === 'high' && $m->enabled);

            if ($highPriorityFallback->isNotEmpty()) {
                $fallbackMethods = $highPriorityFallback->map(fn($m) => $m->jsonResponse());
            } else {
                $fallbackMethods = $allMethods->filter(fn($m) => $m->enabled)->map(fn($m) => $m->jsonResponse());
            }

            return $fallbackMethods;
        } catch (Exception $e) {
            throw ($e);
        }
    }
}
