<?php

namespace App\Http\Controllers\Backend\V1;

use App\Services\UserNotificationService;
use App\Services\UserService;
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

    private $userService = null;
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
    public function __construct(UserService $userService, UserNotificationService $userNotificationService, UserTransactionService $userTransactionService)
    {
        $this->userService             = $userService;
        $this->userNotificationService = $userNotificationService;
        $this->userTransactionService  = $userTransactionService;
    }

    /*
    Api khởi tạo (mã code) đến email hoặc số điện thoại
    */

    public function index()
    {
        return view("backend.".config('app.backend_version').".account.index")->with([]);
    }



    public function changePassword()
    {
        return view("backend.".config('app.backend_version').".account.change-password")->with([]);
    }

    public function updateProfile()
    {
        return view("backend.".config('app.backend_version').".account.update-profile")->with([]);
    }

    public function updateAuthy2Factor()
    {


        // echo '<img src="' . (new QRCode)->render($data) . '" alt="QR Code" />';
        // exit;

        $issuer        = env('APP_NAME');
        $accountName   = auth()->user()->fullname . "(" . auth()->user()->email . ")";
        $secretFactory = new SecretFactory();
        $secret        = $secretFactory->create($issuer, $accountName);
        $strDataImage  = 'otpauth://totp/' . urlencode($accountName) . '?secret=' . $secret->getSecretKey() . '&issuer=' . $issuer;
        $strQrSecret   = (new QRCode)->render($strDataImage);
        return view("backend.".config('app.backend_version').".account.update-authy-2factor")->with([
            'qr_secret' => $strQrSecret,
            'secret_key' => $secret->getSecretKey()
        ]);
    }



    public function ajaxGetInfo()
    {
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->getInfo($arrParams));
    }

    public function ajaxChangePassword()
    {
        $arrParams            = request(['old_password', 'password', 'password_confirmation']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->changePassword($arrParams));
    }


    public function ajaxChangePasswordSales()
    {
        $arrParams            = request(['old_password', 'password', 'password_confirmation']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->changePasswordSales($arrParams));
    }

    public function ajaxCreatePasswordSales()
    {
        $arrParams            = request(['password_sale', 'password', 'password_sale_confirmation']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->createPasswordSales($arrParams));
    }

    public function ajaxCancelPasswordSales()
    {
        $arrParams            = request(['password', 'password_sale']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->cancelPasswordSales($arrParams));
    }

    public function ajaxCreateOrUpdateApiToken()
    {
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->createOrUpdateApiToken($arrParams));
    }

    public function ajaxGetBalance()
    {
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->getBalance($arrParams));
    }

    public function ajaxChangeLanguage()
    {
        $arrParams            = request(['lang']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->changeLanguage($arrParams));
    }

    public function ajaxUpdateInfo()
    {
        $arrParams            = request(['first_name', 'last_name', 'company_name', 'address']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->updateInfo($arrParams));
    }

    public function ajaxUpdateImageAvatar()
    {
        $arrParams            = request(['image_base64']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->updateImageAvatar($arrParams));
    }

    public function ajaxUpdateImageCover()
    {
        $arrParams            = request(['image_base64']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->updateImageCover($arrParams));
    }

    public function ajaxTransferBalance()
    {
        $arrParams            = request(['email', 'amount']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userService->transferBalance($arrParams));
    }

    public function ajaxGetNotification()
    {
        $arrParams                     = request(['page', 'limit', 'query', 'sort']);
        $arrParams["query"]["user_id"] = auth()->user()->id;
        return response()->json($this->userNotificationService->getList($arrParams));
    }

    public function ajaxReadNotification()
    {
        $arrParams            = request(['id']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userNotificationService->readed($arrParams));
    }

    public function ajaxTransaction()
    {
        $arrParams                     = request(['id']);
        $arrParams["query"]["user_id"] = auth()->user()->id;
        return response()->json($this->userTransactionService->getList($arrParams));
    }


    public function ajaxGetListSignInLogs()
    {
        $arrParams                     = request(['page', 'limit', 'query', 'sort']);
        $arrParams["query"]["user_id"] = auth()->user()->id;
        return response()->json($this->userService->getListSignInLogs($arrParams));
    }

}