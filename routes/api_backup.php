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
    // Route::middleware(['XSS'])->group(function () {
    // Route::get('/', [\App\Http\Controllers\Api\V1\IndexController::class, 'index'])->name('index.index');
    // Route::match(["post", "get"], '/sign-in', [\App\Http\Controllers\Api\V1\AuthController::class, 'signIn'])->name('auth.sign-in');
    // Route::match(['post', 'get'], '/sign-up', [\App\Http\Controllers\Api\V1\AuthController::class, 'signUp'])->name('auth.sign-up');
    // Route::match(['post', 'get'], '/check-token', [\App\Http\Controllers\Api\V1\AuthController::class, 'checkToken'])->name('auth.check-token');
    // Route::match(['post', 'get'], '/check-email-login-info', [\App\Http\Controllers\Api\V1\AuthController::class, 'checkEmailLoginInfo'])->name('account.check-email-login-info');
    // /**
    //  * Khôi phục mật khẩu
    //  */
    // // khởi tạo một mã code để xác đổi mk
    // Route::match(["post", "get"], '/recover/initiate', [\App\Http\Controllers\Api\V1\RecoverController::class, 'initiate'])->name('recover.initiate');
    // // nhập một mã code để xác nhận mã code đúng
    // Route::match(["post", "get"], '/recover/code', [\App\Http\Controllers\Api\V1\RecoverController::class, 'code'])->name('recover.code');
    // // đổi mật khẩu
    // Route::match(["post", "get"], '/recover/change-password', [\App\Http\Controllers\Api\V1\RecoverController::class, 'changePassword'])->name('recover.change-password');
    // Route::match(["post", "get"], '/recover/change-password-sales', [\App\Http\Controllers\Api\V1\RecoverController::class, 'changePasswordSales'])->name('recover.change-password-sales');

    // Route::match(["post", "get"], '/bank-tranfer/add-deposit', [\App\Http\Controllers\Api\V1\BankingTranferController::class, 'addDeposit'])->name('bank-tranfer.add-deposit');

    Route::middleware(['auth:api'])->group(function () {
        // Route::match(["GET", "POST"], '/sign-out', [\App\Http\Controllers\Api\V1\AuthController::class, 'signOut'])->name('auth.sign-out');
        // Route::match(['post', 'get'], '/refresh-token', [\App\Http\Controllers\Api\V1\AuthController::class, 'refreshToken'])->name('account.refresh-token');
        // Route::match(['post', 'get'], '/upload/image', [\App\Http\Controllers\Api\V1\UploadController::class, 'image'])->name('upload.image');

        // /**
        //  * ACCOUNTs
        //  */
        // Route::match(['post', 'get'], '/account/get-info', [\App\Http\Controllers\Api\V1\AccountController::class, 'getInfo'])->name('account.getInfo');
        // Route::match(['post', 'get'], '/account/change-password', [\App\Http\Controllers\Api\V1\AccountController::class, 'changePassword'])->name('account.change-password');
        // Route::match(['post', 'get'], '/account/change-password-sales', [\App\Http\Controllers\Api\V1\AccountController::class, 'changePasswordSales'])->name('account.change-password-sales');
        // Route::match(['post', 'get'], '/account/create-password-sales', [\App\Http\Controllers\Api\V1\AccountController::class, 'createPasswordSales'])->name('account.create-password-sales');
        // Route::match(['post', 'get'], '/account/cancel-password-sales', [\App\Http\Controllers\Api\V1\AccountController::class, 'cancelPasswordSales'])->name('account.cancel-password-sales');
        // Route::match(['post', 'get'], '/account/get-balance', [\App\Http\Controllers\Api\V1\AccountController::class, 'getBalance'])->name('account.get-balance');
        // Route::match(['post', 'get'], '/account/update-info', [\App\Http\Controllers\Api\V1\AccountController::class, 'updateInfo'])->name('account.update-info');

        // Route::match(['post', 'get'], '/account/create-or-update-api-token', [\App\Http\Controllers\Api\V1\AccountController::class, 'createOrUpdateApiToken'])->name('account.create-or-update-api-token');
        // Route::match(['post', 'get'], '/account/change-language', [\App\Http\Controllers\Api\V1\AccountController::class, 'changeLanguage'])->name('account.change-language');
        // Route::match(['post', 'get'], '/account/update-image-avatar', [\App\Http\Controllers\Api\V1\AccountController::class, 'updateImageAvatar'])->name('account.update-image-avatar');
        // Route::match(['post', 'get'], '/account/update-image-cover', [\App\Http\Controllers\Api\V1\AccountController::class, 'updateImageCover'])->name('account.update-image-cover');
        // Route::match(['post', 'get'], '/account/transfer-balance', [\App\Http\Controllers\Api\V1\AccountController::class, 'transferBalance'])->name('account.transfer-balance');
        // Route::match(['post', 'get'], '/account/get-notificaiton', [\App\Http\Controllers\Api\V1\AccountController::class, 'getNotification'])->name('account.get-notificaiton');
        // Route::match(['post', 'get'], '/account/read-notificaiton', [\App\Http\Controllers\Api\V1\AccountController::class, 'readNotification'])->name('account.read-notificaiton');
        // Route::match(['post', 'get'], '/account/get-list-signin-logs', [\App\Http\Controllers\Api\V1\AccountController::class, 'getListSignInLogs'])->name('account.get-list-signin-logs');
        // Route::match(['post', 'get'], '/account/transaction', [\App\Http\Controllers\Api\V1\AccountController::class, 'transaction'])->name('account.transaction');



        // Route::match(['post', 'get'], '/bank/select2-get-list', [\App\Http\Controllers\Api\V1\BankController::class, 'select2GetList'])->name('bank.select2-get-list');
        // /**
        //  * Quản lý user bank report
        //  */
        // Route::match(['post', 'get'], '/user-bank-topup/get-list', [\App\Http\Controllers\Api\V1\UserBankTopupController::class, 'getList'])->name('user-bank-topup.get-list');
        // Route::match(['post', 'get'], '/user-bank-topup/get-detail', [\App\Http\Controllers\Api\V1\UserBankTopupController::class, 'getDetail'])->name('user-bank-topup.get-detail');

        // Route::match(['post', 'get'], '/user-bank-withdraw/get-list', [\App\Http\Controllers\Api\V1\UserBankWithdrawController::class, 'getList'])->name('user-bank-withdraw.get-list');
        // Route::match(['post', 'get'], '/user-bank-withdraw/get-detail', [\App\Http\Controllers\Api\V1\UserBankWithdrawController::class, 'getDetail'])->name('user-bank-withdraw.get-detail');
        // Route::match(['post', 'get'], '/user-bank-withdraw/request', [\App\Http\Controllers\Api\V1\UserBankWithdrawController::class, 'request'])->name('user-bank-withdraw.request');

        // /**
        //  * Quản lý Userbank
        //  */
        // Route::match(['post', 'get'], '/user-bank/get-list', [\App\Http\Controllers\Api\V1\UserBankController::class, 'getList'])->name('user-bank.get-list');
        // Route::match(['post', 'get'], '/user-bank/select2-get-list', [\App\Http\Controllers\Api\V1\UserBankController::class, 'select2GetList'])->name('user-bank.select2-get-list');
        // Route::match(['post', 'get'], '/user-bank/get-detail', [\App\Http\Controllers\Api\V1\UserBankController::class, 'getDetail'])->name('user-bank.get-detail');
        // Route::match(['post', 'get'], '/user-bank/add', [\App\Http\Controllers\Api\V1\UserBankController::class, 'add'])->name('user-bank.add');
        // // Route::match(['post', 'get'], '/user-bank/update', [\App\Http\Controllers\Api\V1\UserBankController::class, 'update'])->name('user-bank.update');

        // /**
        //  * Quản lý UserbankTransaction
        //  */
        // Route::match(['post', 'get'], '/user-bank-transaction/get-list', [\App\Http\Controllers\Api\V1\UserBankTransactionController::class, 'getList'])->name('user-bank-transaction.get-list');
        // Route::match(['post', 'get'], '/user-bank-transaction/get-detail', [\App\Http\Controllers\Api\V1\UserBankTransactionController::class, 'getDetail'])->name('user-bank-transaction.get-detail');
        // // Route::match(['post', 'get'], '/user-bank-transaction/add', [\App\Http\Controllers\Api\V1\UserBankTransactionController::class, 'add'])->name('user-bank-transaction.add');
        // // Route::match(['post', 'get'], '/user-bank-transaction/update-status', [\App\Http\Controllers\Api\V1\UserBankTransactionController::class, 'updateStats'])->name('user-bank-transaction.update');


        Route::middleware(['admin.auth:api'])->group(function () {
            // Route::match(['post', 'get'], '/user/get-list', [\App\Http\Controllers\Api\V1\UserController::class, 'getList'])->name('user.get-list');
            // Route::match(['post', 'get'], '/user/select2-get-list', [\App\Http\Controllers\Api\V1\UserController::class, 'select2GetList'])->name('user.select2-get-list');
            // Route::match(['post', 'get'], '/user/get-detail', [\App\Http\Controllers\Api\V1\UserController::class, 'getDetail'])->name('user.get-detail');
            // Route::match(['post', 'get'], '/user/update', [\App\Http\Controllers\Api\V1\UserController::class, 'update'])->name('user.update');
            // Route::match(['post', 'get'], '/user/add', [\App\Http\Controllers\Api\V1\UserController::class, 'add'])->name('user.add');
            // Route::match(['post', 'get'], '/user/update-active-and-deactive', [\App\Http\Controllers\Api\V1\UserController::class, 'updateActiveAndDeactive'])->name('user.update-active-and-deactive');
            // /**
            //  * USER GROUP
            //  */
            // Route::match(['post', 'get'], '/user-group/get-list', [\App\Http\Controllers\Api\V1\UserGroupController::class, 'getList'])->name('user-group.get-list');
            // Route::match(['post', 'get'], '/user-group/get-detail', [\App\Http\Controllers\Api\V1\UserGroupController::class, 'getDetail'])->name('user-group.get-detail');
            // Route::match(['post', 'get'], '/user-group/add', [\App\Http\Controllers\Api\V1\UserGroupController::class, 'add'])->name('user-group.add');
            // Route::match(['post', 'get'], '/user-group/update', [\App\Http\Controllers\Api\V1\UserGroupController::class, 'update'])->name('user-group.update');
            // Route::match(['post', 'get'], '/user-group/delete', [\App\Http\Controllers\Api\V1\UserGroupController::class, 'delete'])->name('user-group.delete');

            // /**
            //  * Quản lý Bank
            //  */
            // Route::match(['post', 'get'], '/bank/get-list', [\App\Http\Controllers\Api\V1\BankController::class, 'getList'])->name('bank.get-list');
            // Route::match(['post', 'get'], '/bank/get-detail', [\App\Http\Controllers\Api\V1\BankController::class, 'getDetail'])->name('bank.get-detail');
            // Route::match(['post', 'get'], '/bank/add', [\App\Http\Controllers\Api\V1\BankController::class, 'add'])->name('bank.add');
            // Route::match(['post', 'get'], '/bank/update', [\App\Http\Controllers\Api\V1\BankController::class, 'update'])->name('bank.update');
            // Route::match(['post', 'get'], '/bank/delete', [\App\Http\Controllers\Api\V1\BankController::class, 'delete'])->name('bank.delete');

            // /**
            //  * Quản lý cấu hình banking
            //  */
            // Route::match(['post', 'get'], '/bank-setting/get-list', [\App\Http\Controllers\Api\V1\BankSettingController::class, 'getList'])->name('bank-setting.get-list');
            // Route::match(['post', 'get'], '/bank-setting/select2-get-list', [\App\Http\Controllers\Api\V1\BankSettingController::class, 'select2GetList'])->name('bank-setting.select2-get-list');
            // Route::match(['post', 'get'], '/bank-setting/get-detail', [\App\Http\Controllers\Api\V1\BankSettingController::class, 'getDetail'])->name('bank-setting.get-detail');
            // Route::match(['post', 'get'], '/bank-setting/add', [\App\Http\Controllers\Api\V1\BankSettingController::class, 'add'])->name('bank-setting.add');
            // Route::match(['post', 'get'], '/bank-setting/update', [\App\Http\Controllers\Api\V1\BankSettingController::class, 'update'])->name('bank-setting.update');
            // Route::match(['post', 'get'], '/bank-setting/update-status', [\App\Http\Controllers\Api\V1\BankSettingController::class, 'updateStatus'])->name('bank-setting.update-status');
            // Route::match(['post', 'get'], '/bank-setting/delete', [\App\Http\Controllers\Api\V1\BankSettingController::class, 'delete'])->name('bank-setting.delete');


            // /**
            //  * Quản lý user business
            //  */
            // Route::match(['post', 'get'], '/user-business/get-list', [\App\Http\Controllers\Api\V1\UserBusinessController::class, 'getList'])->name('user-business.get-list');
            // Route::match(['post', 'get'], '/user-business/select2-get-list', [\App\Http\Controllers\Api\V1\UserBusinessController::class, 'select2GetList'])->name('user-business.select2-get-list');
            // Route::match(['post', 'get'], '/user-business/get-detail', [\App\Http\Controllers\Api\V1\UserBusinessController::class, 'getDetail'])->name('user-business.get-detail');
            // Route::match(['post', 'get'], '/user-business/add', [\App\Http\Controllers\Api\V1\UserBusinessController::class, 'add'])->name('user-business.add');
            // Route::match(['post', 'get'], '/user-business/update', [\App\Http\Controllers\Api\V1\UserBusinessController::class, 'update'])->name('user-business.update');
            // Route::match(['post', 'get'], '/user-business/update-status', [\App\Http\Controllers\Api\V1\UserBusinessController::class, 'updateStatus'])->name('user-business.update-status');
            // Route::match(['post', 'get'], '/user-business/delete', [\App\Http\Controllers\Api\V1\UserBusinessController::class, 'delete'])->name('user-business.delete');


            // /**
            //  * Quản lý user fee
            //  */
            // Route::match(['post', 'get'], '/user-fee/get-list', [\App\Http\Controllers\Api\V1\UserFeeController::class, 'getList'])->name('user-fee.get-list');
            // Route::match(['post', 'get'], '/user-fee/select2-get-list', [\App\Http\Controllers\Api\V1\UserFeeController::class, 'select2GetList'])->name('user-fee.select2-get-list');
            // Route::match(['post', 'get'], '/user-fee/get-detail', [\App\Http\Controllers\Api\V1\UserFeeController::class, 'getDetail'])->name('user-fee.get-detail');
            // Route::match(['post', 'get'], '/user-fee/add', [\App\Http\Controllers\Api\V1\UserFeeController::class, 'add'])->name('user-fee.add');
            // Route::match(['post', 'get'], '/user-fee/update', [\App\Http\Controllers\Api\V1\UserFeeController::class, 'update'])->name('user-fee.update');
            // Route::match(['post', 'get'], '/user-fee/update-status', [\App\Http\Controllers\Api\V1\UserFeeController::class, 'updateStatus'])->name('user-fee.update-status');
            // Route::match(['post', 'get'], '/user-fee/delete', [\App\Http\Controllers\Api\V1\UserFeeController::class, 'delete'])->name('user-fee.delete');

            // /**
            //  * User payment
            //  */
            // Route::match(['post', 'get'], '/user-payment/get-list', [\App\Http\Controllers\Api\V1\UserPaymentController::class, 'getList'])->name('user-payment.get-list');



        });
        // });
    });
});