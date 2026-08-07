<?php

namespace App\Services;

use App\Jobs\UpdateBalanceGatewayAccountJob;
use App\Models\Bank;
use App\Models\BankYoobilMapping;
use App\Models\GatewayAccount;
use App\Models\Trash;
use App\Models\User;
use App\Models\UserGpayConfig;
use App\Models\UserWithdraw;
use App\Models\UserYoobilConfig;
use App\Models\WithdrawGpayLog;
use App\Utilities\General;
use App\Utilities\Gpay;
use App\Utilities\Yoobil;
use Illuminate\Support\Facades\Validator;

class WithdrawGpayLogService extends AbstractService
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
        $objUserWithdraw   = UserWithdraw::where('id', $intUserWithdrawId)->first();
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

        $strTransCode       = $objUserWithdraw->trans_code;
        $intUserId          = $objUserWithdraw->user_id;
        $objWithdrawGpayLog = WithdrawGpayLog::where('trans_code', $strTransCode)->where("user_id", $intUserId)->first();
        if ($objWithdrawGpayLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Yêu cầu đã được tạo vui lòng không tạo lại.")]
            ])->result();
        }

        $userWithdrawService = new UserWithdrawService();
        $objUser             = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy user.")]
            ])->result();
        }

        $gpay              = new Gpay();
        $objUserGpayConfig = UserGpayConfig::where('user_id', $intUserId)->first();
        if (!$objUserGpayConfig) {
            $objWithdrawGpayLog->status_id = 3;
            $objWithdrawGpayLog->message   = "Không tồn tại yoobil config";
            $objWithdrawGpayLog->save();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tồn tại yoobil config.")]
            ])->result();
        }



        $arrRequest = [
            "amount" => $objUserWithdraw->amount,
            "account_number" => $objUserWithdraw->bank_account_number,
            'bank_code' => $objBank->short_code,
            "full_name" => $objUserWithdraw->bank_account_name,
            'type' => "ACCOUNT_NUMBER",
            'transaction_id' => $objUserWithdraw->trans_code,
            'message' => $objUserWithdraw->remark,
        ];

        $objWithdrawGpayLog = WithdrawGpayLog::create([
            "user_id" => $intUserId,
            "trans_code" => $strTransCode,
            "data_request" => json_encode($arrRequest)
        ]);

        if (!$objWithdrawGpayLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận yêu cầu thất bại.")]
            ])->result();
        }


        $strPrivateKey                     = $objUserGpayConfig->private_key;
        $fundTransfersFtToBank             = $gpay->setPrivateKey($strPrivateKey)->fundTransfersFtToBank($arrRequest);
        $objWithdrawGpayLog->data_response = json_encode($fundTransfersFtToBank);
        $objWithdrawGpayLog->save();
        if (empty($fundTransfersFtToBank["success"])) {
            $msg                           = $fundTransfersFtToBank["data"]["meta"]["internal_msg"] ?? "";
            $code                          = $fundTransfersFtToBank["data"]["meta"]["code"] ?? "999999";
            $objWithdrawGpayLog->status_id = 3;
            $objWithdrawGpayLog->message   = "Lỗi " . $msg;
            $objWithdrawGpayLog->save();
            if ($objWithdrawGpayLog->save()) {
                /**
                 * Không hoàn tiền 
                 * "transfer_status": "ORDER_VERIFYING",
                 */
                // $transferStatus = $fundTransfersFtToBank["data"]["response"]["transfer_status"] ?? "";
                // if (!in_array($transferStatus, ["ORDER_VERIFYING"])) { // trường hợp này không cho huỷ giao dịch
                //     $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Lỗi  $msg"]);
                // } else {
                if (in_array($code, ["32006"])) {
                    $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Lỗi  $msg"]);
                } else {
                    $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 5, "note" => "Lỗi  $msg"]);
                }
            }
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tạo yêu  thất bại.")]
            ])->result();
        }

        $objWithdrawGpayLog->status_id = 2;
        $objWithdrawGpayLog->message   = "Thành công";
        $objWithdrawGpayLog->save();

        $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 2, "note" => "Thành công"]);
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
        $objUserWithdraw   = UserWithdraw::where('id', $intUserWithdrawId)->first();
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

        $strTransCode       = $objUserWithdraw->trans_code;
        $intUserId          = $objUserWithdraw->user_id;
        $objWithdrawGpayLog = WithdrawGpayLog::where('trans_code', $strTransCode)->where("user_id", $intUserId)->first();
        if ($objWithdrawGpayLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Yêu cầu đã được tạo vui lòng không tạo lại.")]
            ])->result();
        }

        $userWithdrawService = new UserWithdrawService();
        $objUser             = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy user.")]
            ])->result();
        }

        $gpay              = new Gpay();
        $objGatewayAccount = GatewayAccount::where('id', $objUserWithdraw->gateway_account_id)->where('gateway_id', 2)->first();
        if (!$objGatewayAccount) {
            $objWithdrawGpayLog->status_id = 3;
            $objWithdrawGpayLog->message   = "Không tồn tại gpay config";
            $objWithdrawGpayLog->save();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tồn tại yoobil config.")]
            ])->result();
        }



        $arrRequest = [
            "amount" => $objUserWithdraw->amount,
            "account_number" => $objUserWithdraw->bank_account_number,
            'bank_code' => $objBank->short_code,
            "full_name" => $objUserWithdraw->bank_account_name,
            'type' => "ACCOUNT_NUMBER",
            'transaction_id' => $objUserWithdraw->trans_code,
            'message' => $objUserWithdraw->remark,
        ];

        $objWithdrawGpayLog = WithdrawGpayLog::create([
            "user_id" => $intUserId,
            "trans_code" => $strTransCode,
            "data_request" => json_encode($arrRequest)
        ]);

        if (!$objWithdrawGpayLog) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận yêu cầu thất bại.")]
            ])->result();
        }


        $strPrivateKey                     = $objGatewayAccount->private_key;
        $fundTransfersFtToBank             = $gpay->setPrivateKey($strPrivateKey)->fundTransfersFtToBank($arrRequest);
        $objWithdrawGpayLog->data_response = json_encode($fundTransfersFtToBank);
        $objWithdrawGpayLog->save();
        if (empty($fundTransfersFtToBank["success"])) {
            $msg                           = $fundTransfersFtToBank["data"]["meta"]["internal_msg"] ?? "";
            $code                          = $fundTransfersFtToBank["data"]["meta"]["code"] ?? "999999";
            $objWithdrawGpayLog->status_id = 3;
            $objWithdrawGpayLog->message   = "Lỗi " . $msg;
            $objWithdrawGpayLog->save();
            if ($objWithdrawGpayLog->save()) {
                /**
                 * Không hoàn tiền 
                 * "transfer_status": "ORDER_VERIFYING",
                 */
                // $transferStatus = $fundTransfersFtToBank["data"]["response"]["transfer_status"] ?? "";
                // if (!in_array($transferStatus, ["ORDER_VERIFYING"])) { // trường hợp này không cho huỷ giao dịch
                //     $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Lỗi  $msg"]);
                // } else {
                if (in_array($code, ["32006"])) {
                    $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Lỗi  $msg"]);
                } else {
                    $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 5, "note" => "Lỗi  $msg"]);
                }
            }
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tạo yêu  thất bại.")]
            ])->result();
        }

        $objWithdrawGpayLog->status_id = 2;
        $objWithdrawGpayLog->message   = "Thành công";
        $objWithdrawGpayLog->save();

        $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 2, "note" => "Thành công"]);
        dispatch(new UpdateBalanceGatewayAccountJob([
            'id' => $objUserWithdraw->gateway_account_id,
        ]))->onQueue('request');
        return $this->setStatusCode(0)->setMessage(__("Tạo giao dịch thành công."))->setData([])->result();
    }

}