<?php

namespace App\Review\Controllers;

use App\Order\Models\OrderProduct;
use App\Review\Models\ProductReview;
use App\Review\Models\ProductReviewReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProductReviewController
{
    public function viewAdminReviewListPage()
    {
        try {
            $reviews = ProductReview::orderBy('updated_at', 'desc')
                ->paginate(10);

            $reviews->getCollection()->transform(function ($review) {
                return $review->jsonResponse(['product', 'user_full', 'order']);
            });

            return view('pages.admin.dashboard.review.review_list', [
                'reviews' => $reviews,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminReviewDetailPage(Request $request, $review_id)
    {
        try {
            $review = ProductReview::find($review_id);

            if (!$review) abort(404, "No Review Found");

            $review = $review->jsonResponse(['product', 'user_full', 'order']);

            $replies = ProductReviewReply::where('review_id', $review_id)
                ->orderBy('updated_at', 'desc')
                ->paginate(10);

            $replies->getCollection()->transform(function ($reply) {
                return $reply->jsonResponse(['user']);
            });

            return view('pages.admin.dashboard.review.review_detail', [
                'review' => $review,
                'replies' => $replies,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function fetchProductReviews(Request $request, $product_id)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $reviews = ProductReview::where('product_id', $product_id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $reviews->getCollection()->transform(fn($r) => $r->jsonResponse(['user', 'replies']));

            return response()->json($reviews);
        } catch (Exception $e) {
            handleErrors($e);
        }
    }

    public function createReview(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'nullable|exists:users,id',
                'product_id' => 'required|exists:products,id',
                'order_id' => 'nullable|exists:orders,id',
                'rating' => 'required|numeric|min:0|max:5',
                'comment' => 'nullable|string',
                'image' => 'nullable|image|max:2048',
            ]);

            if ($validator->fails()) {
                return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
            }

            $validated = $validator->validated();
            $userId = $validated['user_id'] ?? auth()->id();

            $firstOrderProduct = OrderProduct::whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('product_id', $validated['product_id'])
                ->orderBy('id', 'asc')
                ->first();

            $orderId = $firstOrderProduct->order_id ?? ($validated['order_id'] ?? null);
            $isApproved = $firstOrderProduct ? true : false;

            // Check if review already exists
            $review = ProductReview::where('product_id', $validated['product_id'])
                ->where('user_id', $userId)
                ->first();

            $imagePath = $review->image ?? null;
            if ($request->hasFile('image')) {
                if ($review && $review->image) {
                    Storage::disk('public')->delete($review->image);
                }
                $imagePath = Storage::disk('public')->putFile('products/reviews', $request->file('image'));
            }

            if ($review) {
                $review->update([
                    'order_id' => $orderId,
                    'rating' => $validated['rating'],
                    'comment' => $validated['comment'] ?? $review->comment,
                    'image' => $imagePath,
                    'is_approved' => $isApproved,
                ]);
            } else {
                ProductReview::create([
                    'product_id' => $validated['product_id'],
                    'user_id' => $userId,
                    'order_id' => $orderId,
                    'rating' => $validated['rating'],
                    'comment' => $validated['comment'] ?? null,
                    'image' => $imagePath,
                    'is_approved' => $isApproved,
                ]);
            }

            return redirect()->back()->with('success', 'Review submitted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }



    public function updateReview(Request $request, $id)
    {
        try {
            $review = ProductReview::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'user_id' => 'nullable|exists:users,id',
                'product_id' => 'required|exists:products,id',
                'order_id' => 'nullable|exists:orders,id',
                'rating' => 'nullable|numeric|min:0|max:5',
                'comment' => 'nullable|string',
                'is_approved' => 'nullable|boolean',
                'image' => 'nullable|image|max:2048',
                'remove_image' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
            }

            $validated = $validator->validated();

            if ($request->boolean('remove_image')) {
                if ($review->image) {
                    Storage::disk('public')->delete($review->image);
                }
                $review->image = null;
            }

            if ($request->hasFile('image')) {
                if ($review->image && Storage::disk('public')->exists($review->image)) {
                    Storage::disk('public')->delete($review->image);
                }
                $review->image = Storage::disk('public')->putFile('products/reviews', $request->file('image'));
            }

            $review->fill([
                'product_id' => $validated['product_id'],
                'user_id' => $validated['user_id'] ?? auth()->id(),
                'order_id' => $validated['order_id'] ?? null,
                'rating' => $validated['rating'] ?? $review->rating,
                'comment' => $validated['comment'],
                'is_approved' => $validated['is_approved'] ?? $review->is_approved,
            ]);

            $review->save();

            return redirect()->back()->with('success', 'Review updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteReview($id)
    {
        try {
            $review = ProductReview::findOrFail($id);

            if ($review->image) {
                Storage::disk('public')->delete($review->image);
            }

            $review->delete();

            return redirect()->back()->with('success', 'Review deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
