<?php

namespace App\Auth\Controllers;

use App\Auth\Services\WishlistService;
use Exception;
use Illuminate\Http\Request;

class WishlistController
{
    public function getWishlists()
    {
        try {
            $wishlists = WishlistService::getWishlists();
            return view('pages.user.dashboard.wishlist', [
                'wishlists' => $wishlists
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createWishlist(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'product_id' => 'required|exists:products,id',
                'product_variant_id' => 'nullable|exists:product_variants,id',
                'note' => 'nullable|string|max:255',
            ]);

            $wishlist = WishlistService::createWishlist($validated);
            return redirect()->back()->with('success', 'Added to wishlist');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteWishlist(Request $request, $id)
    {
        try {
            $deleted = WishlistService::deleteWishlist(intval($id));
            if (!$deleted) abort(500, 'Cannot delete wishlist item');

            return redirect()->back()->with('success', 'Wishlist item deleted');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
