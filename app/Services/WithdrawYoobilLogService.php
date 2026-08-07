<?php

namespace App\Services;

use App\Jobs\UpdateBalanceGatewayAccountJob;
use App\Models\BankYoobilMapping;
use App\Models\GatewayAccount;
use App\Models\Trash;
use App\Models\User;
use App\Models\UserWithdraw;
use App\Models\UserYoobilConfig;
use App\Models\WithdrawYoobilLog;
use App\Utilities\General;
use App\Utilities\Yoobil;
use Illuminate\Support\Facades\Validator;

class WithdrawYoobilLogService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new WithdrawYoobilLog())->getFillable();
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Đang bảo trì'
        ],
        2 => [
            'name' => 'Hoạt động'
        ]
    ];

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

        $strTransCode = $objUserWithdraw->trans_code;
        $intUserId = $objUserWithdraw->user_id;
        $objWithdrawYoobilLog = WithdrawYoobilLog::where('trans_code', $strTransCode)->where("user_id", $intUserId)->first();
        if ($objWithdrawYoobilLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Yêu cầu đã được tạo vui lòng không tạo lại.")]
            ])->result();
        }

        $userWithdrawService = new UserWithdrawService();

        // lấy bank của yoobil
        $objBankYoobilMapping = BankYoobilMapping::where('bank_id', $objUserWithdraw->bank_id)->first();
        if (!$objBankYoobilMapping) {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Ngân hàng đang bảo trì"]);
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy bank.")]
            ])->result();
        }

        $objUser = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy user.")]
            ])->result();
        }

        $yoobil = new Yoobil();
        $objUserYoobilConfig = UserYoobilConfig::where('user_id', $intUserId)->first();
        if (!$objUserYoobilConfig) {
            $objWithdrawYoobilLog->status_id = 3;
            $objWithdrawYoobilLog->message = "Không tồn tại yoobil config";
            $objWithdrawYoobilLog->save();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tồn tại yoobil config.")]
            ])->result();
        }


        $arrRequest = [
            "return_url" => route("api.withdraw-yoobil.callback"),
            // "return_url" => "https://staging-uat.vnpay.biz/api/test/callback",
            "amount" => $objUserWithdraw->amount,
            "order_no" => $strTransCode,
            "bank_no" => $objBankYoobilMapping->yoobil_bank_id,
            "phone_number" => $objUser->phone,
            "remark" => $objUserWithdraw->remark,
            "id_no" => rand(10, 99) . time(),
            "account_name" => $objUserWithdraw->bank_account_name,
            "account_no" => $objUserWithdraw->bank_account_number,
        ];

        $objWithdrawYoobilLog = WithdrawYoobilLog::create([
            "user_id" => $intUserId,
            "trans_code" => $strTransCode,
            "data_request" => json_encode($arrRequest)
        ]);

        if (!$objWithdrawYoobilLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận yêu cầu thất bại.")]
            ])->result();
        }



        $createCashOutOrder = $yoobil->setBusinessId($objUserYoobilConfig->business_id)
            ->setMerchantId($objUserYoobilConfig->merchant_id)
            ->setSecretKey($objUserYoobilConfig->secret_key)
            ->setPrivateKey($objUserYoobilConfig->private_key)
            ->createCashOutOrder($arrRequest);

        $objWithdrawYoobilLog->data_response = json_encode($createCashOutOrder);
        $objWithdrawYoobilLog->save();
        if (empty($createCashOutOrder["success"])) {
            $objWithdrawYoobilLog->status_id = 3;
            $objWithdrawYoobilLog->message = "Thất bại";
            $objWithdrawYoobilLog->save();
            if (isset($createCashOutOrder["data"]["code"])) {
                if ($createCashOutOrder["data"]["code"] != 10000) {
                    $msg = $createCashOutOrder["data"]["msg"] ?? "";
                    $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Lỗi  $msg"]);
                }
            }

            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tạo yêu cầu Yoobil thất bại.")]
            ])->result();
        }

        $objWithdrawYoobilLog->status_id = 2;
        $objWithdrawYoobilLog->message = "Thành công";
        $objWithdrawYoobilLog->save();


        $objUserWithdraw->status_id = 4;
        $objUserWithdraw->save();


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

        $strTransCode = $objUserWithdraw->trans_code;
        $intUserId = $objUserWithdraw->user_id;

        $objWithdrawYoobilLog = WithdrawYoobilLog::where('trans_code', $strTransCode)->where("user_id", $intUserId)->first();
        if ($objWithdrawYoobilLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Yêu cầu đã được tạo vui lòng không tạo lại.")]
            ])->result();
        }

        $userWithdrawService = new UserWithdrawService();

        // lấy bank của yoobil
        $objBankYoobilMapping = BankYoobilMapping::where('bank_id', $objUserWithdraw->bank_id)->first();
        if (!$objBankYoobilMapping) {
            $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Ngân hàng đang bảo trì"]);
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy bank.")]
            ])->result();
        }

        $objUser = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy user.")]
            ])->result();
        }

        $yoobil = new Yoobil();

        $objGatewayAccount = GatewayAccount::where('id', $objUserWithdraw->gateway_account_id)->where('gateway_id', 3)->first();
        if (!$objGatewayAccount) {
            $objWithdrawYoobilLog->status_id = 3;
            $objWithdrawYoobilLog->message = "Không tồn tại cấu hình cổng";
            $objWithdrawYoobilLog->save();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tồn tại cấu hình cổng.")]
            ])->result();
        }


        $arrRequest = [
            "return_url" => route("api.ipn.yoobil-payout"),
            "amount" => $objUserWithdraw->amount,
            "order_no" => $strTransCode,
            "bank_no" => $objBankYoobilMapping->yoobil_bank_id,
            "phone_number" => $objUser->phone,
            "remark" => $objUserWithdraw->remark,
            "id_no" => rand(10, 99) . time(),
            "account_name" => $objUserWithdraw->bank_account_name,
            "account_no" => $objUserWithdraw->bank_account_number,
        ];

        $objWithdrawYoobilLog = WithdrawYoobilLog::create([
            "user_id" => $intUserId,
            "trans_code" => $strTransCode,
            "data_request" => json_encode($arrRequest)
        ]);

        if (!$objWithdrawYoobilLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận yêu cầu thất bại.")]
            ])->result();
        }

        $createCashOutOrder = $yoobil->setBusinessId($objGatewayAccount->business_id)
            ->setMerchantId($objGatewayAccount->merchant_id)
            ->setSecretKey($objGatewayAccount->secret_key)
            ->setPrivateKey($objGatewayAccount->private_key)
            ->createCashOutOrder($arrRequest);

        $objWithdrawYoobilLog->data_response = json_encode($createCashOutOrder);
        $objWithdrawYoobilLog->save();
        if (empty($createCashOutOrder["success"])) {
            $objWithdrawYoobilLog->status_id = 3;
            $objWithdrawYoobilLog->message = "Thất bại";
            $objWithdrawYoobilLog->save();
            if (isset($createCashOutOrder["data"]["code"])) {
                if ($createCashOutOrder["data"]["code"] != 10000) {
                    $msg = $createCashOutOrder["data"]["msg"] ?? "";
                    $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Lỗi  $msg"]);
                }
            }

            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tạo yêu cầu Yoobil thất bại.")]
            ])->result();
        }

        $objWithdrawYoobilLog->status_id = 2;
        $objWithdrawYoobilLog->message = "Thành công";
        $objWithdrawYoobilLog->save();


        $objUserWithdraw->status_id = 4;
        $objUserWithdraw->save();

        dispatch(new UpdateBalanceGatewayAccountJob([
            'id' => $objUserWithdraw->gateway_account_id,
        ]))->onQueue('request');
        return $this->setStatusCode(0)->setMessage(__("Tạo giao dịch thành công."))->setData([])->result();
    }

    public function updateCallback($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "code" => "required",
            ],
            [

                "code.required" => __("Vui lòng nhập code."),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $strCode = $arrParams["code"];
        if ($strCode != 10000) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thất bại.")]
            ])->result();
        }

        $arrResult = $arrParams["result"];

        $strOrderNo = $arrResult["orderNo"];
        $intStatus = $arrResult["status"];
        $intBusinessId = $arrResult["businessId"] ?? 0;
        $objUserWithdraw = UserWithdraw::where('trans_code', $strOrderNo)->first();
        if (!$objUserWithdraw) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Giao dịch không tồn tại.")]
            ])->result();
        }

        /**
         * CẬP NHẬT CALLBACK
         */
        WithdrawYoobilLog::where('trans_code', $strOrderNo)->update(["data_callback_response" => json_encode($arrResult)]);

        $intUserId = $objUserWithdraw->user_id;
        $yoobil = new Yoobil();
        $objUserYoobilConfig = UserYoobilConfig::where('user_id', $intUserId)->where('business_id', $intBusinessId)->first();
        $strPublicKey = (General::beautyKey($objUserYoobilConfig->yoobil_public_key, "PUBLIC KEY"));
        $verified = $yoobil->setSecretKey($objUserYoobilConfig->secret_key)->setPublicKey($strPublicKey)->verifySign($arrParams["result"] ?? []);
        if (!$verified) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Chữ ký không hợp lệ.")]
            ])->result();
        }

        // bật transaction kiểm tra giao dich đã được xử lý chưa
        // nếu đã thất bại hoặc thành công sẽ reject
        $userWithdrawService = new UserWithdrawService();
        if ($intStatus != 0) {
            $yoobil = new Yoobil();
            $strNote = $yoobil->arrTransactionStatus[$intStatus] ?? $intStatus;
            return $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Thất bại ($strNote)"]);
        }
        return $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 2, "note" => "Đã xử lý thành công"]);
    }

    public function updateCallbackV2($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "code" => "required",
            ],
            [

                "code.required" => __("Vui lòng nhập code."),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $strCode = $arrParams["code"];
        if ($strCode != 10000) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thất bại.")]
            ])->result();
        }

        $arrResult = $arrParams["result"];

        $strOrderNo = $arrResult["orderNo"];
        $intStatus = $arrResult["status"];
        $objUserWithdraw = UserWithdraw::where('trans_code', $strOrderNo)->first();
        if (!$objUserWithdraw) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Giao dịch không tồn tại.")]
            ])->result();
        }


        dispatch(new UpdateBalanceGatewayAccountJob([
            'id' => $objUserWithdraw->gateway_account_id,
        ]))->onQueue('request');

        /**
         * CẬP NHẬT CALLBACK
         */
        WithdrawYoobilLog::where('trans_code', $strOrderNo)->update(["data_callback_response" => json_encode($arrResult)]);

        $intUserId = $objUserWithdraw->user_id;
        $yoobil = new Yoobil();

        // $objUserYoobilConfig = UserYoobilConfig::where('user_id', $intUserId)->where('business_id', $intBusinessId)->first();
        $objGatewayAccount = GatewayAccount::where('id', $objUserWithdraw->gateway_account_id)->where('gateway_id', 3)->first();

        $strPublicKey = (General::beautyKey($objGatewayAccount->gateway_public_key, "PUBLIC KEY"));
        $verified = $yoobil->setSecretKey($objGatewayAccount->secret_key)->setPublicKey($strPublicKey)->verifySign($arrParams["result"] ?? []);
        if (!$verified) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Chữ ký không hợp lệ.")]
            ])->result();
        }

        // bật transaction kiểm tra giao dich đã được xử lý chưa
        // nếu đã thất bại hoặc thành công sẽ reject
        $userWithdrawService = new UserWithdrawService();
        if ($intStatus != 0) {
            $yoobil = new Yoobil();
            $strNote = $yoobil->arrTransactionStatus[$intStatus] ?? $intStatus;
            return $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Thất bại ($strNote)"]);
        }
        return $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 2, "note" => "Đã xử lý thành công"]);
    }
}