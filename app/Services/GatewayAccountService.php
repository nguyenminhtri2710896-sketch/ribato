<?php

namespace App\Services;

use App\Models\Gateway;
use App\Models\GatewayAccount;
use App\Models\GatewayAccountHistory;
use App\Models\UserWithdraw;
use App\Utilities\Gpay;
use App\Utilities\Paymenthot;
use App\Utilities\Yoobil;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

class GatewayAccountService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new GatewayAccount())->getFillable();
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

        $intPage   = $arrParams["page"] ?? 1;
        $intLimit  = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objGatewayAccounts = GatewayAccount::select(\DB::raw("gateway_accounts.*,gateways.name as gateway_name"));
        $objGatewayAccounts = $this->getListBuilder($objGatewayAccounts, $arrParams, $this->arrFillable);
        $objGatewayAccounts = $objGatewayAccounts->join('gateways', 'gateways.id', 'gateway_accounts.gateway_id');
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objGatewayAccounts;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objGatewayAccounts = $objGatewayAccounts->orderBy("id", "DESC");
        }
        $objGatewayAccounts = $objGatewayAccounts->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'gateway_accounts' => $objGatewayAccounts,
            'records_total' => $intTotal,
            'status' => self::$arrStatusId,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }

    public function getHistoryList($arrParams = [])
    {

        $intPage   = $arrParams["page"] ?? 1;
        $intLimit  = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objHistories       = GatewayAccountHistory::select(\DB::raw("*"));
        $arrFillableHistory = (new GatewayAccountHistory())->getFillable();
        $objHistories       = $this->getListBuilder($objHistories, $arrParams, $arrFillableHistory);

        $objTotal = $objHistories;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objHistories = $objHistories->orderBy("id", "DESC");
        }
        $objHistories = $objHistories->offset($intOffset)->limit($intLimit)->get();

        return $this->setStatusCode(0)->setData([
            'gateway_account_histories' => $objHistories,
            'records_total' => $intTotal,
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
        $intPage  = $arrResult["data"]["page"] ?? 1;

        $objBanks = $arrResult["data"]["gateway_accounts"];
        $arrData  = [];
        foreach ($objBanks as $objBank) {
            $arrData[] = [
                "id" => $objBank->id,
                "text" => $objBank->name,
            ];
        }
        return ["results" => $arrData, "pagination" => ["more" => $arrResult["data"]["records_total"] >= ($intLimit * $intPage) ? true : false], 'limit' => $intLimit];
    }


    public function add($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "name" => "required",
                "gateway_id" => "required",
            ],
            [
                "name.required" => __("Vui lòng nhập tên tài khoản."),
                "gateway_id.required" => __("Vui lòng nhập cổng."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $strGroupName      = $arrParams["name"];
        $intStatusId       = $arrParams["status_id"] ?? 1;
        
        $arrData = [
            "gateway_id" => $arrParams["gateway_id"],
            "name" => $strGroupName,
            "status_id" => $intStatusId,
            "username" => $arrParams["username"] ?? null,
            "tenant" => $arrParams["tenant"] ?? null,
            "private_key" => $arrParams["private_key"] ?? null,
            "public_key" => $arrParams["public_key"] ?? null,
        ];
        
        if (!empty($arrParams["password"])) {
            $arrData["password"] = \Illuminate\Support\Facades\Crypt::encryptString($arrParams["password"]);
        }
        
        if (!empty($arrParams["payout_pin"])) {
            $arrData["payout_pin"] = \Illuminate\Support\Facades\Crypt::encryptString($arrParams["payout_pin"]);
        }

        $objGatewayAccount = GatewayAccount::create($arrData);

        if (empty($objGatewayAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thêm Tài khoản thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm Tài khoản thành công."))->setData(["gateway_account" => $objGatewayAccount])->result();
    }

    public function update($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "name" => "required",
                "id" => "required",
            ],
            [

                "name.required" => __("Vui lòng nhập tên nhóm."),
                "id.required" => __("Vui lòng nhập mã nhóm."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $strGroupName      = $arrParams["name"];
        $intId             = $arrParams["id"];
        $intStatusId       = $arrParams["status_id"] ?? 1;
        $objGatewayAccount = GatewayAccount::where('id', $intId)->first();
        if (empty($objGatewayAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        $objGatewayAccount->name      = $strGroupName;
        $objGatewayAccount->status_id = $intStatusId;
        
        if (isset($arrParams["gateway_id"])) {
            $objGatewayAccount->gateway_id = $arrParams["gateway_id"];
        }
        if (isset($arrParams["username"])) {
            $objGatewayAccount->username = $arrParams["username"];
        }
        if (isset($arrParams["tenant"])) {
            $objGatewayAccount->tenant = $arrParams["tenant"];
        }
        if (!empty($arrParams["password"])) {
            $objGatewayAccount->password = \Illuminate\Support\Facades\Crypt::encryptString($arrParams["password"]);
        }
        if (!empty($arrParams["payout_pin"])) {
            $objGatewayAccount->payout_pin = \Illuminate\Support\Facades\Crypt::encryptString($arrParams["payout_pin"]);
        }
        if (!empty($arrParams["private_key"])) {
            $objGatewayAccount->private_key = $arrParams["private_key"];
        }
        if (!empty($arrParams["public_key"])) {
            $objGatewayAccount->public_key = $arrParams["public_key"];
        }

        if (!$objGatewayAccount->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData(["gateway_account" => $objGatewayAccount])->result();
    }

    public function delete($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập mã nhóm."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intId             = $arrParams["id"];
        $objGatewayAccount = GatewayAccount::where('id', $intId)->first();
        if (empty($objGatewayAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        if (!$objGatewayAccount->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xoá thành công."))->setData([])->result();
    }




    public function getDetail($arrParams = [])
    {

        $objGatewayAccount = GatewayAccount::select(\DB::raw('*'));
        $objGatewayAccount = $this->getListBuilder($objGatewayAccount, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objGatewayAccount = $objGatewayAccount->first();
        if (empty($objGatewayAccount)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }
        
        $objGatewayAccount->makeVisible(['username', 'tenant', 'private_key', 'public_key']);

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData($objGatewayAccount)->result();
    }

    public function updateBalance($arrParams = [])
    {


        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập mã nhóm."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intId             = $arrParams["id"];
        $objGatewayAccount = GatewayAccount::where('id', $intId)->first();
        if (!$objGatewayAccount) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy tài khoản.')]
            ])->result();
        }
        /**
         * 3: yoobil
         * 2: Gpay
         * 1: Paymenthot
         */
        $intTotalBalance        = 0;
        $intTotalPendingBalance = 0;
        if ($objGatewayAccount->gateway_id == 1) {
            $objGatewayAccount = $this->checkTokenPaymentHot($intId);
            if (!$objGatewayAccount) {
                return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                    [__('Lỗi tài khoản vui lòng kiểm tra lại.')]
                ])->result();
            }

            $paymenthot                   = new Paymenthot();
            $resultBalanceTechnicalWallet = $paymenthot->setAuthorization($objGatewayAccount->access_token)->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPassword($objGatewayAccount->password)->setPrivateKey($objGatewayAccount->private_key)->balanceTechnicalWallet();
            if (empty($resultBalanceTechnicalWallet["success"])) {
                return $this->setStatusCode(404)->setMessage('')->setData($resultBalanceTechnicalWallet)->setErrors([
                    [__('Không lấy được số dư tài khoản vui lòng kiểm tra lại.')]
                ])->result();
            }

            foreach ($resultBalanceTechnicalWallet["data"]["data"] ?? [] as $arrBalanceTechnicalWallet) {
                $intTotalBalance += $arrBalanceTechnicalWallet["totalBalance"];
            }

        } elseif ($objGatewayAccount->gateway_id == 2) {
            $gpay                               = new Gpay();
            $resultGetMerchantAccountInfomation = $gpay->setPrivateKey($objGatewayAccount->private_key)->getMerchantAccountInfomation();
            if (empty($resultGetMerchantAccountInfomation["success"])) {
                return $this->setStatusCode(404)->setMessage('')->setData($resultGetMerchantAccountInfomation)->setErrors([
                    [__('Không lấy được số dư tài khoản vui lòng kiểm tra lại.')]
                ])->result();
            }

            // dd($resultGetMerchantAccountInfomation);
            $intTotalBalance        = (int) ($resultGetMerchantAccountInfomation["data"]["amount_cash"] ?? 0);
            $intTotalPendingBalance = (int) ($resultGetMerchantAccountInfomation["data"]["amount_revenue"] ?? 0);
        } elseif ($objGatewayAccount->gateway_id == 3) {
            $yoobil           = new Yoobil();
            $resultGetBalance = $yoobil->setBusinessId($objGatewayAccount->business_id)
                ->setMerchantId($objGatewayAccount->merchant_id)
                ->setSecretKey($objGatewayAccount->secret_key)
                ->setPrivateKey($objGatewayAccount->private_key)
                ->getBalance();


            if (empty($resultGetBalance["success"])) {
                return $this->setStatusCode(404)->setMessage('')->setData($resultGetBalance)->setErrors([
                    [__('Không lấy được số dư tài khoản vui lòng kiểm tra lại.')]
                ])->result();
            }
            $intTotalBalance    = (int) ($resultGetBalance["data"]["result"]["balance"] ?? 0);
            $reusltTransactions = $yoobil->setBusinessId($objGatewayAccount->business_id)
                ->setMerchantId($objGatewayAccount->merchant_id)
                ->setSecretKey($objGatewayAccount->secret_key)
                ->setPrivateKey($objGatewayAccount->private_key)
                ->getTransaction([
                    "startTime" => (time() - (60 * 60 * 48)) * 1000,
                    "endTime" => (time() + (60 * 60 * 48)) * 1000,
                    "pageSize" => 1500,
                ]);

            foreach ($reusltTransactions["data"]["result"]["transactions"] ?? [] as $reusltTransaction) {
                if ($reusltTransaction["creditedStatus"] != 1) {
                    continue;
                }
                $intTotalPendingBalance += $reusltTransaction["creditedAmount"] ?? 0;
            }

        } else {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không xác định được thông tin cổng.')]
            ])->result();
        }

        $objGatewayAccount->balance         = $intTotalBalance;
        $objGatewayAccount->pending_balance = $intTotalPendingBalance;
        if (!$objGatewayAccount->save()) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Cập nhật thất bại.')]
            ])->result();
        }

        GatewayAccountHistory::create([
            "gateway_account_id" => $objGatewayAccount->id,
            "gateway_account_name" => $objGatewayAccount->name,
            "balance" => $objGatewayAccount->balance,
            "pending_balance" => $objGatewayAccount->pending_balance,
        ]);
        return $this->setStatusCode(0)->setMessage(__('Cập nhật thành công.'))->setData(['gateway_account' => $objGatewayAccount])->result();
    }

    public function getTransactionWithdrawPaymentHotByWithdrawId($arrParams = [])
    {


        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập mã nhóm."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intId           = $arrParams["id"];
        $objUserWithdraw = UserWithdraw::where('id', $intId)->first();
        if (!$objUserWithdraw) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy lệnh rút.')]
            ])->result();
        }

        $objGatewayAccount = GatewayAccount::where('id', $objUserWithdraw->gateway_account_id)->first();
        if (!$objGatewayAccount) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy tài khoản.')]
            ])->result();
        }
        /**
         * 3: yoobil
         * 2: Gpay
         * 1: Paymenthot
         */
        $objGatewayAccount = $this->checkTokenPaymentHot($objUserWithdraw->gateway_account_id);
        if (!$objGatewayAccount) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Lỗi tài khoản vui lòng kiểm tra lại.')]
            ])->result();
        }

        $paymenthot    = new Paymenthot();
        $resultInquiry = $paymenthot->setAuthorization($objGatewayAccount->access_token)->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPassword($objGatewayAccount->password)->setPrivateKey($objGatewayAccount->private_key)->inquiry([
            "txnDate" => $objUserWithdraw->created_at->format('Ymd'),
            "auditNumber" => $objUserWithdraw->trans_code,
        ]);
        if (empty($resultInquiry["success"])) {
            return $this->setStatusCode(404)->setMessage('')->setData($resultInquiry)->setErrors([
                [__('Không lấy được thông tin.')]
            ])->result();
        }



        return $this->setStatusCode(0)->setMessage(__('Lấy thành công.'))->setData(['inquiry' => $resultInquiry["data"]["data"] ?? []])->result();
    }

    public function getTransactionCollectionByTransactionId($arrParams = [])
    {


        $validated = Validator::make(
            $arrParams,
            [
                "txnDate" => "required",
                "auditNumber" => "required",
                "gateway_account_id" => "required",
            ],
            [

                "txnDate.required" => __("Vui lòng nhập txnDate."),
                "auditNumber.required" => __("Vui lòng nhập auditNumber."),
                "gateway_account_id.required" => __("Vui lòng nhập mã tài khoản."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strtxnDate          = $arrParams["txnDate"];
        $strAuditNumber      = $arrParams["auditNumber"];
        $intGatewayAccountID = $arrParams["gateway_account_id"];

        $objGatewayAccount = GatewayAccount::where('id', $intGatewayAccountID)->first();
        if (!$objGatewayAccount) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy tài khoản.')]
            ])->result();
        }
        /**
         * 3: yoobil
         * 2: Gpay
         * 1: Paymenthot
         */
        $objGatewayAccount = $this->checkTokenPaymentHot($intGatewayAccountID);
        if (!$objGatewayAccount) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Lỗi tài khoản vui lòng kiểm tra lại.')]
            ])->result();
        }
        dd($objGatewayAccount);
        $paymenthot    = new Paymenthot();
        $resultInquiry = $paymenthot->setAuthorization($objGatewayAccount->access_token)->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPassword($objGatewayAccount->password)->setPrivateKey($objGatewayAccount->private_key)->inquiry([
            "txnDate" => $strtxnDate,
            "auditNumber" => $strAuditNumber,
        ]);
        if (empty($resultInquiry["success"])) {
            return $this->setStatusCode(404)->setMessage('')->setData($resultInquiry)->setErrors([
                [__('Không lấy được thông tin.')]
            ])->result();
        }



        return $this->setStatusCode(0)->setMessage(__('Lấy thành công.'))->setData(['inquiry' => $resultInquiry["data"]["data"] ?? []])->result();
    }


    public function checkTokenPaymentHot($intGatewayAccount = 0, $intLoop = 0)
    {
        \DB::beginTransaction();
        $objGatewayAccount = GatewayAccount::where('id', $intGatewayAccount)->lockForUpdate()->first();
        if (!$objGatewayAccount) {
            \DB::rollBack();
            return false;
        }
        $paymenthot      = new Paymenthot();
        $getTotalBalance = $paymenthot->setAuthorization($objGatewayAccount->access_token)->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPassword($objGatewayAccount->password)->setPrivateKey($objGatewayAccount->private_key)->balanceTechnicalWallet();
        if (empty($getTotalBalance["success"])) {
            /**
             * Cho đăng nhập lại
             */
            $paymenthot  = new Paymenthot();
            $resultLogin = $paymenthot->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPassword($objGatewayAccount->password)->setPrivateKey($objGatewayAccount->private_key)->login();
            // dd("done",$resultLogin);
            if ($resultLogin['success']) {
                $strToken                        = $resultLogin["data"]["data"]["accessToken"] ?? "";
                $objGatewayAccount->access_token = $strToken;
                $objGatewayAccount->save();
                \DB::commit();
                return $objGatewayAccount;
            }
            \DB::rollBack();
            return false;
        }
        \DB::rollBack();
        return $objGatewayAccount;
    }


    //  $objGatewayAccount = $this->checkTokenPaymentHot($intId);
    //         if (!$objGatewayAccount) {
    //             return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
    //                 [__('Lỗi tài khoản vui lòng kiểm tra lại.')]
    //             ])->result();
    //         }

    //         $paymenthot = new Paymenthot();
    //         $resultBalanceTechnicalWallet = $paymenthot->setAuthorization($objGatewayAccount->access_token)->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPassword($objGatewayAccount->password)->setPrivateKey($objGatewayAccount->private_key)->balanceTechnicalWallet();
    //         if (empty($resultBalanceTechnicalWallet["success"])) {
    //             return $this->setStatusCode(404)->setMessage('')->setData($resultBalanceTechnicalWallet)->setErrors([
    //                 [__('Không lấy được số dư tài khoản vui lòng kiểm tra lại.')]
    //             ])->result();
    //         }

    //         foreach ($resultBalanceTechnicalWallet["data"]["data"] ?? [] as $arrBalanceTechnicalWallet) {
    //             $intTotalBalance += $arrBalanceTechnicalWallet["totalBalance"];
    //         }
}