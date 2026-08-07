<?php

namespace App\Services;

use App\Jobs\UpdateBalanceGatewayAccountJob;
use App\Models\Bank;
use App\Models\BankYoobilMapping;
use App\Models\GatewayAccount;
use App\Models\PaymenthotAccount;
use App\Models\Trash;
use App\Models\User;
use App\Models\UserGpayConfig;
use App\Models\UserWithdraw;
use App\Models\UserYoobilConfig;
use App\Models\WithdrawGpayLog;
use App\Models\WithdrawPaymenthotLog;
use App\Utilities\General;
use App\Utilities\Gpay;
use App\Utilities\Paymenthot;
use App\Utilities\PaymenthotWeb;
use App\Utilities\Yoobil;
use Illuminate\Support\Facades\Validator;

class WithdrawPaymenthotLogService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new WithdrawGpayLog())->getFillable();
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Đang bảo trì'
        ],
        2 => [
            'name' => 'Hoạt động'
        ]
    ];


    public function checkToken($intUserId = 0, $intLoop = 0)
    {
        $objPaymenthotAccount = PaymenthotAccount::where('user_id', $intUserId)->where('is_check_manual', 1)->first();
        if (!$objPaymenthotAccount) {
            return false;
        }
        $paymenthotWeb = new PaymenthotWeb();
        $getTotalBalance = $paymenthotWeb->setAuthorization($objPaymenthotAccount->access_token)->getTotalBalance();
        if (empty($getTotalBalance["success"])) {
            /**
             * Cho đăng nhập lại
             */
            $resultLogin = $paymenthotWeb->setUsername($objPaymenthotAccount->username)
                ->setPassword($objPaymenthotAccount->password_hash)
                ->login();
            if ($resultLogin['success']) {
                $strToken = $resultLogin['data']["accessToken"] ?? "";
                $objPaymenthotAccount->access_token = $strToken;
                $objPaymenthotAccount->save();
                return true;
            }
            return false;
        }
        if ($intLoop > 2) {
            return false;
        }
        return $this->checkToken($intUserId, $intLoop + 1);
    }

    public function checkTokenV2($intUserId = 0, $intLoop = 0)
    {
        \DB::beginTransaction();
        $objPaymenthotAccount = PaymenthotAccount::where('user_id', $intUserId)->lockForUpdate()->first();
        if (!$objPaymenthotAccount) {
            \DB::rollBack();
            return false;
        }
        // echo $objPaymenthotAccount->private_key;exit;
        $paymenthot = new Paymenthot();
        $getTotalBalance = $paymenthot->setAuthorization($objPaymenthotAccount->access_token)->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPassword($objPaymenthotAccount->password)->setPrivateKey($objPaymenthotAccount->private_key)->balanceTechnicalWallet();
        if (empty($getTotalBalance["success"])) {
            /**
             * Cho đăng nhập lại
             */
            $paymenthot = new Paymenthot();
            $resultLogin = $paymenthot->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPassword($objPaymenthotAccount->password)->setPrivateKey($objPaymenthotAccount->private_key)->login();
            // dd("done",$resultLogin);
            if ($resultLogin['success']) {
                $strToken = $resultLogin["data"]["data"]["accessToken"] ?? "";
                $objPaymenthotAccount->access_token = $strToken;
                $objPaymenthotAccount->save();
                \DB::commit();
                return true;
            }
            \DB::rollBack();
            return false;
        }
        \DB::rollBack();
        return true;
    }

    public function checkTokenCreateRequestV2($gateWayAccountId = 0, $intLoop = 0)
    {
        \DB::beginTransaction();
        $objGatewayAccount = GatewayAccount::where('id', $gateWayAccountId)->where('gateway_id', 1)->lockForUpdate()->first();
        // $objPaymenthotAccount = PaymenthotAccount::where('user_id', $intUserId)->lockForUpdate()->first();
        if (!$objGatewayAccount) {
            \DB::rollBack();
            return false;
        }
        // echo $objPaymenthotAccount->private_key;exit;
        $paymenthot = new Paymenthot();
        $getTotalBalance = $paymenthot->setAuthorization($objGatewayAccount->access_token)->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPassword($objGatewayAccount->password)->setPrivateKey($objGatewayAccount->private_key)->balanceTechnicalWallet();
        if (empty($getTotalBalance["success"])) {
            /**
             * Cho đăng nhập lại
             */
            $paymenthot = new Paymenthot();
            $resultLogin = $paymenthot->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPassword($objGatewayAccount->password)->setPrivateKey($objGatewayAccount->private_key)->login();
            // dd("done",$resultLogin);
            if ($resultLogin['success']) {
                $strToken = $resultLogin["data"]["data"]["accessToken"] ?? "";
                $objGatewayAccount->access_token = $strToken;
                $objGatewayAccount->save();
                \DB::commit();
                return true;
            }
            \DB::rollBack();
            return false;
        }
        \DB::rollBack();
        return true;
    }

    public function createRequest($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "user_withdraw_id" => "required",
            ],
            [

                "user_withdraw_id.required" => __("Vui lòng nhập user_withdraw_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserWithdrawId = $arrParams["user_withdraw_id"];
        $objUserWithdraw = UserWithdraw::where('id', $intUserWithdrawId)->first();
        if (!$objUserWithdraw) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy yêu cầu rút tiền.")]
            ])->result();
        }

        $objBank = Bank::where('id', $objUserWithdraw->bank_id)->first();
        if (!$objBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ngân hàng bảo trì.")]
            ])->result();
        }

        $strTransCode = $objUserWithdraw->trans_code;
        $intUserId = $objUserWithdraw->user_id;
        $objWithdrawPaymenthotLog = WithdrawPaymenthotLog::where('trans_code', $strTransCode)->where("user_id", $intUserId)->first();
        if ($objWithdrawPaymenthotLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Yêu cầu đã được tạo vui lòng không tạo lại.")]
            ])->result();
        }

        $userWithdrawService = new UserWithdrawService();
        $objUser = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy user.")]
            ])->result();
        }

        $objBank = Bank::where('id', $objUserWithdraw->bank_id)->first();
        if (!$objBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy ngân hàng cần chuyển.")]
            ])->result();
        }


        $intUserIdForPaymenthot = $intUserId;
        /**
         * Nếu user được phép tham chiếu thanh toán
         */
        if (!empty($objUser->withdraw_refer_user_id)) {
            $intUserIdForPaymenthot = $objUser->withdraw_refer_user_id;
        }



        $checkToken = $this->checkTokenV2($intUserIdForPaymenthot);
        if (!$checkToken) {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Hệ thống giao dịch lỗi"]);
            return $this->setStatusCode(404)->setMessage("")->setData($checkToken)->setErrors([
                [__("Hệ thống giao dịch lỗi.")]
            ])->result();
        }


        $objPaymenthotAccount = PaymenthotAccount::where('user_id', $intUserIdForPaymenthot)->first();
        if (!$objPaymenthotAccount) {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Người dùng chưa được cấu hình cho cổng thanh toán"]);
            return $this->setStatusCode(404)->setMessage("")->setData($checkToken)->setErrors([
                [__("Người dùng chưa được cấu hình cho cổng thanh toán")]
            ])->result();
        }

        $paymenthot = new Paymenthot();
        $merchantTranferVerify = $paymenthot->setAuthorization($objPaymenthotAccount->access_token)->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPassword($objPaymenthotAccount->password)->setPrivateKey($objPaymenthotAccount->private_key)->bodGetName([
            "bankId" => $objBank->short_code,
            "bankRefNumber" => $objUserWithdraw->bank_account_number,
        ]);

        // $merchantTranferVerify = $paymenthotWeb->setAuthorization($objPaymenthotAccount->access_token)->merchantTranferVerify([
        //     "bankId" => $objBank->short_code,
        //     "bankRefNumber" => $objUserWithdraw->bank_account_number,
        // ]);

        if (empty($merchantTranferVerify["success"])) {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Xác thực thông tin tài khoản không hợp lệ"]);
            \Log::info("bodGetNameError: " . json_encode($merchantTranferVerify));
            return $this->setStatusCode(404)->setMessage("")->setData($merchantTranferVerify)->setErrors([
                [__("Xác thực thông tin tài khoản không hợp lệ.")]
            ])->result();
        }
        $strBankAccountGet = str_replace("  ", " ", trim($merchantTranferVerify["data"]["data"]["bankRefName"] ?? ""));
        if (strtoupper($strBankAccountGet) != strtoupper($objUserWithdraw->bank_account_name)) {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Tên người nhận tiền không trùng khớp với số tài khoản ($strBankAccountGet)"]);
            return $this->setStatusCode(404)->setMessage("")->setData($merchantTranferVerify)->setErrors([
                [__("Tên người nhận tiền không trùng khớp với số tài khoản.")]
            ])->result();
        }


        $strPayoutVersion = "v2";
        if ($intUserId == 1) {
            $strPayoutVersion = "v2";
        }
        // $merchantTranferImploreTransfer247 = $paymenthot->setAuthorization($objPaymenthotAccount->access_token)->merchantTranferImploreTransfer247([
        //     "username" => $objPaymenthotAccount->username,
        //     "passCode" => $objPaymenthotAccount->password_payout_hash,
        // ]);

        if ($strPayoutVersion == "v2") {
            $merchantTranferImploreTransfer247 = $paymenthot->setAuthorization($objPaymenthotAccount->access_token)->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPasscode($objPaymenthotAccount->password_payout)->setPrivateKey($objPaymenthotAccount->private_key)->imploreAuth(["api" => "/merchant-transaction-service/api/v2.0/transfer_247"]);
        } else {
            $merchantTranferImploreTransfer247 = $paymenthot->setAuthorization($objPaymenthotAccount->access_token)->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPasscode($objPaymenthotAccount->password_payout)->setPrivateKey($objPaymenthotAccount->private_key)->imploreAuth();
        }

        if (empty($merchantTranferImploreTransfer247["success"])) {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Lỗi  Không khởi tạo được verifiedKey"]);
            return $this->setStatusCode(404)->setMessage("")->setData($merchantTranferImploreTransfer247)->setErrors([
                [__("Không khởi tạo được verifiedKey.")]
            ])->result();
        }
        $verifiedKey = $merchantTranferImploreTransfer247["data"]["data"]["verifiedKey"] ?? "";

        $arrRequest = [
            "verification" => $verifiedKey,
            "audit" => $strTransCode,
            "amount" => $objUserWithdraw->amount,
            "bankCode" => $objBank->napas_code,
            "bankId" => $objBank->short_code,
            "bankRefName" => $objUserWithdraw->bank_account_name,
            "bankRefNumber" => $objUserWithdraw->bank_account_number,
            "content" => $objUserWithdraw->remark,
        ];

        $objWithdrawPaymenthotLog = WithdrawPaymenthotLog::create([
            "user_id" => $intUserId,
            "trans_code" => $strTransCode,
            "data_request" => json_encode($arrRequest)
        ]);

        if (!$objWithdrawPaymenthotLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận yêu cầu thất bại.")]
            ])->result();
        }

        if ($strPayoutVersion == "v2") {
            $tranfer247V2 = $paymenthot->setAuthorization($objPaymenthotAccount->access_token)->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPasscode($objPaymenthotAccount->password_payout)->setPrivateKey($objPaymenthotAccount->private_key)
                ->tranfer247V2($arrRequest);
        } else {
            $tranfer247V2 = $paymenthot->setAuthorization($objPaymenthotAccount->access_token)->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPasscode($objPaymenthotAccount->password_payout)->setPrivateKey($objPaymenthotAccount->private_key)
                ->tranfer247($arrRequest);
        }

        if (empty($tranfer247V2["success"])) {
            $msg = $tranfer247V2["message"];
            $objWithdrawPaymenthotLog->status_id = 3;
            $objWithdrawPaymenthotLog->message = "Lỗi " . $msg;
            $objWithdrawPaymenthotLog->data_response = json_encode($tranfer247V2);

            if ($objWithdrawPaymenthotLog->save()) {
                /**
                 * Sẽ làm lại phần recheck giao dịch
                 */
                $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 5, "note" => "Lỗi  $msg"]);
            }
            return $this->setStatusCode(404)->setMessage("")->setData($tranfer247V2)->setErrors([
                [__("Tạo yêu  thất bại.")]
            ])->result();
        }
        $objWithdrawPaymenthotLog->status_id = 2;
        $objWithdrawPaymenthotLog->message = "Thành công";
        $objWithdrawPaymenthotLog->data_response = json_encode($tranfer247V2);
        $objWithdrawPaymenthotLog->save();
        if ($strPayoutVersion == "v2") {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 4, "note" => "Chờ xác minh giao dịch"]);
        } else {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 2, "note" => "Thành công"]);
        }

        return $this->setStatusCode(0)->setMessage(__("Tạo giao dịch thành công."))->setData([])->result();
    }

    public function createRequestV2($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "user_withdraw_id" => "required",
            ],
            [

                "user_withdraw_id.required" => __("Vui lòng nhập user_withdraw_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserWithdrawId = $arrParams["user_withdraw_id"];
        $objUserWithdraw = UserWithdraw::where('id', $intUserWithdrawId)->first();

        if (!$objUserWithdraw) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy yêu cầu rút tiền.")]
            ])->result();
        }

        $objBank = Bank::where('id', $objUserWithdraw->bank_id)->first();
        if (!$objBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ngân hàng bảo trì.")]
            ])->result();
        }

        $strTransCode = $objUserWithdraw->trans_code;
        $intUserId = $objUserWithdraw->user_id;
        $objWithdrawPaymenthotLog = WithdrawPaymenthotLog::where('trans_code', $strTransCode)->where("user_id", $intUserId)->first();
        if ($objWithdrawPaymenthotLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Yêu cầu đã được tạo vui lòng không tạo lại.")]
            ])->result();
        }

        $userWithdrawService = new UserWithdrawService();
        $objUser = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy user.")]
            ])->result();
        }

        $objBank = Bank::where('id', $objUserWithdraw->bank_id)->first();
        if (!$objBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy ngân hàng cần chuyển.")]
            ])->result();
        }


        $checkToken = $this->checkTokenCreateRequestV2($objUserWithdraw->gateway_account_id);
        if (!$checkToken) {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Hệ thống giao dịch lỗi"]);
            return $this->setStatusCode(404)->setMessage("")->setData($checkToken)->setErrors([
                [__("Hệ thống giao dịch lỗi.")]
            ])->result();
        }

        $objGatewayAccount = GatewayAccount::where('id', $objUserWithdraw->gateway_account_id)->where('gateway_id', 1)->first();

        if (!$objGatewayAccount) {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Người dùng chưa được cấu hình cho cổng thanh toán"]);
            return $this->setStatusCode(404)->setMessage("")->setData($checkToken)->setErrors([
                [__("Người dùng chưa được cấu hình cho cổng thanh toán")]
            ])->result();
        }

        $paymenthot = new Paymenthot();
        $merchantTranferVerify = $paymenthot->setAuthorization($objGatewayAccount->access_token)->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPassword($objGatewayAccount->password)->setPrivateKey($objGatewayAccount->private_key)->bodGetName([
            "bankId" => $objBank->short_code,
            "bankRefNumber" => $objUserWithdraw->bank_account_number,
        ]);

        if (empty($merchantTranferVerify["success"])) {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Xác thực thông tin tài khoản không hợp lệ"]);
            \Log::info("bodGetNameError: " . json_encode($merchantTranferVerify));
            return $this->setStatusCode(404)->setMessage("")->setData($merchantTranferVerify)->setErrors([
                [__("Xác thực thông tin tài khoản không hợp lệ.")]
            ])->result();
        }


        $strBankAccountGet = str_replace("  ", " ", trim($merchantTranferVerify["data"]["data"]["bankRefName"] ?? ""));
        if (strtoupper($strBankAccountGet) != strtoupper($objUserWithdraw->bank_account_name)) {
            \Log::info("merchantTranferVerify: " . json_encode($merchantTranferVerify));
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Tên người nhận tiền không trùng khớp với số tài khoản ($strBankAccountGet)"]);
            return $this->setStatusCode(404)->setMessage("")->setData($merchantTranferVerify)->setErrors([
                [__("Tên người nhận tiền không trùng khớp với số tài khoản.")]
            ])->result();
        }



        $merchantTranferImploreTransfer247 = $paymenthot->setAuthorization($objGatewayAccount->access_token)->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPasscode($objGatewayAccount->payout_pin)->setPrivateKey($objGatewayAccount->private_key)->imploreAuth(["api" => "/merchant-transaction-service/api/v2.0/transfer_247"]);
        if (empty($merchantTranferImploreTransfer247["success"])) {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Lỗi  Không khởi tạo được verifiedKey"]);
            return $this->setStatusCode(404)->setMessage("")->setData($merchantTranferImploreTransfer247)->setErrors([
                [__("Không khởi tạo được verifiedKey.")]
            ])->result();
        }
        $verifiedKey = $merchantTranferImploreTransfer247["data"]["data"]["verifiedKey"] ?? "";

        $arrRequest = [
            "verification" => $verifiedKey,
            "audit" => $strTransCode,
            "amount" => $objUserWithdraw->amount,
            "bankCode" => $objBank->napas_code,
            "bankId" => $objBank->short_code,
            "bankRefName" => $objUserWithdraw->bank_account_name,
            "bankRefNumber" => $objUserWithdraw->bank_account_number,
            "content" => $objUserWithdraw->remark,
        ];

        $objWithdrawPaymenthotLog = WithdrawPaymenthotLog::create([
            "user_id" => $intUserId,
            "trans_code" => $strTransCode,
            "data_request" => json_encode($arrRequest)
        ]);

        if (!$objWithdrawPaymenthotLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận yêu cầu thất bại.")]
            ])->result();
        }

        $tranfer247V2 = $paymenthot->setAuthorization($objGatewayAccount->access_token)->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPrivateKey($objGatewayAccount->private_key)
            ->tranfer247V2($arrRequest);


        if (empty($tranfer247V2["success"])) {
            $msg = $tranfer247V2["message"];
            $objWithdrawPaymenthotLog->status_id = 3;
            $objWithdrawPaymenthotLog->message = "Lỗi " . $msg;
            $objWithdrawPaymenthotLog->data_response = json_encode($tranfer247V2);

            if ($objWithdrawPaymenthotLog->save()) {
                /**
                 * Sẽ làm lại phần recheck giao dịch
                 */
                $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 5, "note" => "Lỗi  $msg"]);
            }
            return $this->setStatusCode(404)->setMessage("")->setData($tranfer247V2)->setErrors([
                [__("Tạo yêu  thất bại.")]
            ])->result();
        }
        $objWithdrawPaymenthotLog->status_id = 2;
        $objWithdrawPaymenthotLog->message = "Thành công";
        $objWithdrawPaymenthotLog->data_response = json_encode($tranfer247V2);
        $objWithdrawPaymenthotLog->save();
        $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 4, "note" => "Chờ xác minh giao dịch"]);
        dispatch(new UpdateBalanceGatewayAccountJob([
            'id' => $objUserWithdraw->gateway_account_id,
        ]))->onQueue('request');
        return $this->setStatusCode(0)->setMessage(__("Tạo giao dịch thành công."))->setData([])->result();
    }

}