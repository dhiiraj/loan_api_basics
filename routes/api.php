<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanController;
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

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});
Route::group([
    'middleware' => 'jwt.verify',
], function ($router) {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/apply', [LoanController::class, 'loanApply']);
    Route::post('/paynow', [LoanController::class, 'makePayment']);
    Route::post('/loanStatus', [LoanController::class, 'updateStatus']);
});
