<?php

namespace App\Review\Controllers;

use App\Review\Models\ProductReview;
use App\Review\Models\ProductReviewReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProductReviewReplyController
{
   

    public function createReviewReply(Request $request, $review_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'review_id' => 'required|exists:product_reviews,id',
                'reply' => 'required|string|max:2000',
            ]);

            if ($validator->fails()) {
                return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
            }

            $validated = $validator->validated();

            ProductReviewReply::create([
                'review_id' => $validated['review_id'],
                'user_id' => auth()->id(),
                'reply' => $validated['reply'],
            ]);

            return redirect()->back()->with('success', 'Reply added successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateReviewReply(Request $request, $review_id, $reply_id)
    {
        try {
            $reply = ProductReviewReply::findOrFail($reply_id);

            $validator = Validator::make($request->all(), [
                'reply' => 'nullable|string|max:2000',
            ]);

            if ($validator->fails()) {
                return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
            }

            $validated = $validator->validated();

            $reply->reply = $validated['reply'] ?? $reply->reply;
            $reply->save();

            return redirect()->back()->with('success', 'Reply updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteReviewReply($review_id, $reply_id)
    {
        try {
            $reply = ProductReviewReply::findOrFail($reply_id);
            $reply->delete();

            return redirect()->back()->with('success', 'Reply deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
