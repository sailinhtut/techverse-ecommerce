<?php

use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

Route::view('/debug', '_');
Route::post('/debug', function (Request $request) {
    $request->session()->regenerate();
    throw new TokenMismatchException();
});

Route::get('/signed-test/{param}', function ($param) {
    return $param;
})->name('signed-test')->middleware('signed');

require __DIR__ . '/app/inventory_routes.php';
require __DIR__ . '/app/auth_routes.php';
require __DIR__ . '/app/order_routes.php';
require __DIR__ . '/app/core_routes.php';
require __DIR__ . '/app/payment_routes.php';
require __DIR__ . '/app/web_routes.php';
require __DIR__ . '/app/admin_routes.php';
