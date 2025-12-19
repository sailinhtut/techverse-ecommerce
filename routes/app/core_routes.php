<?php

use App\Core\Controllers\StorageManager;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/storage-manager', [StorageManager::class, 'show']);
    Route::get('/storage-manager/list', [StorageManager::class, 'list']);
    Route::delete('/storage-manager/delete', [StorageManager::class, 'delete']);
    Route::post('/storage-manager/rename', [StorageManager::class, 'rename']);
    Route::post('/storage-manager/upload', [StorageManager::class, 'upload']);
    Route::post('/storage-manager/create-folder', [StorageManager::class, 'createFolder']);
});
