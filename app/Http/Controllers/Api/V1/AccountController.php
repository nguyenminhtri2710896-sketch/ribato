<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserNotificationService;
use App\Services\UserService;
use App\Services\UserTransactionService;
use App\Services\UserWithdrawalService;
use Illuminate\Http\Request;

class AccountController extends BaseController
{

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
                $this->userService = $userService;
                $this->userNotificationService = $userNotificationService;
                $this->userTransactionService = $userTransactionService;
        }



        public function getInfo()
        {
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->getInfo($arrParams));
        }

        public function changePassword()
        {
                $arrParams = request(['old_password', 'password', 'password_confirmation']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->changePassword($arrParams));
        }


        public function changePasswordSales()
        {
                $arrParams = request(['old_password', 'password', 'password_confirmation']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->changePasswordSales($arrParams));
        }

        public function createPasswordSales()
        {
                $arrParams = request(['password_sale', 'password', 'password_sale_confirmation']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->createPasswordSales($arrParams));
        }

        public function cancelPasswordSales()
        {
                $arrParams = request(['password', 'password_sale']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->cancelPasswordSales($arrParams));
        }

        public function createOrUpdateApiToken()
        {
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->createOrUpdateApiToken($arrParams));
        }

        /**
         * @OA\Post(
         *     path="/api/account/get-balance",
         *     summary="Get balance",
         *     tags={"Accounts"},
         *      @OA\Parameter(
         *         name="api-token",
         *         in="header",
         *         description="token allow create service",
         *         @OA\Schema(
         *             type="string"
         *         )
         *     ),
         *     @OA\RequestBody(
         *         @OA\MediaType(
         *             mediaType="application/json",
         *             @OA\Schema(
         *                 @OA\Property(
         *                     property="sign",
         *                     type="string"
         *                 ),
         *                 example={"query": {"id":1}, "sign": "signiture data"}
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="OK",
         *         @OA\JsonContent(
         *             @OA\Examples(example="result_false", value={"error_code":401,"system_time":"2023-11-24 11:15:59","message":"access_token hết hạn hoặc không được phép sử dụng","errors":{"access_token hết hạn hoặc không được phép sử dụng"},"data":null,"tranid_tracking":"bc47d534b97f7cdb4c2d5d229376915c"}, summary="Result error."),
         *             @OA\Examples(example="result_success", value={"error_code": 200,"message": "success","system_time":"2023-11-24 11:15:59","errors":{},"data":{}}, summary="Result success."),
         *         )
         *     )
         * )
         */
        public function getBalance()
        {
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->getBalance($arrParams));
        }

        public function changeLanguage()
        {
                $arrParams = request(['lang']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->changeLanguage($arrParams));
        }

        public function updateInfo()
        {
                $arrParams = request(['first_name', 'last_name', 'company_name', 'address']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->updateInfo($arrParams));
        }

        public function updateImageAvatar()
        {
                $arrParams = request(['image_base64']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->updateImageAvatar($arrParams));
        }

        public function updateImageCover()
        {
                $arrParams = request(['image_base64']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->updateImageCover($arrParams));
        }

        public function transferBalance()
        {
                $arrParams = request(['email', 'amount', 'note', 'otp']);
                $arrParams["user_id"] = auth()->user()->id;
                if (!auth()->user()->allow_tranfer_balance) {
                        return $this->userService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                                [__("Bạn không được phép sử dụng chức năng này")]
                        ])->result();
                }

                if (!empty(auth()->user()->authy_2factor)) {
                        $resultCheckAuthenticator = $this->userService->checkAuthenticator($arrParams);
                        if ($resultCheckAuthenticator["error_code"] != 0) {
                                return $resultCheckAuthenticator;
                        }
                }

                return response()->json($this->userService->transferBalance($arrParams));
        }

        public function getNotification()
        {
                $arrParams = request(['page', 'limit', 'query', 'sort']);
                $arrParams["query"]["user_id"] = auth()->user()->id;
                return response()->json($this->userNotificationService->getList($arrParams));
        }

        public function readNotification()
        {
                $arrParams = request(['id']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userNotificationService->readed($arrParams));
        }

        public function transaction()
        {
                $arrParams = request(['id']);
                $arrParams["query"]["user_id"] = auth()->user()->id;
                return response()->json($this->userTransactionService->getList($arrParams));
        }

        public function getListSignInLogs()
        {
                $arrParams = request(['page', 'limit', 'query', 'sort']);
                $arrParams["query"]["user_id"] = auth()->user()->id;
                return response()->json($this->userService->getListSignInLogs($arrParams));
        }

        public function getAuthy2Factor()
        {
                $arrParams = [];
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->authy2Factor($arrParams));
        }

        public function validateAuthy2Factor()
        {
                $arrParams = request(['code', 'secret_key']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->validateAuthy2Factor($arrParams));
        }

        public function cancelAuthy2Factor()
        {
                $arrParams = request(['code', 'password']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->cancelAuthy2Factor(arrParams: $arrParams));
        }

        public function requestOtpWithdraw()
        {
                $arrParams = request(['otp', 'password']);
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->requestOtpWithdraw(arrParams: $arrParams));
        }


        /**
         * @OA\Post(
         *     path="/api/account/create-qr-payment",
         *     summary="Tạo QRCode Thanh toán cá nhân",
         *     tags={"Accounts"},
         *      @OA\Parameter(
         *         name="api-token",
         *         in="header",
         *         description="token allow create service",
         *         @OA\Schema(
         *             type="string"
         *         )
         *     ),
         *     @OA\RequestBody(
         *         @OA\MediaType(
         *             mediaType="application/json",
         *             @OA\Schema(
         *                 @OA\Property(
         *                     property="amount",
         *                     type="number"
         *                 ),
         *                 @OA\Property(
         *                     property="remark",
         *                     type="string"
         *                 ),
         *                 example={"amount": 10000, "remark": "test"}
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="OK",
         *         @OA\JsonContent(
         *             @OA\Examples(example="result_false", value={"error_code":401,"system_time":"2023-11-24 11:15:59","message":"access_token hết hạn hoặc không được phép sử dụng","errors":{"access_token hết hạn hoặc không được phép sử dụng"},"data":null,"tranid_tracking":"bc47d534b97f7cdb4c2d5d229376915c"}, summary="Result error."),
         *             @OA\Examples(example="result_success", value={"error_code": 200,"message": "success","system_time":"2023-11-24 11:15:59","errors":{},"data":{}}, summary="Result success."),
         *         )
         *     )
         * )
         */
        public function createQrPayment()
        {
                $arrParams = request()->all();
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->userService->createQrPayment($arrParams));
        }



}
