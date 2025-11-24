<?php

namespace App\Auth\Controllers;

use App\Auth\Models\Wishlist;
use App\Auth\Services\WishlistService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function getAPIWishlists()
    {
        try {
            $user_id = auth()->id();

            $all_wishlist_ids =  Wishlist::where('user_id', $user_id)
                ->orderBy('id', 'desc')
                ->pluck('product_id')
                ->toArray();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Added to wishlist',
                    'data' => [
                        'wishlist_ids' => $all_wishlist_ids
                    ]
                ]);
            }
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function toggleWishlist(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'product_variant_id' => 'nullable|exists:product_variants,id',
                'note' => 'nullable|string|max:255',
            ]);

            $user_id = auth()->id();

            $existing = Wishlist::where('user_id', $user_id)
                ->where('product_id', $validated['product_id'])
                ->where('product_variant_id', $validated['product_variant_id'] ?? null)
                ->first();

            if ($existing) {
                Wishlist::where('user_id', $user_id)
                    ->where('product_id', $validated['product_id'])
                    ->delete();

                $message = 'Removed from wishlist';
            } else {
                Wishlist::create([
                    'user_id' => $user_id,
                    'product_id' => $validated['product_id'],
                    'product_variant_id' => $validated['product_variant_id'] ?? null,
                    'note' => $validated['note'] ?? null,
                ]);

                $message = 'Added to wishlist';
            }

            $all_wishlist_ids = Wishlist::where('user_id', $user_id)
                ->orderBy('id', 'desc')
                ->pluck('product_id')
                ->toArray();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'wishlist_ids' => $all_wishlist_ids
                    ]
                ]);
            }

            return redirect()->back()->with('success', $message);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function deleteWishlist(Request $request, $id)
    {
        try {
            $deleted = WishlistService::deleteWishlist(intval($id));
            if (!$deleted) abort(500, 'Cannot delete wishlist item');

            $user_id = auth()->id();

            $all_wishlist_ids =  Wishlist::where('user_id', $user_id)
                ->orderBy('id', 'desc')
                ->pluck('product_id')
                ->toArray();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deleted wishlist item',
                    'data' => [
                        'wishlist_ids' => $all_wishlist_ids
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Wishlist item deleted');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
