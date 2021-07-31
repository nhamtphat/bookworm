<?php

use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\FilterController;
use App\Http\Controllers\API\HomepageController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\ShopController;
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

Route::get('books/filters', [BookController::class, 'filters']);
Route::apiResource('books', BookController::class)->only('index', 'show');

Route::get('reviews/filters', [ReviewController::class, 'filters']);
Route::apiResource('books.reviews', ReviewController::class)->shallow()->only('index', 'store');

Route::apiResource('orders', OrderController::class)->only('store');
