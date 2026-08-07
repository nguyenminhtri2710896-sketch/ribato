<?php

namespace App\Services;

use App\Models\GatewayAccount;
use App\Models\GatewayAccountTransaction;
use Illuminate\Support\Facades\Validator;

class GatewayAccountTransactionService extends AbstractService
{

    public static $arrTypeId = [
        1 => [
            'name' => "Nạp tiền",
            'code' => "rec"
        ],
        2 => [
            'name' => "Rút tiền",
            'code' => "wit"
        ],
        3 => [
            'name' => "Nhận tiền",
            'code' => "tin"
        ],
        4 => [
            'name' => "Chuyển tiền",
            'code' => "tout"
        ],
        5 => [
            'name' => "Hoàn tiền",
            'code' => "ref"
        ],
        6 => [
            'name' => "Thanh toán",
            'code' => "pay"
        ],
        7 => [
            'name' => "Comission",
            'code' => "com"
        ],
        8 => [
            'name' => "Comission Đại lý",
            'code' => "aco"
        ]
    ];

    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new GatewayAccountTransaction())->getFillable();
    }


    public function getList($arrParams = [])
    {

        $intPage = $arrParams["page"] ?? 1;
        $intLimit = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;


        $objGatewayAccountTransactions = GatewayAccountTransaction::select(\DB::raw('gateway_account_transactions.*,gateway_accounts.name'))
            ->leftJoin('gateway_accounts', 'gateway_accounts.id', 'gateway_account_transactions.gateway_account_id');
        $objGatewayAccountTransactions = $this->getListBuilder($objGatewayAccountTransactions, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objGatewayAccountTransactions;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objGatewayAccountTransactions = $objGatewayAccountTransactions->orderBy("gateway_account_transactions.id", "DESC");
        }
        $objGatewayAccountTransactions = $objGatewayAccountTransactions->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'gateway_account_transactions' => $objGatewayAccountTransactions,
            'records_total' => $intTotal,
            'type' => self::$arrTypeId,
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

        $objGatewayAccountTransactions = $arrResult["data"]["gateway_account_transactions"];
        $arrData = [];
        foreach ($objGatewayAccountTransactions as $objGatewayAccountTransaction) {
            $arrData[] = [
                "id" => $objGatewayAccountTransaction->id,
                "text" => $objGatewayAccountTransaction->name,
            ];
        }
        return ["results" => $arrData, "pagination" => ["more" => $arrResult["data"]["records_total"] >= ($intLimit * $intPage) ? true : false], 'limit' => $intLimit];
    }

    /**
     * Nạp tiền
     */
    public function recharge($arrParams = [])
    {
        $intTypeId = 1;
        $intGatewayAccountId = $arrParams["gateway_account_id"] ?? 0;
        $intAmount = abs((int) $arrParams["amount"]);
        $strNote = $arrParams["note"] ?? "";
        $strTransCode = $arrParams["trans_code"] ?? "";

        $resultUpdateBalance = $this->updateBalance([
            "gateway_account_id" => $intGatewayAccountId,
            "user_id" => $arrParams["user_id"] ?? 0,
            "amount" => $intAmount,
            "type_id" => $intTypeId,
            "note" => $strNote,
            "trans_code" => $strTransCode,
        ]);

        if ($resultUpdateBalance["error_code"] != 0) {
            return $resultUpdateBalance;
        }

        return $resultUpdateBalance;
    }

    /**
     * Rút tiền
     */
    public function withDrawal($arrParams = [])
    {
        $intTypeId = 2;
        $intGatewayAccountId = $arrParams["gateway_account_id"] ?? 0;
        $intAmount = abs((int) $arrParams["amount"]);
        $strNote = $arrParams["note"] ?? "";
        $strTransCode = $arrParams["trans_code"] ?? "";

        $resultUpdateBalance = $this->updateBalance([
            "gateway_account_id" => $intGatewayAccountId,
            "user_id" => $arrParams["user_id"] ?? 0,
            "amount" => $intAmount * -1,
            "type_id" => $intTypeId,
            "note" => $strNote,
            "trans_code" => $strTransCode

        ]);

        if ($resultUpdateBalance["error_code"] != 0) {
            return $resultUpdateBalance;
        }
        return $resultUpdateBalance;
    }


    /**
     * Hoàn tiền
     */
    public function refund($arrParams = [])
    {
        $intTypeId = 5;
        $intGatewayAccountId = $arrParams["gateway_account_id"] ?? 0;
        $intAmount = abs((int) $arrParams["amount"]);
        $strNote = $arrParams["note"] ?? "";
        $strTransCode = $arrParams["trans_code"] ?? "";

        return $this->updateBalance([
            "gateway_account_id" => $intGatewayAccountId,
            "user_id" => $arrParams["user_id"] ?? 0,
            "amount" => $intAmount,
            "type_id" => $intTypeId,
            "note" => $strNote,
            "trans_code" => $strTransCode
        ]);
    }

    private function updateBalance($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "gateway_account_id" => "required",
                "amount" => "required",
            ],
            [
                "gateway_account_id.required" => __("Vui lòng nhập id tài khoản"),
                "amount.required" => __("Vui lòng nhập mệnh giá"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intTypeId = (int) ($arrParams["type_id"] ?? 0);
        $strNote = $arrParams["note"] ?? "";
        $intGatewayAccountId = $arrParams["gateway_account_id"];
        $intAmount = (int) $arrParams["amount"];
        $strTransCode = $arrParams["trans_code"] ?? "";
        \DB::beginTransaction();
        $objGatewayAccount = GatewayAccount::where(["id" => $intGatewayAccountId])->lockForUpdate()->first();
        if (!$objGatewayAccount) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Có lỗi xảy ra khi lấy thông tin số dư.")]
            ])->result();
        }


        if ($objGatewayAccount->balance + $intAmount < 0) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số dư không đủ để thực hiện giao dịch.")]
            ])->result();
        }

        $objGatewayAccount->balance = $objGatewayAccount->balance + $intAmount;
        if (!$objGatewayAccount->save()) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật số dư thất bại.")]
            ])->result();
        }

        $transCode = self::$arrTypeId[$intTypeId]["code"] . substr(md5($intTypeId . rand(100000, 999999) . time()), 0, 20);
        if (!empty($strTransCode)) {
            $transCode = $strTransCode;
        }


        $objGatewayAccountTransaction = GatewayAccountTransaction::create([
            "gateway_account_id" => $intGatewayAccountId,
            "type_id" => $intTypeId,
            "user_id" => $arrParams["user_id"] ?? 0,
            "trans_code" => $transCode,
            "amount" => $intAmount,
            "gateway_account_balance" => $objGatewayAccount->balance,
            "note" => $strNote,
        ]);
        if (!$objGatewayAccountTransaction) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Khởi tạo giao dịch thất bại.")]
            ])->result();
        }

        // if (!empty($arrParams["user_id"])) {
        //     $userTransactionService = new UserTransactionService();
        //     $resultUpdateBalance = $userTransactionService->updateBalance($arrParams);
        //     if ($resultUpdateBalance["error_code"] != 0) {
        //         \DB::rollBack();
        //         return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
        //             [__("Có lỗi xử lý người dùng.")]
        //         ])->result();
        //     }
        // }
        \DB::commit();
        return $this->setStatusCode(0)->setMessage(__("Thành công."))->setData(["gateway_account" => $objGatewayAccount, "gateway_account_transaction" => $objGatewayAccountTransaction])->result();
    }
}