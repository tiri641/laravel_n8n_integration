<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiAuthenticate;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ------------------------------------------------------------------
// 認証ルート (Auth Routes): ログインと登録
// ------------------------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']); 
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


// ------------------------------------------------------------------
// 商品ルート (Product Routes)
// ------------------------------------------------------------------

// 【パブリックアクセス】: 認証なしでアクセス可能
// 商品一覧の取得
Route::get('/products', [ProductController::class, 'index']);
// 個別商品の閲覧
Route::get('/products/{id}', [ProductController::class, 'show']);


// 【認証済みアクセス】: 有効なAPIトークンが必須 (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // 商品を作成 (トークン必須)
    Route::post('/products', [ProductController::class, 'store']);
    // 商品を更新 (トークン必須)
    Route::put('/products/{id}', [ProductController::class, 'update']);
    // 商品を論理削除 (トークン必須)
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    // 商品を物理削除 (トークン必須)
    Route::delete('/products/{id}/force', [ProductController::class, 'forceDestroy']);
    // 削除済み商品を復元 (トークン必須)
    Route::patch('/products/{id}/restore', [ProductController::class, 'restore']);
});