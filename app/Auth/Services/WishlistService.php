<?php

namespace App\Auth\Services;

use App\Auth\Models\Wishlist;
use Exception;
use Illuminate\Http\Request;

class WishlistService
{
    public static function getWishlists()
    {
        try {
            if (!auth()->check()) abort(403, 'Please log in to continue');

            $wishlists = Wishlist::where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(20);
            $wishlists->getCollection()->transform(fn($item) => $item->jsonResponse(['product', 'productVariant']));

            return $wishlists;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function createWishlist(array $data): Wishlist
    {
        try {
            $wishlist =  Wishlist::create([
                'user_id' => $data['user_id'],
                'product_id' => $data['product_id'],
                'product_variant_id' => $data['product_variant_id'] ?? null,
                'note' => $data['note'] ?? null,
            ]);
            return $wishlist;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function deleteWishlist(int $id): bool
    {
        try {
            $wishlist = Wishlist::find($id);
            if (!$wishlist) abort(404, 'No wishlist found');
            $wishlist->delete();
            return true;
        } catch (Exception $e) {
            throw ($e);
        }
    }
}
