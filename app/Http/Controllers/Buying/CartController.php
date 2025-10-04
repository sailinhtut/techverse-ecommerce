<?php

namespace App\Http\Controllers\Buying;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function showCartList()
    {
        $cart_items = session()->get('cart_items', []);
        return view('pages.user.core.cart', compact('cart_items'));
    }

    public function addQuantity(Request $request, $id)
    {
        $cart_items = session()->get('cart_items', []);

        if (isset($cart_items[$id])) {
            $cart_items[$id]['quantity']++;
        } else {
            $cart_items[$id] = [
                'id' => $id,
                'title' => $request->input('title'),
                'price' => $request->input('price'),
                'image' => $request->input('image'),
                'slug' => $request->input('slug'),
                'quantity' => 1,
            ];
        }

        session()->put('cart_items', $cart_items);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$request->input('title')} is added to cart.",
                'cart_count' => count($cart_items),
            ]);
        }
        return redirect()->back()->with('success', "{$request->input('title')} is added to cart.");
    }

    public function removeQuantity(Request $request, $id)
    {
        $cart_items = session()->get('cart_items', []);
        $matched_item = null;

        if (isset($cart_items[$id])) {
            $matched_item = $cart_items[$id];
            $cart_items[$id]['quantity']--;

            if ($cart_items[$id]['quantity'] <= 0) {
                unset($cart_items[$id]);
            }

            session()->put('cart_items', $cart_items);

            return redirect()->back()->with('success', "Removed Item");
        }

        return redirect()->back()->with('error', "Item not found in cart.");
    }

    public function removeCartItem(Request $request, $id)
    {
        $cart_items = session()->get('cart_items', []);
        $matched_item = null;

        if (isset($cart_items[$id])) {
            $matched_item = $cart_items[$id];
            unset($cart_items[$id]);
            session()->put('cart_items', $cart_items);

            return redirect()->back()->with('success', "Removed Item");
        }

        return redirect()->back()->with('error', "Item not found in cart.");
    }

    public function clearCartItems(Request $request)
    {
        session()->forget('cart_items');
        return redirect()->back()->with('success', "Cart cleared successfully.");
    }
}
