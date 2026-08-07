<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\Gateway;
use App\Models\GatewayAccount;
use App\Models\User;
use App\Models\UserToken;
use App\Models\UserVirtualAccount;
use App\Utilities\Gpay;
use App\Utilities\Yoobil;
use Illuminate\Support\Facades\Validator;

class UserVirtualAccountService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserVirtualAccount())->getFillable();
        $this->arrFillable = array_merge($this->arrFillable, (new Gateway())->getFillable());
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Đang bảo trì'
        ],
        2 => [
            'name' => 'Hoạt động'
        ]
    ];

    public function getList($arrParams = [])
    {

        $intPage = $arrParams["page"] ?? 1;
        $intLimit = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objUserVirtualAccounts = UserVirtualAccount::select(\DB::raw("user_virtual_accounts.*,gateways.name as gateway_name"));
        $objUserVirtualAccounts = $this->getListBuilder($objUserVirtualAccounts, $arrParams, $this->arrFillable);
        $objUserVirtualAccounts = $objUserVirtualAccounts->join('gateways', 'gateways.id', 'user_virtual_accounts.gateway_id');
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objUserVirtualAccounts;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUserVirtualAccounts = $objUserVirtualAccounts->orderBy("id", "DESC");
        }
        $objUserVirtualAccounts = $objUserVirtualAccounts->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'user_virtual_accounts' => $objUserVirtualAccounts,
            'records_total' => $intTotal,
            'status' => self::$arrStatusId,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }


    public function responseSelect2($arrResult = [])
    {
        if ($arrResult["error_code"] != 0) {
            return [];
        }

        $intLimit = $arrResult["data"]["limit"] ?? 1;
        $intPage = $arrResult["data"]["page"] ?? 1;

        $objBanks = $arrResult["data"]["user_virtual_accounts"];
        $arrData = [];
        foreach ($objBanks as $objBank) {
            $arrData[] = [
                "id" => $objBank->bank_account_number,
                "text" => $objBank->bank_account_name . " - " . $objBank->bank_account_number,
            ];
        }
        return ["results" => $arrData, "pagination" => ["more" => $arrResult["data"]["records_total"] >= ($intLimit * $intPage) ? true : false], 'limit' => $intLimit];
    }


    public function add($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "gateway_account_id" => "required",
                "bank_account_name" => "required",
                "user_id" => "required",
                "bank_id" => "required",
            ],
            [

                "gateway_account_id.required" => __("Vui lòng nhập gateway."),
                "bank_account_name.required" => __("Vui lòng nhập bank_account_name."),
                "user_id.required" => __("Vui lòng nhập user_id."),
                "bank_id.required" => __("Vui lòng nhập bank_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intGatewayAccountId = $arrParams["gateway_account_id"];
        $intUserId = $arrParams["user_id"];
        $strBankAccountName = $arrParams["bank_account_name"];
        $intBankId = $arrParams["bank_id"];


        $objGatewayAccount = GatewayAccount::where('id', $intGatewayAccountId)->first();
        if (empty($objGatewayAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin cổng thanh toán.")]
            ])->result();
        }

        $objUser = User::where('id', $intUserId)->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin người dùng.")]
            ])->result();
        }
        $intGatewayId = $objGatewayAccount->gateway_id;

        $objUserToken = UserToken::where('user_id', $intUserId)->first();
        if (empty($objUserToken)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy cấu hình token.")]
            ])->result();
        }

        $objBank = Bank::where('id', $intBankId)->first();
        if (empty($objBank)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy ngân hàng này.")]
            ])->result();
        }

        $strBankShortCode = $objBank->short_code;
        /**
         * YOOBILL
         */
        $orderNo = "";
        if ($intGatewayId == 3) {
            if ($strBankShortCode != "BIDV") {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Không hỗ trợ ngân hàng này.")]
                ])->result();
            }

            $yoobil = new Yoobil();
            $urlIpnCollection = url()->route('api.ipn.yoobil-collection', ["token" => $objUserToken->token_gateway]);
            $urlIpnPayout = url()->route('api.ipn.yoobil-payout', ["token" => $objUserToken->token_gateway]);

            $resultCreateVA = $yoobil->setSecretKey($objGatewayAccount->secret_key)
                ->setPrivateKey($objGatewayAccount->private_key)
                ->setBusinessId($objGatewayAccount->business_id)
                ->setMerchantId($objGatewayAccount->merchant_id)->createVA([
                        "userName" => strtoupper($strBankAccountName),
                        "returnUrl" => $urlIpnCollection,
                        "user_id" => $objUser->id
                    ]);
            if ($resultCreateVA['success'] == false) {
                return $this->setStatusCode(404)->setMessage("")->setData($resultCreateVA)->setErrors([
                    [__("Tạo tài khoản ảo thất bại: " . $resultCreateVA['message'])]
                ])->result();
            }
            $bankAccountNumber = $resultCreateVA['data']['result']["accountNo"] ?? "";
            $orderNo = $resultCreateVA['data']['result']["orderNo"] ?? "";
        } else if ($intGatewayId == 2) {
            if (!in_array($strBankShortCode, ["BIDV", "TCB", "MSB"])) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Không hỗ trợ ngân hàng này.")]
                ])->result();
            }

            $urlIpnCollection = url()->route('api.ipn.gpay-collection', ["token" => $objUserToken->token_gateway]);
            $urlIpnPayout = url()->route('api.ipn.gpay-payout', ["token" => $objUserToken->token_gateway]);

            $gpay = new Gpay();
            $resultTOken = ($gpay->createToken());
            $strToken = ($resultTOken["data"]["token"] ?? "");

            $resultCreateVirtualAccount = $gpay->setAuthentication($strToken)->setPrivateKey($objGatewayAccount->private_key)->createVirtualAccount(["account_name" => strtoupper($strBankAccountName), 'bank_code' => $strBankShortCode, 'account_type' => "M"]);
            if ($resultCreateVirtualAccount['success'] == false) {
                return $this->setStatusCode(404)->setMessage("")->setData($resultCreateVirtualAccount)->setErrors([
                    [__("Tạo tài khoản ảo thất bại: " . $resultCreateVirtualAccount['message'])]
                ])->result();
            }
            $bankAccountNumber = $resultCreateVirtualAccount['data']['account_number'] ?? "";
        } else {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cổng không hợp lệ.")]
            ])->result();
        }


        $objUserVirtualAccount = UserVirtualAccount::create([
            "gateway_id" => $intGatewayId,
            "gateway_account_id" => $intGatewayAccountId,
            "bank_account_name" => $strBankAccountName,
            "bank_account_number" => $bankAccountNumber,
            "bank_id" => $intBankId,
            "bank_short_name" => $strBankShortCode,
            "bank_short_code" => $strBankShortCode,
            "user_id" => $intUserId,
            "ipn_payout" => $urlIpnPayout,
            "ipn_collection" => $urlIpnCollection,
            "order_no" => $orderNo,
        ]);

        if (!$objUserVirtualAccount) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tạo tài khoản VA thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm thành công."))->setData(["user_virtual_account" => $objUserVirtualAccount])->result();
    }



    public function getDetail($arrParams = [])
    {

        $objUserVirtualAccount = UserVirtualAccount::select(\DB::raw('*'));
        $objUserVirtualAccount = $this->getListBuilder($objUserVirtualAccount, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objUserVirtualAccount = $objUserVirtualAccount->first();
        if (empty($objUserVirtualAccount)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['gateway_account' => $objUserVirtualAccount])->result();
    }

    public function changeStatus($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                'id' => 'required',
                'status_id' => 'required',
            ],
            [
                "id.required" => __("Vui lòng nhập id."),
                "status_id.required" => __("Vui lòng nhập trạng thái."),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intId = $arrParams["id"];
        $intStatusId = $arrParams["status_id"];
        if (empty(self::$arrStatusId[$intStatusId])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Trạng thái không hợp lệ.")]
            ])->result();
        }

        $objUserVirtualAccount = UserVirtualAccount::where('id', $intId)->first();
        if (empty($objUserVirtualAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        if ($objUserVirtualAccount->gateway_id == 3) {
            $arrGatewayAccount = GatewayAccount::find($objUserVirtualAccount->gateway_account_id);
            if (empty($arrGatewayAccount)) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Không tìm thấy tài khoản cổng.")]
                ])->result();
            }

            $arrUpdateVA = ["orderNo" => $objUserVirtualAccount->order_no, "status" => $intStatusId == 2 ? 0 : 1];
            $objUserToken = UserToken::where(['user_id' => $objUserVirtualAccount->user_id])->first();
            if ($objUserToken) {
                $arrUpdateVA["returnUrl"] = $objUserVirtualAccount->ipn_collection;
            }


            $yoobill = new Yoobil();
            $resultUpdateVA = $yoobill->setSecretKey($arrGatewayAccount->secret_key)
                ->setPrivateKey($arrGatewayAccount->private_key)
                ->setBusinessId($arrGatewayAccount->business_id)
                ->setMerchantId($arrGatewayAccount->merchant_id)->updateVA($arrUpdateVA);

            if ($resultUpdateVA['success'] == false) {
                return $this->setStatusCode(404)->setMessage("")->setData($resultUpdateVA)->setErrors([
                    [__(($intStatusId == 1 ? "Hủy" : "Kích hoạt") . " tài khoản ảo thất bại: " . $resultUpdateVA['message'])]
                ])->result();
            }
        }

        $objUserVirtualAccount->status_id = $intStatusId;
        if (!$objUserVirtualAccount->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData(["user_virtual_account" => $objUserVirtualAccount])->result();
    }
}