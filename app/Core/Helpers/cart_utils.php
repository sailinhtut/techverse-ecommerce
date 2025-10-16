<?php


if (!function_exists('getCartItemQuantity')) {
    function getCartItemQuantity($itemId)
    {
        $cart = session('cart_items', []);
        return $cart[$itemId]['quantity'] ?? 0;
    }
}

if (!function_exists('getCartTotalItems')) {
    function getCartTotalItems()
    {
        $cart = session('cart_items', []);
        return count($cart);
    }
}

if (!function_exists('getCartTotalCost')) {
    function getCartTotalCost()
    {
        $cart = session('cart_items', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += ($item['price'] * $item['quantity']);
        }

        return $total;
    }
}
