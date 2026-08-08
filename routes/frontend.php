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

$strRouterQRcode = function () {
    Route::group(['as' => 'frontend.'], function () {

        Route::middleware(['XSS'])->group(function () {
            Route::match(['post', 'get'], '/{bank}-{code}.png', [\App\Http\Controllers\Frontend\V1\QrcodeController::class, 'index'])->name('qrcode.index');
        });
    });
};

Route::group(['domain' => env('APP_URL_QRCODE')], $strRouterQRcode);

Route::group(['domain' => env('APP_URL_HOME')], function () {
    Route::middleware(['XSS', 'payment.gate'])->group(function () {

        Route::group(['as' => 'frontend.'], function () {

            Route::middleware(['XSS'])->group(function () {
                Route::match(['post', 'get'], '/', [\App\Http\Controllers\Frontend\V1\IndexController::class, 'index'])->name('index.index');
                Route::post('/contact', [\App\Http\Controllers\Frontend\V1\IndexController::class, 'contact'])->name('contact.submit');
            });
        });
    });
});


Route::group(['domain' => 'doc-paypay.com'], function () {
    Route::get('/', [\App\Http\Controllers\Backend\V1\DocController::class, 'index'])->name('doc.paypay.index');
    Route::get('/payout', [\App\Http\Controllers\Backend\V1\DocController::class, 'payout'])->name('doc.paypay.payout');
    Route::get('/collection', [\App\Http\Controllers\Backend\V1\DocController::class, 'collection'])->name('doc.paypay.collection');
    Route::get('/colletion', [\App\Http\Controllers\Backend\V1\DocController::class, 'collection'])->name('doc.paypay.colletion');
});

Route::group(['domain' => 'www.doc-paypay.com'], function () {
    Route::get('/', [\App\Http\Controllers\Backend\V1\DocController::class, 'index'])->name('www.doc.paypay.index');
    Route::get('/payout', [\App\Http\Controllers\Backend\V1\DocController::class, 'payout'])->name('www.doc.paypay.payout');
    Route::get('/collection', [\App\Http\Controllers\Backend\V1\DocController::class, 'collection'])->name('www.doc.paypay.collection');
    Route::get('/colletion', [\App\Http\Controllers\Backend\V1\DocController::class, 'collection'])->name('www.doc.paypay.colletion');
});

Route::group(['as' => 'frontend.'], function () {
    Route::middleware(['XSS'])->group(function () {

    });
});

