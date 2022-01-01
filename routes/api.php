<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [UserController::class, 'authenticate']);
Route::post('/create_user', [UserController::class, 'register']);

Route::group(['middleware' => 'jwt.verify'], function() {
    Route::get('/balance_read', [TransactionController::class, 'balanceRead']);
    Route::get('/top_transactions_per_user', [TransactionController::class, 'topTransactionsUser']);
    Route::get('/top_users', [TransactionController::class, 'topUsersTransaction']);
    Route::post('/transfer', [TransactionController::class, 'transfer']);
    Route::post('/balance_topup', [TransactionController::class, 'balanceTopup']);
});