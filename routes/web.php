<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/debug', '_');
Route::post('/debug', function (Request $request) {});

require __DIR__ . '/app/auth_routes.php';
require __DIR__ . '/app/core_routes.php';
require __DIR__ . '/app/web_routes.php';
require __DIR__ . '/app/admin_routes.php';
