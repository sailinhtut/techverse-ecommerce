<?php

use Illuminate\Support\Facades\Route;

Route::view('/debug', '__');
Route::get('/test', function () {});

require __DIR__ . '/app/inventory_routes.php';
require __DIR__ . '/app/auth_routes.php';
require __DIR__ . '/app/order_routes.php';
require __DIR__ . '/app/core_routes.php';
require __DIR__ . '/app/payment_routes.php';
