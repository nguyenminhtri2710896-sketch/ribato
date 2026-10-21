<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\Gateway;
use App\Models\GatewayAccount;
use App\Models\User;
use App\Models\UserToken;
use App\Models\UserVirtualAccount;
use App\Utilities\Gpay;
use App\Utilities\GpayV2;
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

        /**
         * Loại tài khoản (O: dùng 1 lần, M: dùng nhiều lần) và hạn mức giao dịch
         */
        $strAccountType = strtoupper((string) ($arrParams["account_type"] ?? GpayV2::VA_TYPE_MULTIPLE));
        $intMinAmount = isset($arrParams["min_amount"]) && $arrParams["min_amount"] !== "" ? (int) $arrParams["min_amount"] : null;
        $intMaxAmount = isset($arrParams["max_amount"]) && $arrParams["max_amount"] !== "" ? (int) $arrParams["max_amount"] : null;

        $validatedLimit = Validator::make(
            ["account_type" => $strAccountType, "min_amount" => $intMinAmount, "max_amount" => $intMaxAmount],
            [
                "account_type" => "in:" . GpayV2::VA_TYPE_ONE_TIME . "," . GpayV2::VA_TYPE_MULTIPLE,
                "min_amount" => "nullable|integer|min:0",
                "max_amount" => "nullable|integer|min:0",
            ],
            [
                "account_type.in" => __("Loại tài khoản không hợp lệ (O: dùng 1 lần, M: dùng nhiều lần)."),
                "min_amount.integer" => __("Giá trị tối thiểu không hợp lệ."),
                "min_amount.min" => __("Giá trị tối thiểu không hợp lệ."),
                "max_amount.integer" => __("Giá trị tối đa không hợp lệ."),
                "max_amount.min" => __("Giá trị tối đa không hợp lệ."),
            ]
        );
        if ($validatedLimit->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validatedLimit->errors()->messages())->result();
        }
        if ($intMinAmount && $intMaxAmount && $intMinAmount > $intMaxAmount) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors([
                [__("Giá trị tối thiểu không được lớn hơn giá trị tối đa.")]
            ])->result();
        }
        $intMinAmount = $intMinAmount ?: null;
        $intMaxAmount = $intMaxAmount ?: null;


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
        if ($objGatewayAccount->gateway_id != 8) {
            // Chỉ cổng GPAY V2 hỗ trợ loại tài khoản và hạn mức
            $strAccountType = GpayV2::VA_TYPE_MULTIPLE;
            $intMinAmount = null;
            $intMaxAmount = null;
        }
        /**
         * YOOBILL
         */
        $orderNo = "";
        if (app()->environment('sandbox')) {
            /**
             * SANDBOX: tạo VA giả lập, không gọi đến cổng thanh toán hay dịch vụ bên ngoài nào
             */
            if ($intGatewayId == 3) {
                $urlIpnCollection = url()->route('api.ipn.yoobil-collection', ["token" => $objUserToken->token_gateway]);
                $urlIpnPayout = url()->route('api.ipn.yoobil-payout', ["token" => $objUserToken->token_gateway]);
            } elseif ($intGatewayId == 8) {
                $urlIpnCollection = url()->route('api.gpay.ipn', ["token" => $objUserToken->token_gateway]);
                $urlIpnPayout = $urlIpnCollection;
            } else {
                $urlIpnCollection = url()->route('api.ipn.gpay-collection', ["token" => $objUserToken->token_gateway]);
                $urlIpnPayout = url()->route('api.ipn.gpay-payout', ["token" => $objUserToken->token_gateway]);
            }

            do {
                $bankAccountNumber = "SBX" . $strBankShortCode . random_int(10000000000, 99999999999);
            } while (UserVirtualAccount::where('bank_account_number', $bankAccountNumber)->exists());
            $orderNo = "SBX" . $objUser->id . "_" . uniqid();
        } else if ($intGatewayId == 3) {
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
        } else if ($intGatewayId == 8) {
            if (!in_array($strBankShortCode, ["BIDV","MB", "TCB", "MSB", "VCCB", "VPB", "WOO"])) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Không hỗ trợ ngân hàng này cho cổng GPAY V2.")]
                ])->result();
            }

            $urlIpnCollection = url()->route('api.gpay.ipn', ["token" => $objUserToken->token_gateway]);
            $urlIpnPayout     = url()->route('api.gpay.ipn', ["token" => $objUserToken->token_gateway]);

            $gpayV2 = GpayV2::fromGatewayAccount($objGatewayAccount);
            $resultCreateVirtualAccount = $gpayV2->createVirtualAccount([
                "account_name" => strtoupper($strBankAccountName),
                "bank_code"    => $strBankShortCode,
                "account_type" => $strAccountType,
                "map_type"     => GpayV2::MAP_TYPE_CUSTOMER_ID,
                // VA dùng 1 lần cần map_id duy nhất cho mỗi lần tạo
                "map_id"       => $strAccountType == GpayV2::VA_TYPE_ONE_TIME ? ($objUser->id . "_" . uniqid()) : (string) $objUser->id,
                "description"  => "VA {$objUser->id} - {$strBankAccountName}",
                "min_amount"   => $intMinAmount,
                "max_amount"   => $intMaxAmount,
            ]);

            if (empty($resultCreateVirtualAccount['success'])) {
                return $this->setStatusCode(404)->setMessage("")->setData($resultCreateVirtualAccount)->setErrors([
                    [__("Tạo tài khoản ảo thất bại: " . ($resultCreateVirtualAccount['message'] ?? ''))]
                ])->result();
            }

            $bankAccountNumber = $resultCreateVirtualAccount['account_number'] ?? ($resultCreateVirtualAccount['data']['account_number'] ?? "");
            $orderNo           = $resultCreateVirtualAccount['data']['map_id'] ?? ((string) $objUser->id);
        } else {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cổng không hợp lệ.")]
            ])->result();
        }


        do {
            $strOrderCode = strtoupper(\Illuminate\Support\Str::random(16));
        } while (UserVirtualAccount::where('order_code', $strOrderCode)->exists());

        $objUserVirtualAccount = UserVirtualAccount::create([
            "order_code" => $strOrderCode,
            "gateway_id" => $intGatewayId,
            "gateway_account_id" => $intGatewayAccountId,
            "bank_account_name" => ($strBankShortCode=="BIDV"?"GVA ":"") . $strBankAccountName,
            "bank_account_number" => $bankAccountNumber,
            "bank_id" => $intBankId,
            "bank_short_name" => $strBankShortCode,
            "bank_short_code" => $strBankShortCode,
            "user_id" => $intUserId,
            "ipn_payout" => $urlIpnPayout,
            "ipn_collection" => $urlIpnCollection,
            "order_no" => $orderNo,
            "account_type" => $strAccountType,
            "min_amount" => $intMinAmount,
            "max_amount" => $intMaxAmount,
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

    /**
     * Tìm VA theo order_code (và user_id nếu có, để đảm bảo VA thuộc user đó quản lý)
     */
    private function findByOrderCode($arrParams = [])
    {
        $strOrderCode = trim((string) ($arrParams["order_code"] ?? ""));
        if ($strOrderCode === "") {
            return null;
        }

        $objQuery = UserVirtualAccount::where('order_code', $strOrderCode);
        if (!empty($arrParams["user_id"])) {
            $objQuery->where('user_id', $arrParams["user_id"]);
        }
        return $objQuery->first();
    }

    /**
     * Kích hoạt / hủy kích hoạt VA theo order_code
     * $intStatusId: 2 = hoạt động, 1 = ngừng hoạt động
     */
    public function changeStatusByOrderCode($arrParams = [], $intStatusId = 2)
    {
        $objUserVirtualAccount = $this->findByOrderCode($arrParams);
        if (empty($objUserVirtualAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy tài khoản VA.")]
            ])->result();
        }

        return $this->changeStatus(["id" => $objUserVirtualAccount->id, "status_id" => $intStatusId]);
    }

    /**
     * Cập nhật loại tài khoản (1 lần / nhiều lần) và hạn mức tối thiểu / tối đa của VA
     */
    public function updateByOrderCode($arrParams = [])
    {
        $objUserVirtualAccount = $this->findByOrderCode($arrParams);
        if (empty($objUserVirtualAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy tài khoản VA.")]
            ])->result();
        }

        $blnHasType = isset($arrParams["account_type"]) && $arrParams["account_type"] !== "";
        $blnHasMin = array_key_exists("min_amount", $arrParams) && $arrParams["min_amount"] !== null && $arrParams["min_amount"] !== "";
        $blnHasMax = array_key_exists("max_amount", $arrParams) && $arrParams["max_amount"] !== null && $arrParams["max_amount"] !== "";
        if (!$blnHasType && !$blnHasMin && !$blnHasMax) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors([
                [__("Vui lòng nhập ít nhất một trong các trường: account_type, min_amount, max_amount.")]
            ])->result();
        }

        $strAccountType = $blnHasType ? strtoupper((string) $arrParams["account_type"]) : ($objUserVirtualAccount->account_type ?: GpayV2::VA_TYPE_MULTIPLE);
        $intMinAmount = $blnHasMin ? $arrParams["min_amount"] : $objUserVirtualAccount->min_amount;
        $intMaxAmount = $blnHasMax ? $arrParams["max_amount"] : $objUserVirtualAccount->max_amount;

        $validated = Validator::make(
            ["account_type" => $strAccountType, "min_amount" => $intMinAmount, "max_amount" => $intMaxAmount],
            [
                "account_type" => "in:" . GpayV2::VA_TYPE_ONE_TIME . "," . GpayV2::VA_TYPE_MULTIPLE,
                "min_amount" => "nullable|integer|min:0",
                "max_amount" => "nullable|integer|min:0",
            ],
            [
                "account_type.in" => __("Loại tài khoản không hợp lệ (O: dùng 1 lần, M: dùng nhiều lần)."),
                "min_amount.integer" => __("Giá trị tối thiểu không hợp lệ."),
                "min_amount.min" => __("Giá trị tối thiểu không hợp lệ."),
                "max_amount.integer" => __("Giá trị tối đa không hợp lệ."),
                "max_amount.min" => __("Giá trị tối đa không hợp lệ."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intMinAmount = $intMinAmount ? (int) $intMinAmount : null;
        $intMaxAmount = $intMaxAmount ? (int) $intMaxAmount : null;
        if ($intMinAmount && $intMaxAmount && $intMinAmount > $intMaxAmount) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors([
                [__("Giá trị tối thiểu không được lớn hơn giá trị tối đa.")]
            ])->result();
        }

        if ($objUserVirtualAccount->gateway_id != 8) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản VA này không hỗ trợ cập nhật loại tài khoản và hạn mức.")]
            ])->result();
        }

        if (!app()->environment('sandbox')) {
            $objGatewayAccount = GatewayAccount::find($objUserVirtualAccount->gateway_account_id);
            if (empty($objGatewayAccount)) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Không tìm thấy tài khoản cổng.")]
                ])->result();
            }

            $strAccountName = $objUserVirtualAccount->bank_account_name;
            if ($objUserVirtualAccount->bank_short_code == "BIDV" && strpos($strAccountName, "GVA ") === 0) {
                $strAccountName = substr($strAccountName, 4);
            }

            $gpayV2 = GpayV2::fromGatewayAccount($objGatewayAccount);
            $resultUpdate = $gpayV2->updateVirtualAccount([
                "account_number" => $objUserVirtualAccount->bank_account_number,
                "account_name" => $strAccountName,
                "account_type" => $strAccountType,
                "min_amount" => $intMinAmount,
                "max_amount" => $intMaxAmount,
            ]);
            if (empty($resultUpdate['success'])) {
                return $this->setStatusCode(404)->setMessage("")->setData($resultUpdate)->setErrors([
                    [__("Cập nhật tài khoản ảo thất bại: " . ($resultUpdate['message'] ?? ''))]
                ])->result();
            }
        }

        $objUserVirtualAccount->account_type = $strAccountType;
        $objUserVirtualAccount->min_amount = $intMinAmount;
        $objUserVirtualAccount->max_amount = $intMaxAmount;
        if (!$objUserVirtualAccount->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData(["user_virtual_account" => $objUserVirtualAccount])->result();
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

        if (app()->environment('sandbox')) {
            // SANDBOX: chỉ cập nhật trạng thái nội bộ, không gọi cổng thanh toán
        } elseif ($objUserVirtualAccount->gateway_id == 3) {
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
        } elseif ($objUserVirtualAccount->gateway_id == 8) {
            $arrGatewayAccount = GatewayAccount::find($objUserVirtualAccount->gateway_account_id);
            if (empty($arrGatewayAccount)) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Không tìm thấy tài khoản cổng.")]
                ])->result();
            }

            $gpayV2 = GpayV2::fromGatewayAccount($arrGatewayAccount);
            if ($intStatusId == 2) {
                $resultVaStatus = $gpayV2->reopenVirtualAccount([
                    'account_number' => $objUserVirtualAccount->bank_account_number,
                    'bank_code'      => $objUserVirtualAccount->bank_short_code,
                ]);
            } else {
                $resultVaStatus = $gpayV2->closeVirtualAccount([
                    'account_number' => $objUserVirtualAccount->bank_account_number,
                    'bank_code'      => $objUserVirtualAccount->bank_short_code,
                ]);
            }

            if (empty($resultVaStatus['success'])) {
                return $this->setStatusCode(404)->setMessage("")->setData($resultVaStatus)->setErrors([
                    [__(($intStatusId == 1 ? "Hủy" : "Kích hoạt") . " tài khoản ảo thất bại: " . ($resultVaStatus['message'] ?? ''))]
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