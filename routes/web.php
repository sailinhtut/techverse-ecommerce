<?php

use App\Inventory\Models\Product;
use App\Inventory\Models\ProductVariant;
use App\Order\Models\Order;
use App\Review\Models\ProductReview;
use App\Review\Models\ProductReviewReply;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

Route::get('/debug', function () {
    return view('_');
});

Route::post('/debug', function (Request $request) {

    $big_data = Cache::remember('big_data', 60, fn()=> ['name' => 'Sai Lin Htut', 'email' => 'sailinhtut25220@gmail.com']);

    return response()->json(['status' => 'success', 'message' => $big_data]);
});

require __DIR__ . '/app/inventory_routes.php';
require __DIR__ . '/app/auth_routes.php';
require __DIR__ . '/app/core_routes.php';
require __DIR__ . '/app/payment_routes.php';
require __DIR__ . '/app/web_routes.php';
require __DIR__ . '/app/admin_routes.php';
