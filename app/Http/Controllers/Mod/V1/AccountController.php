<?php

namespace App\Http\Controllers\Mod\V1;

use App\Services\UserNotificationService;
use App\Services\SubUserService;
use App\Services\UserTransactionService;
use chillerlan\QRCode\QRCode;
use Dolondro\GoogleAuthenticator\SecretFactory;

class AccountController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $subUserService = null;
    private $userNotificationService = null;
    private $userWidthDrawalsService;
    private $userTransactionService;
    /**
     * api identify account (hiển thị thông tin người dùng cơ bản)
     *- sử dụng cho lấy lại mật khẩu
     *- kiểm tra đăng nhập tài khoản tồn tại
     *- kiểm tra tài khoản đăng ký tồn tại
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SubUserService $subUserService, UserNotificationService $userNotificationService, UserTransactionService $userTransactionService)
    {
        $this->subUserService = $subUserService;
        $this->userNotificationService = $userNotificationService;
        $this->userTransactionService = $userTransactionService;
    }

    /*
    Api khởi tạo (mã code) đến email hoặc số điện thoại
    */

    public function index()
    {
        return view("mod.".config('app.mod_version').".account.index")->with([]);
    }



    public function changePassword()
    {
        return view("mod.".config('app.mod_version').".account.change-password")->with([]);
    }

    public function updateProfile()
    {
        return view("mod.".config('app.mod_version').".account.update-profile")->with([]);
    }

    public function updateAuthy2Factor()
    {
        $issuer = env('APP_NAME');
        $accountName = auth()->user()->fullname . "(" . auth()->user()->email . ")";
        $secretFactory = new SecretFactory();
        $secret = $secretFactory->create($issuer, $accountName);
        $strDataImage = 'otpauth://totp/' . urlencode($accountName) . '?secret=' . $secret->getSecretKey() . '&issuer=' . $issuer;
        $strQrSecret = (new QRCode)->render($strDataImage);
        return view("mod.".config('app.mod_version').".account.update-authy-2factor")->with([
            'qr_secret' => $strQrSecret,
            'secret_key' => $secret->getSecretKey()
        ]);
    }

    public function ajaxGetInfo()
    {
        $arrParams["sub_user_id"] = auth()->user()->id;
        return response()->json($this->subUserService->getInfo($arrParams));
    }

    public function ajaxChangePassword()
    {
        $arrParams = request(['old_password', 'password', 'password_confirmation']);
        $arrParams["sub_user_id"] = auth()->user()->id;
        return response()->json($this->subUserService->changePassword($arrParams));
    }

    public function ajaxUpdateInfo()
    {
        $arrParams = request(['first_name', 'last_name', 'company_name', 'address']);
        $arrParams["sub_user_id"] = auth()->user()->id;
        return response()->json($this->subUserService->updateInfo($arrParams));
    }

    public function ajaxGetAuthy2Factor()
    {
        $arrParams = [];
        $arrParams["sub_user_id"] = auth()->user()->id;
        return response()->json($this->subUserService->authy2Factor($arrParams));
    }

    public function ajaxValidateAuthy2Factor()
    {
        $arrParams = request(['code', 'secret_key']);
        $arrParams["sub_user_id"] = auth()->user()->id;
        return response()->json($this->subUserService->validateAuthy2Factor($arrParams));
    }

    public function ajaxCancelAuthy2Factor()
    {
        $arrParams = request(['code', 'password']);
        $arrParams["sub_user_id"] = auth()->user()->id;
        return response()->json($this->subUserService->cancelAuthy2Factor(arrParams: $arrParams));
    }


    public function getBalance()
    {
        $arrParams["user_id"] = auth()->user()->user_id;
        return response()->json($this->subUserService->getBalance($arrParams));
    }

}