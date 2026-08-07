<?php

namespace App\Services;

use App\Exports\UserTransactionExport;
use App\Models\UserBalance;
use App\Models\User;
use App\Models\UserTransaction;
use App\Models\UserTransfer;
use App\Models\UserWithdraw;
use Illuminate\Support\Facades\Validator;

class UserTransactionService extends AbstractService
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
        $this->arrFillable = (new UserTransaction())->getFillable();
    }


    public function getList($arrParams = [])
    {

        $intPage = $arrParams["page"] ?? 1;
        $intLimit = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;


        $objUserTransactions = UserTransaction::select(\DB::raw('user_transactions.*,users.fullname'))
            ->leftJoin('users', 'users.id', 'user_transactions.user_id');
        $objUserTransactions = $this->getListBuilder($objUserTransactions, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objUserTransactions;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUserTransactions = $objUserTransactions->orderBy("user_transactions.id", "DESC");
        }
        $objUserTransactions = $objUserTransactions->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'user_transactions' => $objUserTransactions,
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

        $objUserTransactions = $arrResult["data"]["user_transactions"];
        $arrData = [];
        foreach ($objUserTransactions as $objUserTransaction) {
            $arrData[] = [
                "id" => $objUserTransaction->id,
                "text" => $objUserTransaction->name,
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
        $intUseriD = $arrParams["user_id"] ?? 0;
        $intAmount = abs((int) $arrParams["amount"]);
        $strNote = $arrParams["note"] ?? "";
        $strTransCode = $arrParams["trans_code"] ?? "";

        $resultUpdateBalance = $this->updateBalance([
            "user_id" => $intUseriD,
            "amount" => $intAmount,
            "type_id" => $intTypeId,
            "note" => $strNote,
            "trans_code" => $strTransCode,
        ]);

        // if (empty($resultUpdateBalance["success"])) {
        //     return $resultUpdateBalance;
        // }
        return $resultUpdateBalance;
    }

    /**
     * Rút tiền
     */
    public function withDrawal($arrParams = [])
    {
        $intTypeId = 2;
        $intUseriD = $arrParams["user_id"] ?? 0;
        $intAmount = abs((int) $arrParams["amount"]);
        $strNote = $arrParams["note"] ?? "";
        $strTransCode = $arrParams["trans_code"] ?? "";

        $resultUpdateBalance = $this->updateBalance([
            "user_id" => $intUseriD,
            "amount" => $intAmount * -1,
            "type_id" => $intTypeId,
            "note" => $strNote,
            "trans_code" => $strTransCode

        ]);

        // if (empty($resultUpdateBalance["success"])) {
        //     return $resultUpdateBalance;
        // }
        return $resultUpdateBalance;
    }

    /**
     * Chuyển tiền
     */

    public function transfer($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "from_user_id" => "required",
                "to_user_id" => "required",
                "amount" => "required",
            ],
            [
                "from_user_id.required" => __("Vui lòng nhập id người chuyển tiền."),
                "to_user_id.required" => __("Vui lòng nhập id người nhận tiền"),
                "amount.required" => __("Vui lòng nhập mệnh giá"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intFromUserId = (int) ($arrParams["from_user_id"] ?? 0);
        $intToUserId = (int) ($arrParams["to_user_id"] ?? 0);
        $intAmount = abs((int) $arrParams["amount"]);
        $strNote = $arrParams["note"] ?? "";

        if ($intFromUserId == $intToUserId) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người nhận tiền không hợp lệ.")]
            ])->result();
        }

        $objFromUser = User::where('id', $intFromUserId)->first();
        if (!$objFromUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người gửi không tồn tại.")]
            ])->result();
        }

        $objToUser = User::where('id', $intToUserId)->first();
        if (!$objToUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người nhận không tồn tại.")]
            ])->result();
        }

        /**
         * Kiểm tra lại chỗ này,
         * Nếu user không tồn tại trong bảng user_balance sẽ không được cộng số dư, vì nó nằm trong transaction nên ko thể tạo được
         */
        \DB::beginTransaction();
        $objFromUserBalance = UserBalance::where(["user_id" => $intFromUserId])->lockForUpdate()->first();
        if (!$objFromUserBalance) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số dư người nhận không tồn tại.")]
            ])->result();
        }

        $objToUserBalance = UserBalance::where(["user_id" => $intToUserId])->lockForUpdate()->first();
        if (!$objToUserBalance) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số dư người nhận chưa được khai báo.")]
            ])->result();
        }


        if ($objFromUserBalance->balance - $intAmount < 0) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số dư người gửi không đủ để thực hiện giao dịch.")]
            ])->result();
        }

        $objFromUserBalance->balance = $objFromUserBalance->balance - $intAmount;
        if (!$objFromUserBalance->save()) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật số dư thất bại.")]
            ])->result();
        }

        $objToUserBalance->balance = $objToUserBalance->balance + $intAmount;
        if (!$objToUserBalance->save()) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật số dư thất bại.")]
            ])->result();
        }



        $intTypeId = 4;
        $transCode = self::$arrTypeId[$intTypeId]["code"] . substr(md5($intTypeId . $intFromUserId . rand(10000, 99999)), 0, 20);
        $objFromUserTransaction = UserTransaction::create([
            "user_id" => $intFromUserId,
            "type_id" => $intTypeId,
            "trans_code" => $transCode,
            "amount" => $intAmount * -1,
            "user_balance" => $objFromUserBalance->balance,
            "note" => !empty($strNote) ? $strNote : "Chuyển tiền cho  $objToUser->email",
        ]);

        if (!$objFromUserTransaction) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Khởi tạo giao dịch thất bại.")]
            ])->result();
        }

        $intTypeId = 3;
        $strNote = !empty($strNote) ? $strNote : "Nhận tiền từ $objFromUser->email";
        if ($objFromUser->id == 270) {
            $strNote = !empty($strNote) ? $strNote : "[$objFromUser->id] Nạp tiền vào tài khoản";
        }

        $transCode = self::$arrTypeId[$intTypeId]["code"] . substr(md5($intTypeId . $intFromUserId . rand(10000, 99999)), 0, 20);
        $objToUserTransaction = UserTransaction::create([
            "user_id" => $intToUserId,
            "type_id" => $intTypeId,
            "trans_code" => $transCode,
            "amount" => $intAmount,
            "user_balance" => $objToUserBalance->balance,
            "note" => $strNote,
        ]);

        if (!$objToUserTransaction) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Khởi tạo giao dịch thất bại.")]
            ])->result();
        }

        $objUserTransfer = UserTransfer::create([
            'from_user_transaction_id' => $objFromUserTransaction->id,
            'to_user_transaction_id' => $objToUserTransaction->id,
            "from_user_id" => $intFromUserId,
            "to_user_id" => $intToUserId,
            "amount" => $intAmount,
            "from_user_balance" => $objFromUserBalance->balance,
            "to_user_balance" => $objToUserBalance->balance,
        ]);

        if (!$objUserTransfer) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận giao dịch thất bại.")]
            ])->result();
        }

        $objUserWithDraw = UserWithdraw::create([
            'gateway_id' => 0,
            'user_id' => $objFromUser->id,
            'user_email' => $objFromUser->email,
            'bank_id' => 0,
            'user_transaction_id' => $objFromUserTransaction->id,
            'remark' => "Chuyển khoản nội bộ: ".$strNote,
            'trans_code' => $transCode,
            'bank_short_name' => "LOCAL",
            'bank_account_number' => "",
            'bank_account_name' => $objToUser->email,
            'amount' => $intAmount,
            'fee' => 0,
            'status_id' => 2,
            'amount_after_fee' => $intAmount,
            "gateway_fee" => 0,
            "referal_fee" => 0,
            "profit" => 0,
            "user_fee_json" => json_encode([]),
            "gateway_fee_json" => json_encode([]),
            "referal_fee_json" => json_encode([]),
            'is_tranfer_local' => 1,
            'platform' => "web"
        ]);

        if (!$objUserWithDraw) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận chuyển tiền thất bại.")]
            ])->result();
        }

        \DB::commit();
        return $this->setStatusCode(0)->setMessage(__("Chuyển tiền thành công với số tiền :balance đ.", ["balance" => number_format($intAmount)]))->setData([
            "from_user" => $objFromUser,
            "to_user" => $objToUser,
            "from_user_balance" => $objFromUserBalance,
            "to_user_balance" => $objToUserBalance,
            "from_user_transaction" => $objFromUserTransaction,
            "to_user_transaction" => $objToUserTransaction
        ])->result();
    }

    /**
     * Hoàn tiền
     */
    public function refund($arrParams = [])
    {
        $intTypeId = 5;
        $intUseriD = $arrParams["user_id"] ?? 0;
        $intAmount = abs((int) $arrParams["amount"]);
        $strNote = $arrParams["note"] ?? "";
        $strTransCode = $arrParams["trans_code"] ?? "";

        return $this->updateBalance([
            "user_id" => $intUseriD,
            "amount" => $intAmount,
            "type_id" => $intTypeId,
            "note" => $strNote,
            "trans_code" => $strTransCode
        ]);
    }


    /**
     * Commission
     */
    public function comission($arrParams = [])
    {
        $intTypeId = 7;
        $intUseriD = $arrParams["user_id"] ?? 0;
        $intAmount = abs((int) $arrParams["amount"]);
        $strNote = $arrParams["note"] ?? "";
        $strTransCode = $arrParams["trans_code"] ?? "";

        return $this->updateBalance([
            "user_id" => $intUseriD,
            "amount" => $intAmount,
            "type_id" => $intTypeId,
            "note" => $strNote,
            "trans_code" => $strTransCode
        ]);
    }

    /**
     * Trả chi phí Commission cho đại lý
     */
    public function comissionAgent($arrParams = [])
    {
        $intTypeId = 8;
        $intUseriD = $arrParams["user_id"] ?? 0;
        $intAmount = abs((int) $arrParams["amount"]);
        $strNote = $arrParams["note"] ?? "";
        $strTransCode = $arrParams["trans_code"] ?? "";

        return $this->updateBalance([
            "user_id" => $intUseriD,
            "amount" => $intAmount,
            "type_id" => $intTypeId,
            "note" => $strNote,
            "trans_code" => $strTransCode
        ]);
    }

    /**
     * Thanh toán tiền dịch vụ
     */
    public function payment($arrParams = [])
    {
        $intTypeId = 6;
        $intUseriD = $arrParams["user_id"] ?? 0;
        $intAmount = abs((int) $arrParams["amount"]);
        $strNote = $arrParams["note"] ?? "";
        $strTransCode = $arrParams["trans_code"] ?? "";

        return $this->updateBalance([
            "user_id" => $intUseriD,
            "amount" => $intAmount * -1,
            "type_id" => $intTypeId,
            "note" => $strNote,
            "trans_code" => $strTransCode
        ]);
    }

    public function updateBalance($arrParams = [])
    {


        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
                "amount" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập id người dùng"),
                "amount.required" => __("Vui lòng nhập mệnh giá"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intTypeId = (int) ($arrParams["type_id"] ?? 0);
        $strNote = $arrParams["note"] ?? "";
        $intUserID = $arrParams["user_id"];
        $intAmount = (int) $arrParams["amount"];
        $strTransCode = $arrParams["trans_code"] ?? "";
        $objUser = User::where('id', $intUserID)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        \DB::beginTransaction();
        $objUserBalance = UserBalance::where(["user_id" => $intUserID])->lockForUpdate()->first();
        if (!$objUserBalance) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Có lỗi xảy ra khi lấy thông tin số dư.")]
            ])->result();
        }


        if ($objUserBalance->balance + $intAmount < 0) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số dư không đủ để thực hiện giao dịch.")]
            ])->result();
        }

        $objUserBalance->balance = $objUserBalance->balance + $intAmount;
        if (!$objUserBalance->save()) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật số dư thất bại.")]
            ])->result();
        }

        $transCode = self::$arrTypeId[$intTypeId]["code"] . substr(md5($intTypeId . rand(100000, 999999) . time()), 0, 20);
        if (!empty($strTransCode)) {
            $transCode = $strTransCode;
        }


        $objUserTransaction = UserTransaction::create([
            "user_id" => $intUserID,
            "type_id" => $intTypeId,
            "trans_code" => $transCode,
            "amount" => $intAmount,
            "user_balance" => $objUserBalance->balance,
            "note" => $strNote,
        ]);
        if (!$objUserTransaction) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Khởi tạo giao dịch thất bại.")]
            ])->result();
        }

        \DB::commit();
        return $this->setStatusCode(0)->setMessage(__("Thành công."))->setData(["user" => $objUser, "user_balance" => $objUserBalance, "user_transaction" => $objUserTransaction])->result();
    }


    public function exportExcel($arrParams = [])
    {
        $objUserTransactions = UserTransaction::select();
        $objUserTransactions = $this->getListBuilder($objUserTransactions, $arrParams, $this->arrFillable)->orderBy('id', 'ASC')->get();
        if (!$objUserTransactions) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([[__("Không tìm thấy sữ liệu để xuất.")]])->result();
        }
        $fileName = 'exports/export_user_transaction_' . time() . '.xlsx';
        $resultExport = \Excel::store(new UserTransactionExport(['objUserTransactions' => $objUserTransactions, 'types' => self::$arrTypeId]), $fileName, 'export-excel', null);
        if (!$resultExport) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Xuất file thất bại.')]])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xuất giao dịch thành công."))->setData(["url" => url("static/" . $fileName)])->result();
    }
}