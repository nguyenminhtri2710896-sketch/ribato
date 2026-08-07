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

Route::group(['as' => 'api.'], function () {
    Route::match(['post', 'get'], '/test/index', [\App\Http\Controllers\Backend\V1\TestController::class, 'index'])->name('test.index');
    Route::match(['post', 'get'], '/test/test', [\App\Http\Controllers\Backend\V1\TestController::class, 'test'])->name('test.test');
    Route::match(['post', 'get'], '/test/callback', [\App\Http\Controllers\Backend\V1\TestController::class, 'callback'])->name('test.callback');


    Route::match(['post', 'get'], '/', [\App\Http\Controllers\Api\V1\IndexController::class, 'index'])->name('index.index');

    Route::match(['post', 'get'], '/app-message/noti', [\App\Http\Controllers\Api\V1\AppMessageController::class, 'noti'])->name('app-message.add-noti');
    Route::match(['post', 'get'], '/app-message/sms', [\App\Http\Controllers\Api\V1\AppMessageController::class, 'sms'])->name('app-message.add-sms');
    Route::match(['post', 'get'], '/app-message/yoobil', action: [\App\Http\Controllers\Api\V1\AppMessageController::class, 'yoobil'])->name('app-message.add-yoobil');
    // Route::match(['post', 'get'], '/app-message/neox', action: [\App\Http\Controllers\Api\V1\AppMessageController::class, 'neox'])->name('app-message.add-neox');
    /**
     * Dùng cho bank xài trực tiếp ko qua hệ thống
     */
    Route::match(['post', 'get'], '/app-message/noti-forward', [\App\Http\Controllers\Api\V1\AppMessageController::class, 'notiForward'])->name('app-message.add-noti-forward');
    Route::match(['post', 'get'], '/app-message/sms-forward', [\App\Http\Controllers\Api\V1\AppMessageController::class, 'smsForward'])->name('app-message.add-sms-forward');
    Route::match(['post', 'get'], '/app-message/yoobil-forward', [\App\Http\Controllers\Api\V1\AppMessageController::class, 'yoobilForward'])->name('app-message.add-yoobil-forward');
    Route::match(['post', 'get'], '/withdraw-yoobil/callback', [\App\Http\Controllers\Api\V1\WithdrawYoobilLogController::class, 'callback'])->name('withdraw-yoobil.callback');

    Route::match(['post', 'get'], '/app-message/gpay-forward', [\App\Http\Controllers\Api\V1\AppMessageController::class, 'gpayForward'])->name('app-message.add-gpay-forward');
    Route::match(['post', 'get'], '/app-message/neox-forward', [\App\Http\Controllers\Api\V1\AppMessageController::class, 'neoxForward'])->name('app-message.add-neox-forward');
    Route::match(['post', 'get'], '/app-message/paymenthot-forward', [\App\Http\Controllers\Api\V1\AppMessageController::class, 'paymenthotForward'])->name('app-message.add-paymenthot-forward');
    Route::match(['post', 'get'], '/app-message/paymenthot-payout', [\App\Http\Controllers\Api\V1\AppMessageController::class, 'paymenthotPayout'])->name('app-message.paymenthot-payout');
    Route::match(['post', 'get'], '/app-message/ribato-gpay-forward', [\App\Http\Controllers\Api\V1\AppMessageController::class, 'ribatoGpayForward'])->name('app-message.add-ribato-gpay-forward');

    /**
     * Version mới
     */
   Route::match(['post', 'get'], '/ipn/noti-forward', [\App\Http\Controllers\Api\V1\IpnController::class, 'notiForward'])->name('ipn.noti-forward');

    Route::match(['post', 'get'], '/ipn/seapay-collection', [\App\Http\Controllers\Api\V1\IpnController::class, 'seapayCollection'])->name('ipn.seapay-collection');


    Route::match(['post', 'get'], '/ipn/yoobil-collection', [\App\Http\Controllers\Api\V1\IpnController::class, 'yoobilCollection'])->name('ipn.yoobil-collection');
    Route::match(['post', 'get'], '/ipn/yoobil-payout', [\App\Http\Controllers\Api\V1\IpnController::class, 'yoobilPayout'])->name('ipn.yoobil-payout');

    Route::match(['post', 'get'], '/ipn/paymenthot-collection', [\App\Http\Controllers\Api\V1\IpnController::class, 'paymenthotCollection'])->name('ipn.paymenthot-collection');
    Route::match(['post', 'get'], '/ipn/paymenthot-payout', [\App\Http\Controllers\Api\V1\IpnController::class, 'paymenthotPayout'])->name('ipn.paymenthot-payout');


    Route::match(['post', 'get'], '/ipn/paymenthot-forward-v2', [\App\Http\Controllers\Api\V1\IpnController::class, 'paymenthotCollectionV2'])->name('app-message.add-paymenthot-forward-v2');
    Route::match(['post', 'get'], '/ipn/paymenthot-payout-v2', [\App\Http\Controllers\Api\V1\IpnController::class, 'paymenthotPayoutV2'])->name('app-message.paymenthot-payout-v2');


    Route::match(['post', 'get'], '/ipn/gpay-collection', [\App\Http\Controllers\Api\V1\IpnController::class, 'gpayCollection'])->name('ipn.gpay-collection');
    Route::match(['post', 'get'], '/ipn/gpay-payout', [\App\Http\Controllers\Api\V1\IpnController::class, 'gpayPayout'])->name('ipn.gpay-payout');

    Route::match(['post', 'get'], '/ipn/neox-collection', [\App\Http\Controllers\Api\V1\IpnController::class, 'neoxCollection'])->name('ipn.neox-collection');
    Route::match(['post', 'get'], '/ipn/neox-payout', [\App\Http\Controllers\Api\V1\IpnController::class, 'neoxPayout'])->name('ipn.neox-payout');
    Route::match(['post', 'get'], '/other/get-bank-account-name', [\App\Http\Controllers\Api\V1\OtherController::class, 'getBankAccountName'])->name('other.get-bank-account-name');


    Route::middleware(['auth:api'])->group(function () {
        Route::match(['post', 'get'], '/report/get-total-transaction-amount', [\App\Http\Controllers\Api\V1\ReportController::class, 'getTotalTransactionAmount'])->name('report.get-total-transaction-amount');
        Route::match(['post', 'get'], '/transaction/create-payment-base', [\App\Http\Controllers\Api\V1\TransactionController::class, 'createPaymentBase'])->name('bank-transaction.create-payment-base');
        Route::match(['post', 'get'], '/report/get-revenue-paymenthot', [\App\Http\Controllers\Api\V1\ReportController::class, 'getRevenuePaymenthot'])->name('report.get-revenue-paymenthot');


        Route::middleware(['signiture.auth'])->group(function () {
            Route::match(['post', 'get'], '/transaction/create-payment', [\App\Http\Controllers\Api\V1\TransactionController::class, 'createPayment'])->name('transaction.create-payment');
            Route::match(['post', 'get'], '/transaction/get-list', [\App\Http\Controllers\Api\V1\TransactionController::class, 'getList'])->name('transaction.get-list');
            Route::match(['post', 'get'], '/transaction/get-detail', [\App\Http\Controllers\Api\V1\TransactionController::class, 'getDetail'])->name('transaction.get-detail');
            Route::match(['post', 'get'], '/transaction/create-qr-payment', [\App\Http\Controllers\Api\V1\TransactionController::class, 'createQrPayment'])->name('transaction.create-qr-payment');
            Route::match(['post', 'get'], '/report/qrcode-revenue', [\App\Http\Controllers\Api\V1\ReportController::class, 'qrcodeRevenue'])->name('report.qrcode-revenue');


            Route::match(['post', 'get'], '/user-id-qrcode/va-get-list', [\App\Http\Controllers\Api\V1\UserIdQrcodeController::class, 'vaGetList'])->name('user-id-qrcode.ajax-va-get-list');
            Route::match(['post', 'get'], '/user-id-qrcode/get-list', [\App\Http\Controllers\Api\V1\UserIdQrcodeController::class, 'getList'])->name('user-id-qrcode.ajax-get-list');
            Route::match(['post', 'get'], '/user-id-qrcode/create', [\App\Http\Controllers\Api\V1\UserIdQrcodeController::class, 'add'])->name('user-id-qrcode.ajax-add');
            /**
             * 
             */
            // user-withdraw/index
            Route::match(['post', 'get'], '/bank/get-list', [\App\Http\Controllers\Api\V1\BankController::class, 'getList'])->name('bank.get-list');
            Route::match(['post', 'get'], '/user-withdraw/get-list', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'getList'])->name('user-withdraw.get-list');
            Route::match(['post', 'get'], '/user-withdraw/create', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'add'])->name('user-withdraw.create');

            /**
             * Account
             */
            Route::match(['post', 'get'], '/account/get-balance', [\App\Http\Controllers\Api\V1\AccountController::class, 'getBalance'])->name('account.get-balance');
            Route::match(['post', 'get'], '/account/create-qr-payment', [\App\Http\Controllers\Api\V1\AccountController::class, 'createQrPayment'])->name('account.create-qr-payment');


             Route::match(['post', 'get'], '/user-virtual-account/get-list', [\App\Http\Controllers\Api\V1\UserVirtualAccountController::class, 'getList'])->name('user-virtual-account.get-list');

            // Route::match(['post', 'get'], '/report/get-total-transaction-amount', [\App\Http\Controllers\Api\V1\ReportController::class, 'getTotalTransactionAmount'])->name('report.get-total-transaction-amount');

        });

        Route::middleware(['checksum.auth'])->prefix('v2')->group(function () {
            Route::match(['post', 'get'], '/transaction/get-list', [\App\Http\Controllers\Api\V2\TransactionController::class, 'getList'])->name('transaction.get-list-v2');
            Route::match(['post', 'get'], '/transaction/get-detail', [\App\Http\Controllers\Api\V2\TransactionController::class, 'getDetail'])->name('transaction.get-detail-v2');
            Route::match(['post', 'get'], '/bank/get-list', [\App\Http\Controllers\Api\V2\BankController::class, 'getList'])->name('bank.get-list-v2');
            Route::match(['post', 'get'], '/user-withdraw/get-list', [\App\Http\Controllers\Api\V2\UserWithdrawController::class, 'getList'])->name('user-withdraw.get-list-v2');
            Route::match(['post', 'get'], '/user-withdraw/create', [\App\Http\Controllers\Api\V2\UserWithdrawController::class, 'add'])->name('user-withdraw.create-v2');
        });
    });
});