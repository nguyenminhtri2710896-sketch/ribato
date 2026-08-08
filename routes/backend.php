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

$strRouteBackend = function () {
    Route::group(['as' => 'backend.'], function () {
        Route::middleware(['XSS', 'payment.gate'])->group(function () {

            Route::get('/test/index', [\App\Http\Controllers\Backend\V1\TestController::class, 'index'])->name('test.index');
            Route::match(['post', 'get'], '/vr/{hash?}', [\App\Http\Controllers\Backend\V1\ConfirmWithdrawController::class, 'verify'])->name('confirm-withdraw.verify');
            Route::match(['post', 'get'], '/wl/{hash?}', [\App\Http\Controllers\Backend\V1\ConfirmWithdrawController::class, 'index'])->name('confirm-withdraw.index');
            Route::match(['post', 'get'], '/upload/image', [\App\Http\Controllers\Api\V1\UploadController::class, 'image'])->name('upload.image');

            Route::match(['post', 'get'], '/confirm-withdraw/ajax/confirm', [\App\Http\Controllers\Backend\V1\ConfirmWithdrawController::class, 'ajaxConfirm'])->name('confirm-withdraw.ajax-confirm');
            Route::match(['post', 'get'], '/confirm-withdraw/ajax/cancel', [\App\Http\Controllers\Backend\V1\ConfirmWithdrawController::class, 'ajaxCancel'])->name('confirm-withdraw.ajax-cancel');


            Route::get('/tool', [\App\Http\Controllers\Backend\V1\ToolController::class, 'index'])->name('tool.index');
            Route::get('/tool/create-sign', [\App\Http\Controllers\Backend\V1\ToolController::class, 'createSign'])->name('tool.create-sign');
            Route::match(['post', 'get'], '/tool/ajax-create-sign', [\App\Http\Controllers\Backend\V1\ToolController::class, 'ajaxCreateSign'])->name('tool.ajax-create-sign');



            // confirm-withdraw.ajax-confirm'
            Route::get('/auth/sign-in', [\App\Http\Controllers\Backend\V1\AuthController::class, 'signIn'])->name('auth.sign-in');
            Route::match(['post', 'get'], '/auth/ajax-sign-in', [\App\Http\Controllers\Backend\V1\AuthController::class, 'ajaxSignIn'])->name('auth.ajax-sign-in');
            Route::get('/auth/sign-out', [\App\Http\Controllers\Backend\V1\AuthController::class, 'signOut'])->name('auth.sign-out');

            // Public API documentation routes
            if (config('app.backend_version') == "v2") {
                Route::get('/doc', [\App\Http\Controllers\Backend\V1\DocController::class, 'index'])->name('doc.index');
                Route::get('/doc/payout', [\App\Http\Controllers\Backend\V1\DocController::class, 'payout'])->name('doc.payout');
                Route::get('/doc/collection', [\App\Http\Controllers\Backend\V1\DocController::class, 'collection'])->name('doc.collection');
                Route::get('/doc/colletion', [\App\Http\Controllers\Backend\V1\DocController::class, 'collection'])->name('doc.colletion');
            }

            Route::middleware(['auth'])->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\V1\IndexController::class, 'index'])->name('index.index');
                Route::get('/lang/{locale}', [\App\Http\Controllers\Backend\V1\IndexController::class, 'lang'])->name('index.lang');

                Route::get('/account', [\App\Http\Controllers\Backend\V1\AccountController::class, 'index'])->name('account.index');
                Route::get('/account/change-password', [\App\Http\Controllers\Backend\V1\AccountController::class, 'changePassword'])->name('account.change-password');
                Route::get('/account/update-profile', [\App\Http\Controllers\Backend\V1\AccountController::class, 'updateProfile'])->name('account.update-profile');
                Route::get('/account/update-authy-2factor', [\App\Http\Controllers\Backend\V1\AccountController::class, 'updateAuthy2Factor'])->name('account.update-authy-2factor');

                /**
                 * View
                 */
                Route::get('/transaction/index', [\App\Http\Controllers\Backend\V1\TransactionController::class, 'index'])->name('transaction.index');
                Route::match(['post', 'get'], '/transaction/ajax/get-list', [\App\Http\Controllers\Api\V1\TransactionController::class, 'getList'])->name('transaction.ajax-get-list');
                Route::match(['post', 'get'], '/transaction/ajax/create-qr-payment', [\App\Http\Controllers\Api\V1\TransactionController::class, 'createQrPayment'])->name('transaction.ajax-create-qr-payment');
                Route::match(['post', 'get'], '/account/ajax/create-qr-payment', [\App\Http\Controllers\Api\V1\AccountController::class, 'createQrPayment'])->name('account.ajax-create-qr-payment');
                Route::match(['post', 'get'], '/transaction/ajax/export-excel', [\App\Http\Controllers\Api\V1\TransactionController::class, 'exportExcel'])->name('transaction.ajax-export-excel');



                Route::get('/user-bank-account/index', [\App\Http\Controllers\Backend\V1\UserBankAccountController::class, 'index'])->name('user-bank-account.index');
                Route::match(['post', 'get'], '/user-bank-account/ajax/select2-get-list', [\App\Http\Controllers\Api\V1\UserBankAccountController::class, 'select2GetList'])->name('user-bank-account.ajax-select2-get-list');

                Route::get('/user-transaction/index', [\App\Http\Controllers\Backend\V1\UserTransactionController::class, 'index'])->name('user-transaction.index');
                Route::get('/user-withdraw/index', [\App\Http\Controllers\Backend\V1\UserWithdrawController::class, 'index'])->name('user-withdraw.index');
                Route::get('/ipn/collection', [\App\Http\Controllers\Backend\V1\IpnController::class, 'collection'])->name('ipn.collection');
                Route::get('/ipn/payout', [\App\Http\Controllers\Backend\V1\IpnController::class, 'payout'])->name('ipn.payout');

                /**
                 * AJAX sử dụng cotroller API
                 */
                Route::match(["GET", "POST"], '/ajax/sign-out', [\App\Http\Controllers\Api\V1\AuthController::class, 'signOut'])->name('auth.ajax-sign-out');
                Route::match(['post', 'get'], '/account/ajax/get-info', [\App\Http\Controllers\Api\V1\AccountController::class, 'getInfo'])->name('account.ajax-getInfo');
                Route::match(['post', 'get'], '/account/ajax/change-password', [\App\Http\Controllers\Api\V1\AccountController::class, 'changePassword'])->name('account.ajax-change-password');
                Route::match(['post', 'get'], '/account/ajax/change-password-sales', [\App\Http\Controllers\Api\V1\AccountController::class, 'changePasswordSales'])->name('account.ajax-change-password-sales');
                Route::match(['post', 'get'], '/account/ajax/create-password-sales', [\App\Http\Controllers\Api\V1\AccountController::class, 'createPasswordSales'])->name('account.ajax-create-password-sales');
                Route::match(['post', 'get'], '/account/ajax/cancel-password-sales', [\App\Http\Controllers\Api\V1\AccountController::class, 'cancelPasswordSales'])->name('account.ajax-cancel-password-sales');
                Route::match(['post', 'get'], '/account/ajax/get-balance', [\App\Http\Controllers\Api\V1\AccountController::class, 'getBalance'])->name('account.ajax-get-balance');
                Route::match(['post', 'get'], '/account/ajax/update-info', [\App\Http\Controllers\Api\V1\AccountController::class, 'updateInfo'])->name('account.ajax-update-info');
                Route::match(['post', 'get'], '/account/ajax/get-authy-2factor', [\App\Http\Controllers\Api\V1\AccountController::class, 'getAuthy2Factor'])->name('account.ajax-get-authy-2factor');
                Route::match(['post', 'get'], '/account/ajax/validate-authy-2factor', [\App\Http\Controllers\Api\V1\AccountController::class, 'validateAuthy2Factor'])->name('account.ajax-validate-authy-2factor');
                Route::match(['post', 'get'], '/account/ajax/cancel-authy-2factor', [\App\Http\Controllers\Api\V1\AccountController::class, 'cancelAuthy2Factor'])->name('account.ajax-cancel-authy-2factor');
                Route::match(['post', 'get'], '/account/ajax/request-otp-withdraw', [\App\Http\Controllers\Api\V1\AccountController::class, 'requestOtpWithdraw'])->name('account.ajax-request-otp-withdraw');




                Route::match(['post', 'get'], '/account/ajax/create-or-update-api-token', [\App\Http\Controllers\Api\V1\AccountController::class, 'createOrUpdateApiToken'])->name('account.ajax-create-or-update-api-token');
                Route::match(['post', 'get'], '/account/ajax/change-language', [\App\Http\Controllers\Api\V1\AccountController::class, 'changeLanguage'])->name('account.ajax-change-language');
                Route::match(['post', 'get'], '/account/ajax/update-image-avatar', [\App\Http\Controllers\Api\V1\AccountController::class, 'updateImageAvatar'])->name('account.ajax-update-image-avatar');
                Route::match(['post', 'get'], '/account/ajax/update-image-cover', [\App\Http\Controllers\Api\V1\AccountController::class, 'updateImageCover'])->name('account.ajax-update-image-cover');
                Route::match(['post', 'get'], '/account/ajax/transfer-balance', [\App\Http\Controllers\Api\V1\AccountController::class, 'transferBalance'])->name('account.ajax-transfer-balance');
                Route::match(['post', 'get'], '/account/ajax/get-notificaiton', [\App\Http\Controllers\Api\V1\AccountController::class, 'getNotification'])->name('account.ajax-get-notificaiton');
                Route::match(['post', 'get'], '/account/ajax/read-notificaiton', [\App\Http\Controllers\Api\V1\AccountController::class, 'readNotification'])->name('account.ajax-read-notificaiton');
                Route::match(['post', 'get'], '/account/ajax/get-list-signin-logs', [\App\Http\Controllers\Api\V1\AccountController::class, 'getListSignInLogs'])->name('account.ajax-get-list-signin-logs');
                Route::match(['post', 'get'], '/account/ajax/transaction', [\App\Http\Controllers\Api\V1\AccountController::class, 'transaction'])->name('account.ajax-transaction');


                Route::get('/user-id-qrcode/index', [\App\Http\Controllers\Backend\V1\UserIdQrcodeController::class, 'index'])->name('user-id-qrcode.index');
                Route::match(['post', 'get'], '/user-id-qrcode/ajax/get-list', [\App\Http\Controllers\Api\V1\UserIdQrcodeController::class, 'getList'])->name('user-id-qrcode.ajax-get-list');
                Route::match(['post', 'get'], '/user-id-qrcode/ajax/get-detail', [\App\Http\Controllers\Api\V1\UserIdQrcodeController::class, 'getDetail'])->name('user-id-qrcode.ajax-get-detail');
                Route::match(['post', 'get'], '/user-id-qrcode/ajax/create', [\App\Http\Controllers\Api\V1\UserIdQrcodeController::class, 'add'])->name('user-id-qrcode.ajax-add');
                Route::match(['post', 'get'], '/user-id-qrcode/ajax/delete', [\App\Http\Controllers\Api\V1\UserIdQrcodeController::class, 'delete'])->name('user-id-qrcode.ajax-delete');
                Route::match(['post', 'get'], '/user-bank-account/qrcode', [\App\Http\Controllers\Api\V1\UserIdQrcodeController::class, 'qrcode'])->name('user-bank-account.qrcode');


                Route::match(['post', 'get'], '/user-transaction/ajax/get-list', [\App\Http\Controllers\Api\V1\UserTransactionController::class, 'getList'])->name('user-transaction.ajax-get-list');
                Route::match(['post', 'get'], '/user-transaction/ajax/export-excel', [\App\Http\Controllers\Api\V1\UserTransactionController::class, 'exportExcel'])->name('user-transaction.ajax-export-excel');

                Route::match(['post', 'get'], '/user-withdraw/ajax/get-list', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'getList'])->name('user-withdraw.ajax-get-list');
                Route::match(['post', 'get'], '/user-withdraw/ajax/get-detail', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'getDetail'])->name('user-withdraw.ajax-get-detail');
                Route::match(['post', 'get'], '/user-withdraw/ajax/add', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'add'])->name('user-withdraw.ajax-add');
                // Route::match(['post', 'get'], '/user-withdraw/ajax/add-v2', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'addV2'])->name('user-withdraw.ajax-add-v2'); // đã detect từ v1 rồi
                Route::match(['post', 'get'], '/user-withdraw/ajax/add-multible', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'addMultible'])->name('user-withdraw.ajax-add-multible');
                Route::match(['post', 'get'], '/user-withdraw/ajax/add-multible-check', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'addMultibleCheck'])->name('user-withdraw.ajax-add-multible-check');
                Route::match(['post', 'get'], '/user-withdraw/ajax/export-excel', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'exportExcel'])->name('user-withdraw.ajax-export-excel');
                Route::match(['post', 'get'], '/user-withdraw/ajax/create-bill', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'createBill'])->name('user-withdraw.ajax-create-bill');
                Route::match(['post', 'get'], '/ipn/collection/ajax/get-list', [\App\Http\Controllers\Api\V1\TransactionCallbackController::class, 'getList'])->name('ipn.collection.ajax-get-list');
                Route::match(['post', 'get'], '/ipn/collection/ajax/detail', [\App\Http\Controllers\Api\V1\TransactionCallbackController::class, 'detail'])->name('ipn.collection.ajax-detail');
                Route::match(['post', 'get'], '/ipn/collection/ajax/resend', [\App\Http\Controllers\Api\V1\TransactionCallbackController::class, 'resend'])->name('ipn.collection.ajax-resend');
                Route::match(['post', 'get'], '/ipn/payout/ajax/get-list', [\App\Http\Controllers\Api\V1\UserWithdrawCallbackController::class, 'getList'])->name('ipn.payout.ajax-get-list');
                Route::match(['post', 'get'], '/ipn/payout/ajax/detail', [\App\Http\Controllers\Api\V1\UserWithdrawCallbackController::class, 'detail'])->name('ipn.payout.ajax-detail');
                Route::match(['post', 'get'], '/ipn/payout/ajax/resend', [\App\Http\Controllers\Api\V1\UserWithdrawCallbackController::class, 'resend'])->name('ipn.payout.ajax-resend');


                Route::match(['post', 'get'], '/bank/ajax/get-list', [\App\Http\Controllers\Api\V1\BankController::class, 'getList'])->name('bank.ajax-get-list');
                Route::match(['post', 'get'], '/bank/ajax/ajax-select2-get-list', [\App\Http\Controllers\Api\V1\BankController::class, 'select2GetList'])->name('bank.ajax-select2-get-list');
                Route::match(['post', 'get'], '/report/ajax/get-total-transaction-amount', [\App\Http\Controllers\Api\V1\ReportController::class, 'getTotalTransactionAmount'])->name('report.ajax-get-total-transaction-amount');

                Route::match(['post', 'get'], '/user-token/ajax/update-public-key', [\App\Http\Controllers\Api\V1\UserTokenController::class, 'updatePublicKey'])->name('user-token.ajax-update-public-key');
                Route::match(['post', 'get'], '/user-token/ajax/update-webhook-url', [\App\Http\Controllers\Api\V1\UserTokenController::class, 'updateWebhookUrl'])->name('user-token.ajax-update-webhook-url');

                Route::get('/personal-token/index', [\App\Http\Controllers\Backend\V1\PersonalTokenController::class, 'index'])->name('personal-token.index');
                Route::match(['post', 'get'], '/personal-token/ajax/get-list', [\App\Http\Controllers\Api\V1\PersonalTokenController::class, 'getList'])->name('personal-token.ajax-get-list');
                Route::match(['post', 'get'], '/personal-token/ajax/add', [\App\Http\Controllers\Api\V1\PersonalTokenController::class, 'add'])->name('personal-token.ajax-add');
                Route::match(['post', 'get'], '/personal-token/ajax/delete', [\App\Http\Controllers\Api\V1\PersonalTokenController::class, 'delete'])->name('personal-token.ajax-delete');

                Route::get('/sub-user/index', [\App\Http\Controllers\Backend\V1\SubUserController::class, 'index'])->name('sub-user.index');
                Route::match(['post', 'get'], '/sub-user/ajax/get-list', [\App\Http\Controllers\Api\V1\SubUserController::class, 'getList'])->name('sub-user.ajax-get-list');
                Route::match(['post', 'get'], '/sub-user/ajax/get-detail', [\App\Http\Controllers\Api\V1\SubUserController::class, 'getDetail'])->name('sub-user.ajax-get-detail');
                Route::match(['post', 'get'], '/sub-user/ajax/add', [\App\Http\Controllers\Api\V1\SubUserController::class, 'add'])->name('sub-user.ajax-add');
                Route::match(['post', 'get'], '/sub-user/ajax/update', [\App\Http\Controllers\Api\V1\SubUserController::class, 'update'])->name('sub-user.ajax-update');
                Route::match(['post', 'get'], '/sub-user/ajax/delete', [\App\Http\Controllers\Api\V1\SubUserController::class, 'delete'])->name('sub-user.ajax-delete');

                Route::match(['post', 'get'], '/sub-user/ajax/get-list', [\App\Http\Controllers\Api\V1\SubUserController::class, 'getList'])->name('sub-user.ajax-get-list');
                Route::match(['post', 'get'], '/sub-user/ajax/get-detail', [\App\Http\Controllers\Api\V1\SubUserController::class, 'getDetail'])->name('sub-user.ajax-get-detail');
                Route::match(['post', 'get'], '/sub-user/ajax/add', [\App\Http\Controllers\Api\V1\SubUserController::class, 'add'])->name('sub-user.ajax-add');
                Route::match(['post', 'get'], '/sub-user/ajax/update', [\App\Http\Controllers\Api\V1\SubUserController::class, 'update'])->name('sub-user.ajax-update');
                Route::match(['post', 'get'], '/sub-user/ajax/delete', [\App\Http\Controllers\Api\V1\SubUserController::class, 'delete'])->name('sub-user.ajax-delete');

                Route::get('/virtual-account/index', [\App\Http\Controllers\Backend\V1\VirtualAccountController::class, 'index'])->name('virtual-account.index');
                Route::match(['post', 'get'], '/virtual-account/ajax/get-list', [\App\Http\Controllers\Api\V1\UserVirtualAccountController::class, 'getList'])->name('virtual-account.ajax-get-list');
                Route::match(['post', 'get'], '/virtual-account/ajax/ajax-select2-get-list', [\App\Http\Controllers\Api\V1\UserVirtualAccountController::class, 'select2GetList'])->name('virtual-account.ajax-select2-get-list');

                Route::get('/user-debit/index', [\App\Http\Controllers\Backend\V1\UserDebitController::class, 'index'])->name('user-debit.index');
                Route::match(['post', 'get'], '/user-debit/ajax/get-list', [\App\Http\Controllers\Api\V1\UserDebitController::class, 'getList'])->name('user-debit.ajax-get-list');

                Route::middleware(['accountant.auth:api'])->group(function () {
                    Route::get('/report/index', [\App\Http\Controllers\Backend\V1\ReportController::class, 'index'])->name('report.index');
                    Route::match(['post', 'get'], '/report/ajax-revenues-by-day', [\App\Http\Controllers\Api\V1\ReportController::class, 'revenuesByDay'])->name('report.ajax-revenues-by-day');
                    Route::match(['post', 'get'], '/report/ajax-revenues-by-month', [\App\Http\Controllers\Api\V1\ReportController::class, 'revenuesByMonth'])->name('report.ajax-revenues-by-month');
                    Route::match(['post', 'get'], '/report/ajax-profit-chart', [\App\Http\Controllers\Api\V1\ReportController::class, 'profitChart'])->name('report.ajax-profit-chart');

                    Route::get('/report/user', [\App\Http\Controllers\Backend\V1\ReportController::class, 'user'])->name('report.user');
                    Route::match(['post', 'get'], '/report/ajax/get-top-user', [\App\Http\Controllers\Api\V1\ReportController::class, 'getTopUser'])->name('report.ajax-get-top-user');
                    Route::match(['post', 'get'], '/report/ajax/get-system-balance', [\App\Http\Controllers\Api\V1\ReportController::class, 'getSystemBalance'])->name('report.ajax-get-system-balance');
                    Route::match(['post', 'get'], '/report/ajax-get-top-user-list', [\App\Http\Controllers\Api\V1\ReportController::class, 'getTopUser'])->name('report.ajax-get-top-user-list');

                    Route::get('/user-revenue-report/index', [\App\Http\Controllers\Backend\V1\UserRevenueReportController::class, 'index'])->name('user-revenue-report.index');
                    Route::match(['post', 'get'], '/user-revenue-report/ajax/get-list', [\App\Http\Controllers\Api\V1\UserRevenueReportController::class, 'getList'])->name('user-revenue-report.ajax-get-list');
                    Route::match(['post', 'get'], '/user-revenue-report/ajax/export-excel', [\App\Http\Controllers\Api\V1\UserRevenueReportController::class, 'exportExcel'])->name('user-revenue-report.ajax-export-excel');

                    Route::match(['post', 'get'], '/user-debit/ajax/get-detail', [\App\Http\Controllers\Api\V1\UserDebitController::class, 'getDetail'])->name('user-debit.ajax-get-detail');
                    Route::match(['post', 'get'], '/user-debit/ajax/add', [\App\Http\Controllers\Api\V1\UserDebitController::class, 'add'])->name('user-debit.ajax-add');
                    Route::match(['post', 'get'], '/user-debit/ajax/return', [\App\Http\Controllers\Api\V1\UserDebitController::class, 'return'])->name('user-debit.ajax-return');
                    Route::match(['post', 'get'], '/user-debit/ajax/delete', [\App\Http\Controllers\Api\V1\UserDebitController::class, 'delete'])->name(name: 'user-debit.ajax-delete');


                    Route::get('/user/index', [\App\Http\Controllers\Backend\V1\UserController::class, 'index'])->name('user.index');
                    Route::get('/user/detail', [\App\Http\Controllers\Backend\V1\UserController::class, 'detail'])->name('user.detail');
                    Route::match(['post', 'get'], '/user/ajax/get-list', [\App\Http\Controllers\Api\V1\UserController::class, 'getList'])->name('user.ajax-get-list');
                    Route::match(['post', 'get'], '/user/ajax/export-excel', [\App\Http\Controllers\Api\V1\UserController::class, 'ajaxExportExcel'])->name('user.ajax-export-excel');
                    Route::match(['post', 'get'], '/user/ajax/ajax-select2-get-list', [\App\Http\Controllers\Api\V1\UserController::class, 'select2GetList'])->name('user.ajax-select2-get-list');

                    Route::get('/gateway-account/index', [\App\Http\Controllers\Backend\V1\GatewayAccountController::class, 'index'])->name('gateway-account.index');
                    Route::match(['post', 'get'], '/gateway-account/ajax/get-list', [\App\Http\Controllers\Api\V1\GatewayAccountController::class, 'getList'])->name('gateway-account.ajax-get-list');
                    Route::match(['post', 'get'], '/gateway-account/ajax/get-history-list', [\App\Http\Controllers\Api\V1\GatewayAccountController::class, 'getHistoryList'])->name('gateway-account.ajax-get-history-list');
                    Route::match(['post', 'get'], '/gateway-account/ajax/ajax-select2-get-list', [\App\Http\Controllers\Api\V1\GatewayAccountController::class, 'select2GetList'])->name('gateway-account.ajax-select2-get-list');
                    Route::match(['post', 'get'], '/gateway-account/ajax/get-detail', [\App\Http\Controllers\Api\V1\GatewayAccountController::class, 'getDetail'])->name('gateway-account.ajax-get-detail');

                    Route::middleware(['admin.auth:api'])->group(function () {
                        Route::match(['post', 'get'], '/gateway-account/ajax/add', [\App\Http\Controllers\Api\V1\GatewayAccountController::class, 'add'])->name('gateway-account.ajax-add');
                        Route::match(['post', 'get'], '/gateway-account/ajax/download-public-key', [\App\Http\Controllers\Api\V1\GatewayAccountController::class, 'downloadPublicKey'])->name('gateway-account.ajax-download-public-key');
                        Route::match(['post', 'get'], '/gateway-account/ajax/update', [\App\Http\Controllers\Api\V1\GatewayAccountController::class, 'update'])->name('gateway-account.ajax-update');
                        Route::match(['post', 'get'], '/gateway-account/ajax/delete', [\App\Http\Controllers\Api\V1\GatewayAccountController::class, 'delete'])->name('gateway-account.ajax-delete');


                        Route::match(['post', 'get'], '/user-withdraw/ajax/add-manual', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'addManual'])->name('user-withdraw.ajax-add-manual');

                        Route::match(['post', 'get'], '/user-bank-account/ajax/get-list', [\App\Http\Controllers\Api\V1\UserBankAccountController::class, 'getList'])->name('user-bank-account.ajax-get-list');
                        Route::match(['post', 'get'], '/user-bank-account/ajax/get-detail', [\App\Http\Controllers\Api\V1\UserBankAccountController::class, 'getDetail'])->name('user-bank-account.ajax-get-detail');
                        Route::match(['post', 'get'], '/user-bank-account/ajax/add', [\App\Http\Controllers\Api\V1\UserBankAccountController::class, 'add'])->name('user-bank-account.ajax-add');
                        Route::match(['post', 'get'], '/user-bank-account/ajax/update', [\App\Http\Controllers\Api\V1\UserBankAccountController::class, 'update'])->name('user-bank-account.ajax-update');
                        Route::match(['post', 'get'], '/user-bank-account/ajax/delete', [\App\Http\Controllers\Api\V1\UserBankAccountController::class, 'delete'])->name('user-bank-account.ajax-delete');

                        Route::match(['post', 'get'], '/bank-account/ajax/get-list', [\App\Http\Controllers\Api\V1\BankAccountController::class, 'getList'])->name('bank-account.ajax-get-list');
                        Route::match(['post', 'get'], '/bank-account/ajax/ajax-select2-get-list', [\App\Http\Controllers\Api\V1\BankAccountController::class, 'select2GetList'])->name('bank-account.ajax-select2-get-list');
                        Route::match(['post', 'get'], '/bank-account/ajax/get-detail', [\App\Http\Controllers\Api\V1\BankAccountController::class, 'getDetail'])->name('bank-account.ajax-get-detail');
                        Route::match(['post', 'get'], '/bank-account/ajax/add', [\App\Http\Controllers\Api\V1\BankAccountController::class, 'add'])->name('bank-account.ajax-add');
                        Route::match(['post', 'get'], '/bank-account/ajax/update', [\App\Http\Controllers\Api\V1\BankAccountController::class, 'update'])->name('bank-account.ajax-update');
                        Route::match(['post', 'get'], '/bank-account/ajax/delete', [\App\Http\Controllers\Api\V1\BankAccountController::class, 'delete'])->name('bank-account.ajax-delete');



                        Route::match(['post', 'get'], '/user/ajax/get-detail', [\App\Http\Controllers\Api\V1\UserController::class, 'getDetail'])->name('user.ajax-get-detail');
                        Route::match(['post', 'get'], '/user/ajax/add', [\App\Http\Controllers\Api\V1\UserController::class, 'add'])->name('user.ajax-add');
                        Route::match(['post', 'get'], '/user/ajax/update', [\App\Http\Controllers\Api\V1\UserController::class, 'update'])->name('user.ajax-update');
                        Route::match(['post', 'get'], '/user/ajax/update-withdraw-limit', [\App\Http\Controllers\Api\V1\UserController::class, 'updateWithdrawLimit'])->name('user.ajax-update-withdraw-limit');
                        Route::match(['post', 'get'], '/user/ajax/delete', [\App\Http\Controllers\Api\V1\UserController::class, 'delete'])->name('user.ajax-delete');
                        Route::match(['post', 'get'], '/user/ajax-change-password', [\App\Http\Controllers\Api\V1\UserController::class, 'changePassword'])->name('user.ajax-change-password');



                        Route::match(['post', 'get'], '/user-group/ajax/get-list', [\App\Http\Controllers\Api\V1\UserGroupController::class, 'getList'])->name('user-group.ajax-get-list');
                        Route::match(['post', 'get'], '/user-group/ajax/ajax-select2-get-list', [\App\Http\Controllers\Api\V1\UserGroupController::class, 'select2GetList'])->name('user-group.ajax-select2-get-list');
                        Route::match(['post', 'get'], '/user-group/ajax/get-detail', [\App\Http\Controllers\Api\V1\UserGroupController::class, 'getDetail'])->name('user-group.ajax-get-detail');
                        Route::match(['post', 'get'], '/user-group/ajax/add', [\App\Http\Controllers\Api\V1\UserGroupController::class, 'add'])->name('user-group.ajax-add');
                        Route::match(['post', 'get'], '/user-group/ajax/update', [\App\Http\Controllers\Api\V1\UserGroupController::class, 'update'])->name('user-group.ajax-update');
                        Route::match(['post', 'get'], '/user-group/ajax/delete', [\App\Http\Controllers\Api\V1\UserGroupController::class, 'delete'])->name('user-group.ajax-delete');


                        Route::get('/gateway/index', [\App\Http\Controllers\Backend\V1\GatewayController::class, 'index'])->name('gateway.index');
                        Route::match(['post', 'get'], '/gateway/ajax/get-list', [\App\Http\Controllers\Api\V1\GatewayController::class, 'getList'])->name('gateway.ajax-get-list');
                        Route::match(['post', 'get'], '/gateway/ajax-generate-key', [\App\Http\Controllers\Api\V1\GatewayController::class, 'generateKey'])->name('gateway.ajax-generate-key');
                        Route::match(['post', 'get'], '/gateway/ajax/ajax-select2-get-list', [\App\Http\Controllers\Api\V1\GatewayController::class, 'select2GetList'])->name('gateway.ajax-select2-get-list');
                        Route::match(['post', 'get'], '/gateway/ajax/get-detail', [\App\Http\Controllers\Api\V1\GatewayController::class, 'getDetail'])->name('gateway.ajax-get-detail');
                        Route::match(['post', 'get'], '/gateway/ajax/add', [\App\Http\Controllers\Api\V1\GatewayController::class, 'add'])->name('gateway.ajax-add');
                        Route::match(['post', 'get'], '/gateway/ajax/update', [\App\Http\Controllers\Api\V1\GatewayController::class, 'update'])->name('gateway.ajax-update');
                        Route::match(['post', 'get'], '/gateway/ajax/delete', [\App\Http\Controllers\Api\V1\GatewayController::class, 'delete'])->name('gateway.ajax-delete');




                        Route::get('/gateway-account/detail', [\App\Http\Controllers\Backend\V1\GatewayAccountController::class, 'detail'])->name('gateway-account.detail');
                        Route::match(['post', 'get'], '/gateway-account-transaction/ajax/get-list', [\App\Http\Controllers\Api\V1\GatewayAccountTransactionController::class, 'getList'])->name('gateway-account-transaction.ajax-get-list');
                        Route::match(['post', 'get'], '/gateway-account-transaction/ajax-add-money', [\App\Http\Controllers\Api\V1\GatewayAccountTransactionController::class, 'ajaxAddMoney'])->name('gateway-account-transaction.ajax-add-money');
                        Route::match(['post', 'get'], '/gateway-account-transaction/ajax-deduct-money', [\App\Http\Controllers\Api\V1\GatewayAccountTransactionController::class, 'ajaxDeductMoney'])->name('gateway-account-transaction.ajax-deduct-money');



                        Route::match(['post', 'get'], '/user-virtual-account/ajax/add', [\App\Http\Controllers\Api\V1\UserVirtualAccountController::class, 'add'])->name('user-virtual-account.ajax-add');
                        Route::match(['post', 'get'], '/user-virtual-account/ajax/get-list', [\App\Http\Controllers\Api\V1\UserVirtualAccountController::class, 'getList'])->name('user-virtual-account.ajax-get-list');
                        Route::match(['post', 'get'], '/user-virtual-account/ajax/ajax-select2-get-list', [\App\Http\Controllers\Api\V1\UserVirtualAccountController::class, 'select2GetList'])->name('user-virtual-account.ajax-select2-get-list');
                        Route::match(['post', 'get'], '/user-virtual-account/ajax/change-status', [\App\Http\Controllers\Api\V1\UserVirtualAccountController::class, 'changeStatus'])->name('user-virtual-account.ajax-change-status');




                        Route::match(['post', 'get'], '/user-transaction/ajax-add-money', [\App\Http\Controllers\Api\V1\UserTransactionController::class, 'ajaxAddMoney'])->name('user-transaction.ajax-add-money');
                        Route::match(['post', 'get'], '/user-transaction/ajax-deduct-money', [\App\Http\Controllers\Api\V1\UserTransactionController::class, 'ajaxDeductMoney'])->name('user-transaction.ajax-deduct-money');


                        Route::match(['post', 'get'], '/user-withdraw/ajax/chang-status', [\App\Http\Controllers\Api\V1\UserWithdrawController::class, 'changeStatus'])->name('user-withdraw.ajax-change-status');

                        Route::get('/bank/index', [\App\Http\Controllers\Backend\V1\BankController::class, 'index'])->name('bank.index');
                        Route::get('/bank-account/index', [\App\Http\Controllers\Backend\V1\BankAccountController::class, 'index'])->name('bank-account.index');


                        Route::get('/report/revenue-paymenthot', [\App\Http\Controllers\Backend\V1\ReportController::class, 'revenuePaymenthot'])->name('report.revenue-paymenthot');
                        Route::match(['post', 'get'], '/report/ajax-get-list-revenue-paymenthot', [\App\Http\Controllers\Backend\V1\ReportController::class, 'getListRevenuePaymenthot'])->name('report.ajax-get-list-revenue-paymenthot');




                        Route::match(['post', 'get'], '/user-fee/ajax/get-list', [\App\Http\Controllers\Api\V1\UserFeeController::class, 'getList'])->name('user-fee.ajax-get-list');
                        Route::match(['post', 'get'], '/user-fee/ajax/get-detail', [\App\Http\Controllers\Api\V1\UserFeeController::class, 'getDetail'])->name('user-fee.ajax-get-detail');
                        Route::match(['post', 'get'], '/user-fee/ajax/update', [\App\Http\Controllers\Api\V1\UserFeeController::class, 'update'])->name('user-fee.ajax-update');

                        Route::match(['post', 'get'], '/user-referal-fee/ajax/get-list', [\App\Http\Controllers\Api\V1\UserReferalFeeController::class, 'getList'])->name('user-referal-fee.ajax-get-list');
                        Route::match(['post', 'get'], '/user-referal-fee/ajax/get-detail', [\App\Http\Controllers\Api\V1\UserReferalFeeController::class, 'getDetail'])->name('user-referal-fee.ajax-get-detail');
                        Route::match(['post', 'get'], '/user-referal-fee/ajax/update', [\App\Http\Controllers\Api\V1\UserReferalFeeController::class, 'update'])->name('user-referal-fee.ajax-update');



                    });
                });
            });
        });
    });
};


$arrUrlBackend = explode(',', env('APP_URL_BACKEND'));
foreach ($arrUrlBackend as $url) {
    Route::group(['domain' => $url], $strRouteBackend);
}



// Route::group(['domain' => env('APP_URL_BACKEND')], $strRouteBackend);
