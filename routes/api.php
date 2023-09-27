<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\TokenAuthMiddleware;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\WalletController;
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

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('user/login', [UserController::class, 'login']);

    Route::post('user/register', [UserController::class, 'register']);

    Route::get('user/account/{id}', [UserController::class, 'show'])
        ->middleware(TokenAuthMiddleware::class);

    Route::get('item/list', [ItemController::class, 'list']);

    Route::get('item/show/{id}', [ItemController::class, 'show']);

    Route::post('item/create', [ItemController::class, 'create'])
        ->middleware(TokenAuthMiddleware::class);

    Route::post('bid/register', [BidController::class, 'register'])
        ->middleware(TokenAuthMiddleware::class);

    Route::post('wallet/transfer', [WalletController::class, 'transfer'])
        ->middleware(TokenAuthMiddleware::class);


