<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['domain' => env('APP_URL_PAYMENT')], function () {
    Route::group(['as' => 'payment.'], function () {
        Route::get('/', [\App\Http\Controllers\Payment\V1\IndexController::class, 'index'])->name('index.index');
        Route::match(['post', 'get'], '/transaction/payment-method/{hash}', [\App\Http\Controllers\Payment\V1\TransactionController::class, 'paymentMethod'])->name('transaction.payment-method');
        Route::get('/transaction/{bank}/{hash}', [\App\Http\Controllers\Payment\V1\TransactionController::class, 'paymentBank'])->name('transaction.payment-bank');
        Route::get('/transaction/error', [\App\Http\Controllers\Payment\V1\TransactionController::class, 'error'])->name('transaction.error');
        Route::get('/transaction/check-complete', [\App\Http\Controllers\Payment\V1\TransactionController::class, 'checkComplete'])->name('transaction.check-complete');
    });
});

