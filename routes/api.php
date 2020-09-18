<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('api.')->group(function () {
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/download/{file_url}', [DownloadController::class, 'getFile'])->name('download.getFile');
});
