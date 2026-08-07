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


$routeMod = function () {
    Route::group(['as' => 'mod.'], function () {
        Route::middleware(['XSS'])->group(function () {
            Route::get('/auth/sign-in', [\App\Http\Controllers\Mod\V1\AuthController::class, 'signIn'])->name('auth.sign-in');
            Route::match(['post', 'get'], '/auth/ajax-sign-in', [\App\Http\Controllers\Mod\V1\AuthController::class, 'ajaxSignIn'])->name('auth.ajax-sign-in');
            Route::middleware(['mod.auth'])->group(function () {
                Route::get('/', [\App\Http\Controllers\Mod\V1\IndexController::class, 'index'])->name('index.index');
                Route::get('/lang/{locale}', [\App\Http\Controllers\Mod\V1\IndexController::class, 'lang'])->name('index.lang');

                Route::get('/account', [\App\Http\Controllers\Mod\V1\AccountController::class, 'index'])->name('account.index');
                Route::get('/account/change-password', [\App\Http\Controllers\Mod\V1\AccountController::class, 'changePassword'])->name('account.change-password');
                Route::get('/account/update-profile', [\App\Http\Controllers\Mod\V1\AccountController::class, 'updateProfile'])->name('account.update-profile');
                Route::get('/account/update-authy-2factor', [\App\Http\Controllers\Mod\V1\AccountController::class, 'updateAuthy2Factor'])->name('account.update-authy-2factor');

                /**
                 * View
                 */
                Route::get('/transaction/index', [\App\Http\Controllers\Mod\V1\TransactionController::class, 'index'])->name('transaction.index');
                Route::match(['post', 'get'], '/transaction/ajax/get-list', [\App\Http\Controllers\Mod\V1\TransactionController::class, 'ajaxGetList'])->name('transaction.ajax-get-list');
                Route::match(['post', 'get'], '/transaction/ajax/export-excel', [\App\Http\Controllers\Mod\V1\TransactionController::class, 'ajaxExportExcel'])->name('transaction.ajax-export-excel');

                /**
                 * AJAX sử dụng cotroller API
                 */
                Route::match(["GET", "POST"], '/ajax/sign-out', [\App\Http\Controllers\Mod\V1\AuthController::class, 'ajaxSignOut'])->name('auth.ajax-sign-out');
                Route::match(['post', 'get'], '/account/ajax/get-balance', [\App\Http\Controllers\Mod\V1\AccountController::class, 'getBalance'])->name('account.ajax-get-balance');
                Route::match(['post', 'get'], '/account/ajax/get-info', [\App\Http\Controllers\Mod\V1\AccountController::class, 'ajaxGetInfo'])->name('account.ajax-getInfo');
                Route::match(['post', 'get'], '/account/ajax/change-password', [\App\Http\Controllers\Mod\V1\AccountController::class, 'ajaxChangePassword'])->name('account.ajax-change-password');
                Route::match(['post', 'get'], '/account/ajax/update-info', [\App\Http\Controllers\Mod\V1\AccountController::class, 'ajaxUpdateInfo'])->name('account.ajax-update-info');
                Route::match(['post', 'get'], '/account/ajax/get-authy-2factor', [\App\Http\Controllers\Mod\V1\AccountController::class, 'ajaxGetAuthy2Factor'])->name('account.ajax-get-authy-2factor');
                Route::match(['post', 'get'], '/account/ajax/validate-authy-2factor', [\App\Http\Controllers\Mod\V1\AccountController::class, 'ajaxValidateAuthy2Factor'])->name('account.ajax-validate-authy-2factor');
                Route::match(['post', 'get'], '/account/ajax/cancel-authy-2factor', [\App\Http\Controllers\Mod\V1\AccountController::class, 'ajaxCancelAuthy2Factor'])->name('account.ajax-cancel-authy-2factor');

                Route::get('/user-withdraw/index', [\App\Http\Controllers\Mod\V1\UserWithdrawController::class, 'index'])->name('user-withdraw.index');
                Route::match(['post', 'get'], '/user-withdraw/ajax/get-list', [\App\Http\Controllers\Mod\V1\UserWithdrawController::class, 'ajaxGetList'])->name('user-withdraw.ajax-get-list');
                Route::match(['post', 'get'], '/user-withdraw/ajax/export-excel', [\App\Http\Controllers\Mod\V1\UserWithdrawController::class, 'ajaxExportExcel'])->name('user-withdraw.ajax-export-excel');

                Route::match(['post', 'get'], '/report/ajax/get-total-transaction-amount', [\App\Http\Controllers\Mod\V1\ReportController::class, 'ajaxGetTotalTransactionAmount'])->name('report.ajax-get-total-transaction-amount');

                Route::get('/virtual-account/index', [\App\Http\Controllers\Mod\V1\VirtualAccountController::class, 'index'])->name('virtual-account.index');
                Route::match(['post', 'get'], '/virtual-account/ajax/get-list', [\App\Http\Controllers\Mod\V1\VirtualAccountController::class, 'ajaxGetList'])->name('virtual-account.ajax-get-list');
                Route::match(['post', 'get'], '/virtual-account/ajax/ajax-select2-get-list', [\App\Http\Controllers\Mod\V1\VirtualAccountController::class, 'select2GetList'])->name('virtual-account.ajax-select2-get-list');



            });
        });
    });
};

$arrUrl = explode(',', env('APP_URL_SUBUSER'));
foreach ($arrUrl as $url) {
    Route::group(['domain' => $url], $routeMod);
}
