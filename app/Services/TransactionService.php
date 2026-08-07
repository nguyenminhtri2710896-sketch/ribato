<?php

namespace App\Services;

use App\Exports\TransactionExport;
use App\Exports\TransactionFullaccessExport;
use App\Jobs\TransactionCallbackResultJob;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Gateway;
use App\Models\GatewayFee;
use App\Models\GatewayForward;
use App\Models\Transaction;
use App\Models\TransactionCallback;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserFee;
use App\Models\UserIdQrcode;
use App\Models\UserReferalFee;
use App\Models\UserToken;
use App\Utilities\General;
use Curl\Curl;
use Illuminate\Support\Facades\Validator;
use tttran\viet_qr_generator\Generator;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class TransactionService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new Transaction())->getFillable();
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Đang xử lý'
        ],
        2 => [
            'name' => 'Thành công'
        ],
        3 => [
            'name' => 'Thất bại'
        ],
        5 => [
            'name' => 'Chờ kiểm tra giao dịch'
        ],
        6 => [
            'name' => 'Chờ đối soát'
        ],
        7 => [
            'name' => 'Chuyển tiếp'
        ]
    ];


    public static $arrCallbackStatusId = [
        1 => [
            'name' => 'Đang xử lý'
        ],
        2 => [
            'name' => 'Thành công'
        ],
        3 => [
            'name' => 'Thất bại'
        ]
    ];

    public static $arrTypeId = [
        1 => [
            'name' => 'Nạp'
        ],
        2 => [
            'name' => 'Rút'
        ]
    ];


    public static $arrCurrency = [
        'usd' => [
            'name' => 'USD'
        ],
        'vnd' => [
            'name' => 'Việt Nam đồng'
        ]
    ];

    public function getList($arrParams = [])
    {

        $intPage = $arrParams["page"] ?? 1;
        $intLimit = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objTransactions = Transaction::select();
        $objTransactions = $this->getListBuilder($objTransactions, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objTransactions;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objTransactions = $objTransactions->orderBy("id", "DESC");
        }
        $objTransactions = $objTransactions->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'transactions' => $objTransactions,
            'records_total' => $intTotal,
            'status' => self::$arrStatusId,
            'type' => self::$arrTypeId,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }

    public function getDetail($arrParams = [])
    {


        $objTransaction = Transaction::select();
        $objTransaction = $this->getListBuilder($objTransaction, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTransaction = $objTransaction->first();
        if (empty($objTransaction)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['transaction' => $objTransaction])->result();
    }




    public function createPayment($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                'ref_code' => "required|max:80",
                'user_id' => "required",
                'amount' => "required",
                'user_token_id' => "required",
            ],
            [

                "ref_code.required" => __("Vui lòng nhập mã đơn của bạn."),
                "ref_code.max" => __("Mã tham chiếu phải nhỏ hơn :max ký tự ."),
                "user_id.required" => __("Vui lòng nhập user_id."),
                "amount.required" => __("Vui lòng nhập số tiền cần nạp."),
                "user_token_id.required" => __("Vui lòng nhập token ID."),

            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["user_id"];
        $intUserTokenId = $arrParams["user_token_id"];
        $strRefCode = $arrParams["ref_code"];
        $intAmount = (double) $arrParams["amount"];
        $strCurrency = strtolower($arrParams["currency"] ?? "vnd");
        $strPaymentSuccessUrl = $arrParams["payment_success_url"] ?? "";
        $strPaymentCancelUrl = $arrParams["payment_cancel_url"] ?? "";
        $strBankAccountName = $arrParams["bank_account_name"] ?? "";
        $strBankAccountNumber = $arrParams["bank_account_number"] ?? "";
        $userIdQrcodeId = $arrParams["user_id_qrcode_id"] ?? "";
        $userIdQrcodeName = $arrParams["user_id_qrcode_name"] ?? "";
        $userIdQrcodeCode = $arrParams["user_id_qrcode_code"] ?? "";
        $intGatewayId = $arrParams["gateway_id"] ?? "0";
        $strBankShortName = $arrParams["bank_short_name"] ?? "";
        $strBankShortCode = $arrParams["bank_short_code"] ?? "";


        /**
         * Kiểm tra tiền tệ
         */

        if (empty(self::$arrCurrency[$strCurrency])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Loại tiền tệ không hợp lệ.")]
            ])->result();
        }

        if ($strCurrency == "vnd") {
            // if ($intAmount % 10000) {
            //     return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            //         [__("Số tiền nạp phải là bộ số của 10000đ.")]
            //     ])->result();
            // }
        }

        if ($strCurrency == "usd") {
            if ($intAmount > 1) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Số tiền nạp phải lớn hơn 1usd.")]
                ])->result();
            }
        }

        $intExchangeRate = 1;
        if ($strCurrency == "usd") {
            $intExchangeRate = 25600;
        }

        /**
         * Kiểm tra user tồn tại
         */

        $objUser = User::where('id', $intUserId)->where('actived', 1)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại hoặc chưa được kích hoạt.")]
            ])->result();
        }

        $objUserToken = UserToken::where('id', $intUserTokenId)->first();
        if (!$objUserToken) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy token.")]
            ])->result();
        }

        if (strtotime($objUserToken->expired_at) < time()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Token đã hết hạn.")]
            ])->result();
        }



        /**
         * Lấy phí IN 
         */
        $intUserFee = 0;
        $arrUserFee = [];
        $objUserFee = UserFee::where('user_id', $intUserId)->whereIn('type_id', [1, 3])->first();
        if ($objUserFee) {
            $arrUserFee = [
                "type_id" => $objUserFee->type_id,
                "fee" => $objUserFee->fee,
                "min_fee" => $objUserFee->min_fee,
            ];
            /**
             * Liểm tra loại để tính phí
             * 1: phí cố định in
             * 2: phí cố định out
             * 3: phí % in
             * 4: phí % out
             */
            $intUserBusiessFeeType = $objUserFee->type_id;
            if ($intUserBusiessFeeType == 1) {
                //1: phí cố định in
                $intUserFee += $objUserFee->fee;

            } elseif ($intUserBusiessFeeType == 3) {
                // 3: phí % in
                $intUserFee += round($intAmount * $objUserFee->fee / 100, 3);
            }

            if ($intUserFee < $objUserFee->min_fee) {
                $intUserFee = $objUserFee->min_fee;
            }
        }

        $intGatewayId = $intGatewayId != 0 ? $intGatewayId : $objUser->gateway_id;


        $intGatewayFee = 0;
        $arrGatewayFee = [];
        $objGatewayFee = GatewayFee::where('gateway_id', $intGatewayId)->whereIn('type_id', [1, 3])->first();
        if ($objGatewayFee) {
            $arrGatewayFee = [
                "type_id" => $objGatewayFee->type_id,
                "fee" => $objGatewayFee->fee,
                "min_fee" => $objGatewayFee->min_fee,
            ];
            if ($objGatewayFee->type_id == 1) {
                //1: phí cố định in
                $intGatewayFee += $objGatewayFee->fee;

            } elseif ($objGatewayFee->type_id == 3) {
                // 3: phí % in
                $intGatewayFee += round($intAmount * $objGatewayFee->fee / 100, 3);
            }

            if ($intGatewayFee < $objGatewayFee->min_fee) {
                $intGatewayFee = $objGatewayFee->min_fee;
            }
        }

        $intUserReferalFee = 0;
        $arrUserReferalFee = [];
        $objUserReferalFee = UserReferalFee::where('user_id', $intUserId)->whereIn('type_id', [1, 3])->first();
        if ($objUserReferalFee) {
            $arrUserReferalFee = [
                "type_id" => $objUserReferalFee->type_id,
                "fee" => $objUserReferalFee->fee,
                "min_fee" => $objUserReferalFee->min_fee,
            ];
            if ($objUserReferalFee->type_id == 1) {
                //1: phí cố định in
                $intUserReferalFee += $objUserReferalFee->fee;

            } elseif ($objUserReferalFee->type_id == 3) {
                // 3: phí % in
                $intUserReferalFee += round($intAmount * $objUserReferalFee->fee / 100, 3);
            }

            if ($intUserReferalFee < $objUserReferalFee->min_fee) {
                $intUserReferalFee = $objUserReferalFee->min_fee;
            }
        }

        /**
         * Kiểm tra mã tham chiếu 
         */
        $objTransactionCheckExist = Transaction::where('user_id', $intUserId)->where('ref_code', $strRefCode)->first();
        if ($objTransactionCheckExist) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mã đơn ref_code đã tồn tại, vui lòng sử dụng mã khác.")]
            ])->result();
        }

        $intAmountAfterFee = $intAmount - $intUserFee;

        $strCode = "FPA" . strtoupper(\Str::random(2)) . rand(1000000000, 9999999999);
        $strCodeHash = md5($strCode . time() . rand(11111, 99999));
        $dateExpiredAt = date('Y-m-d H:i:s', time() + 3600);
        $arrInsert = [
            "gateway_id" => $intGatewayId,
            "user_id_qrcode_id" => $userIdQrcodeId,
            "user_id_qrcode_name" => $userIdQrcodeName,
            "user_id_qrcode_code" => $userIdQrcodeCode,
            "bank_account_name" => $strBankAccountName,
            "bank_account_number" => $strBankAccountNumber,
            "bank_short_name" => $strBankShortName,
            "bank_short_code" => $strBankShortCode,
            "code" => $strCode,
            "user_token_id" => $intUserTokenId,
            "code_hashed" => $strCodeHash,
            "ref_code" => $strRefCode,
            "user_id" => $intUserId,
            "user_email" => $objUser->email,
            "bank_id" => 0,
            "status_id" => 1,
            "type_id" => 1,
            "currency" => $strCurrency,
            "exchange_rate" => $intExchangeRate,
            "amount" => $intAmount,
            "fee" => $intUserFee,
            "gateway_fee" => $intGatewayFee,
            "referal_fee" => $intUserReferalFee,
            "profit" => $intUserFee - ($intGatewayFee + $intUserReferalFee),
            "amount_after_fee" => $intAmountAfterFee,
            "user_fee_json" => json_encode($arrUserFee),
            "gateway_fee_json" => json_encode($arrGatewayFee),
            "referal_fee_json" => json_encode($arrUserReferalFee),
            "expired_at" => $dateExpiredAt, // hết hạn sau 1 tiếng
            "payment_success_url" => $strPaymentSuccessUrl,
            "payment_cancel_url" => $strPaymentCancelUrl,
            "payment_ipn_url" => $objUserToken->webhook_url,
            "callback_status_id" => 1,
            "callback_total_retry" => 0,
            "for_control_at" => date('Y-m-d H:i:s', time() + (60 * 60 * 24 * ($objUser->number_day_for_control))),
            "for_control_yyyymmdd" => date('Ymd', time() + (60 * 60 * 24 * $objUser->number_day_for_control)),
            "for_control_yyyymmddhh" => date('Ymdh', time() + (60 * 60 * 24 * $objUser->number_day_for_control)),
            'for_control_type' => $objUser->for_control_type
        ];


        $objTransaction = $this->createTransactionUnique($arrInsert);
        if (!$objTransaction) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Khởi tạo giao dịch thất bại.")]
            ])->result();
        }


        dispatch(new \App\Jobs\ToolPushBackupTransactionJob([
            'transaction_id' => $objTransaction->id
        ]))->onQueue('notification');




        return $this->setStatusCode(0)->setMessage(__("Thành công"))->setData([
            "code" => $objTransaction->code,
            "code_hashed" => $objTransaction->code_hashed,
            "ref_code" => $objTransaction->ref_code,
            "amount" => $intAmount,
            "fee" => $intUserFee,
            "amount_after_fee" => $intAmountAfterFee,
            "created_at" => $objTransaction->created_at,
            "payment_url" => route('payment.transaction.payment-method', ["hash" => $objTransaction->code_hashed])
        ])->result();
    }

    public function chonsePaymentBank($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                'code_hashed' => "required",
                'bank_account_id' => "required",
            ],
            [

                "code_hashed.required" => __("Vui lòng nhập mã đơn của bạn."),
                "bank_account_id.required" => __("Vui lòng nhập bank_account_id id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strCodeHash = $arrParams["code_hashed"];
        $intBankAccountId = $arrParams["bank_account_id"];

        $objTransaction = Transaction::where('code_hashed', $strCodeHash)->first();
        if (!$objTransaction) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Giao dịch không tồn tại.")]
            ])->result();
        }

        if (!empty($objTransaction->bank_account_id)) {
            return $this->setStatusCode(808)->setMessage("")->setData([])->setErrors([
                [__("Giao dịch đã cấu hình bank.")]
            ])->result();
        }



        $intUserId = $objTransaction->user_id;

        if (strtotime($objTransaction->expired_at) < time()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Giao dịch đã hết hạn.")]
            ])->result();
        }

        /**
         * Kiểm tra UserBankAccount tránh submit cheating
         */

        $objUserBankAccount = UserBankAccount::where('user_id', $intUserId)->where('bank_account_id', $intBankAccountId)->first();
        if (!$objUserBankAccount) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Giao dịch không hợp lệ, mã bank không tồn tại.")]
            ])->result();
        }

        /**
         * Lấy bank Account
         */

        $objBankAccount = BankAccount::where('id', $intBankAccountId)->where("status_id", 2)->first();
        if (!$objBankAccount) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Giao dịch không hợp lệ, bank không tồn tại.")]
            ])->result();
        }


        $objBank = Bank::where('id', $objBankAccount->bank_id)->where("status_id", 2)->first();
        if (!$objBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ngân hàng không tồn tại hoặc đang bảo trì.")]
            ])->result();
        }

        $objTransaction->bank_account_name = $objBankAccount->bank_account_name;
        $objTransaction->bank_account_number = $objBankAccount->bank_account_number;
        $objTransaction->bank_short_name = $objBank->short_name;
        $objTransaction->bank_short_code = $objBank->short_code;
        $objTransaction->bank_napas_code = $objBank->napas_code;
        $objTransaction->bank_account_id = $intBankAccountId;

        if (!$objTransaction->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xác nhận giao dịch thất bại, vui lòng kiểm tra lại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thành công"))->setData([
            "transaction" => $objTransaction,
            "bank" => $objBank,
            "bank_account" => $objBankAccount,
            "user_bank_account" => $objUserBankAccount,
        ])->result();
    }

    private function createTransactionUnique($arrInsert = [])
    {
        try {
            return Transaction::create($arrInsert);
        } catch (\Exception $ex) {
            \Log::error("createTransactionUnique:" . $ex->getMessage());
            if (strpos($ex->getMessage(), "Duplicate entry") != false) {
                return $this->createTransactionUnique($arrInsert);
            }
            return false;
        }
    }


    public function formatContentToTransactionCode($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                'content' => "required",
            ],
            [

                "content.required" => __("Vui lòng nhập nội dung."),

            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intBypassCheckExistCode = $arrParams["bypass_check_exist_code"] ?? false;
        $strContent = ($arrParams["content"]);
        $strContentNone = General::getContentNone(mb_strtoupper($arrParams["content"]));
        $strContentNone = str_replace(["VND", "  "], [" VND", " "], $strContentNone);

        /**
         * Định dạng lấy mã giao dịch
         */
        preg_match('/FPA([A-Z0-9])\w+/', $strContentNone, $arrResult);
        if ($intBypassCheckExistCode == false) {
            if (empty($arrResult[0])) {
                return $this->setStatusCode(404)->setMessage("")->setData(["content" => $strContent])->setErrors([
                    [__("Không tìm thấy code.")]
                ])->result();
            }
        }
        $strCode = $arrResult[0] ?? "";



        /**
         * Lấy số tiền chuyển
         */
        preg_match('/\+(.*?)VND/', $strContentNone, $arrResult);
        $intAmount = 0;
        if (!empty($arrResult[1])) {
            $intAmount = str_replace(",", "", $arrResult[1]);
        }

        /**
         * Sử dụng message của techcombank
         */
        if (empty($intAmount)) {
            preg_match('/GD:\+(.*?)[\n| ]/', $strContentNone, $arrResult);
            if (!empty($arrResult[1])) {
                $intAmount = str_replace(",", "", $arrResult[1]);
            }
        }

        /**
         * Áp dụng VIB
         */
        if (empty($intAmount)) {
            preg_match('/PS:\+([0-9]*?)[\n| ]/', str_replace([",", "\n"], ["", " "], $strContentNone), $arrResult);
            $intAmount = str_replace(",", "", $arrResult[1] ?? 0);

        }

        $receivedDate = "";
        preg_match('/LUC (.*?)\./', $strContentNone, $arrResult);
        $receivedDate = "";
        if (!empty($arrResult[1])) {
            $receivedDate = General::formatInputDay($arrResult[1]);
        }

        if (empty($receivedDate)) {
            $pattern = '/(\d{2}:\d{2});(\d{2}\/\d{2}\/\d{4})/';

            if (preg_match($pattern, $strContentNone, $matches)) {
                $timePart = $matches[1]; // 13:50
                $datePart = $matches[2]; // 03/04/2026

                // 2. Kết hợp và Format lại
                $fullDate = $datePart . ' ' . $timePart;
                $dateObj = \DateTime::createFromFormat('d/m/Y H:i', $fullDate);

                if ($dateObj) {
                    $receivedDate = $dateObj->format('Y-m-d H:i:s');
                }
            }

        }
        //       preg_match('/LUC (.*?)\./', $strContentNone, $arrResult);
        // $receivedDate = "";
        // if (!empty($arrResult[1])) {
        //     $receivedDate = General::formatInputDay($arrResult[1]);
        // }


        /**
         * Lấy tổng balance
         */
        preg_match('/[SO DU|SD|SODU] ([0-9]*?) VND\./', str_replace(",", "", $strContentNone), $arrResult);
        $intTotalBalance = str_replace(",", "", $arrResult[1] ?? 0);

        /**
         * Áp dụng techcombank
         */
        if (empty($intTotalBalance)) {
            preg_match('/SO DU:([0-9]*?)[\n| ]/', str_replace(",", "", $strContentNone), $arrResult);
            $intTotalBalance = str_replace(",", "", $arrResult[1] ?? 0);
        }
        /**
         * Ap dung VIB
         */



        return $this->setStatusCode(0)->setMessage(__("Thành công"))->setData([
            "code" => $strCode,
            "amount" => $intAmount,
            "total_balance" => $intTotalBalance,
            "content" => str_replace("\n", " ", $strContentNone),
            "received_date" => $receivedDate,
        ])->result();

    }

    public function updateResultTransaction($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                'code' => "required",
                'amount' => "required",
            ],
            [
                "code.required" => __("Vui lòng nhập mã giao dịch."),
                "amount.required" => __("Vui lòng nhập số tiền."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strCode = $arrParams["code"];
        $intAmount = (int) $arrParams["amount"];
        $intTotalBalance = (int) $arrParams["total_balance"] ?? 0;
        $strContent = $arrParams["content"] ?? "";
        $strReceivedAt = $arrParams["received_date"] ?? "";
        $strBankShortName = $arrParams["bank_short_name"] ?? "";
        $strAccountName = $arrParams["bank_account_name"] ?? "";
        $strBankAccountNumber = $arrParams["bank_account_number"] ?? "";

        \DB::beginTransaction();
        $objTransaction = Transaction::where('code', $strCode)->lockForUpdate()->first();
        if (!$objTransaction) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mã giao dịch không tồn tại trên hệ thống.")]
            ])->result();
        }

        if ($objTransaction->status_id == 2) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Giao dịch đã thành công không cần cập nhật lại.")]
            ])->result();
        }

        $intStatusId = 5;
        if ($objTransaction->amount == $intAmount) {
            $intStatusId = 6;
            if ($objTransaction->for_control_type == 3) {
                $intStatusId = 2;
            }
        }

        /**
         * @var 
         * Nếu nằm trong forward
         */
        $objGatewayForward = GatewayForward::where("gateway_source_code", "gpay")->where("bank_account_number", $strBankAccountNumber)->first();
        if ($objGatewayForward) {
            $intStatusId = 7;
        }


        $objTransaction->status_id = $intStatusId;
        $objTransaction->received_amount = $objTransaction->amount_after_fee;
        $objTransaction->bank_total_balance = $intTotalBalance;
        $objTransaction->content = $strContent;
        $objTransaction->received_at = $strReceivedAt;
        if (!empty($strBankShortName)) {
            $objTransaction->bank_short_name = $strBankShortName;
        }

        if (!empty($strAccountName)) {
            $objTransaction->bank_account_name = $strAccountName;
        }

        if (!empty($strBankAccountNumber)) {
            $objTransaction->bank_account_number = $strBankAccountNumber;
        }


        if (!$objTransaction->save()) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Có lỗi xảy ra khi ghi nhận dữ liệu.")]
            ])->result();
        }

        /**
         * Ghi nhận số tiền cho user
         */
        if ($objTransaction->status_id == 6) {
            dispatch(new TransactionCallbackResultJob([
                'id' => $objTransaction->id,
            ]))->onQueue('callback');
        } elseif ($objTransaction->for_control_type == 3) {
            /**
             * Đối soát net +1
             */
            $userTransaction = new UserTransactionService();
            $resultRecharge = $userTransaction->recharge([
                "user_id" => $objTransaction->user_id,
                "amount" => $objTransaction->amount_after_fee,
                "note" => "Cộng tiền giao dịch $objTransaction->code số tiền " . number_format($objTransaction->amount_after_fee),
                "trans_code" => md5($objTransaction->code)
            ]);

            if ($resultRecharge["error_code"] != 0) {
                \DB::rollBack();
                return $resultRecharge;
            }

            /**
             * Gọi job để callback
             */
            dispatch(new TransactionCallbackResultJob([
                'id' => $objTransaction->id,
            ]))->onQueue('callback');
        }

        \DB::commit();
        return $this->setStatusCode(0)->setMessage(__("Thành công"))->setData([
            'transaction' => $objTransaction
        ])->result();
    }

    public function callbackResultTransaction($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                'transaction_id' => "required",
            ],
            [
                "transaction_id.required" => __("Vui lòng nhập transaction_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intTransactionId = $arrParams["transaction_id"];
        $objTransaction = Transaction::where('id', $intTransactionId)->first();
        if (!$objTransaction) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mã giao dịch không tồn tại trên hệ thống.")]
            ])->result();
        }

        if ($objTransaction->callback_total_retry > 3) {
            TransactionCallback::create([
                "transaction_id" => $objTransaction->id,
                "user_id" => $objTransaction->user_id,
                "message" => "Giới hạn callback"
            ]);
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Callback quá nhiều lần vẫn thất bại.")]
            ])->result();
        }
        $objTransaction->callback_total_retry += 1;
        /**
         * Lấy token
         */
        $intUserId = $objTransaction->user_id;
        $strWebhookUrl = "";
        $strSystemPrivateKey = "";
        $strToken = "";
        $objToken = UserToken::where('user_id', $objTransaction->user_id)->first();
        if ($objToken) {
            $strWebhookUrl = $objToken->webhook_url;
            $strSystemPrivateKey = $objToken->system_private_key;
            $strToken = $objToken->token;
            /**
             * RIBATO không chơi rule này
             */
            if (env('APP_NAME') != "RIBATO") {
                if ($objTransaction->user_id == 900 || $objTransaction->user_id == 904 || $objTransaction->user_id == 905) {
                    $strToken = "aebac58282286cfea3a0ea49f45f58e5";
                }
            }
        }

        if ($strWebhookUrl == "") {
            $objTransaction->callback_status_id = 3;
            $objTransaction->save();
            TransactionCallback::create([
                "transaction_id" => $objTransaction->id,
                "user_id" => $objTransaction->user_id,
                "message" => "Không tìm thấy IPN callback"
            ]);
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không có url để callback.")]
            ])->result();
        }

        $_curl = new Curl();
        $_curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
        $_curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
        $_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $_curl->setOpt(CURLOPT_ENCODING, "");
        $_curl->setHeader('Content-Type', 'application/json');
        $_curl->setHeader('User-agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36');
        $_curl->setTimeout(90);
        $_curl->setConnectTimeout(90);

        $result = [
            'user_id' => $intUserId,
            'amount' => $objTransaction->amount,
            'received_amount' => $objTransaction->received_amount,
            'received_date' => $objTransaction->received_at,
            'fee' => $objTransaction->fee,
            'content' => str_replace("\n", " ", $objTransaction->content),
            'ref_code' => $objTransaction->ref_code,
            'bank_short_name' => $objTransaction->bank_short_name,
            'bank_short_code' => $objTransaction->bank_short_code,
            'bank_account_name' => $objTransaction->bank_account_name,
            'bank_account_number' => $objTransaction->bank_account_number
        ];

        if (!empty($objTransaction->bank_total_balance)) {
            $result["bank_total_balance"] = $objTransaction->bank_total_balance;
        }

        $strSign = "";
        if (!empty($strSystemPrivateKey)) {
            try {
                $strSign = General::getSign($result, $strToken, $strSystemPrivateKey);
            } catch (\Exception $ex) {

            }
        }

        $result["sign"] = $strSign;
        $arrPost = [
            'error_code' => 0,
            'msg' => 'Thành công',
            'result' => $result
        ];

        if (env('BACKEND_VERSION') == 'v2' && env('POSTBACK_VERSION') == 'v2') {
            if (isset($result["sign"])) {
                unset($result["sign"]);
            }

            $result = [
                'amount' => $objTransaction->amount,
                'received_date' => $objTransaction->received_at,
                'content' => str_replace("\n", " ", $objTransaction->content),
                'reference' => $objTransaction->ref_code,
            ];
            $result["checksum"] = md5(General::httpBuildQuery($result) . $strToken);
            $arrPost = [
                'success' => true,
                'message' => 'Thành công',
                'data' => $result
            ];
            $_curl->setProxy('104.250.122.113', 22128, 'admin', 'admin@admin123123');
        }



        $_curl->post($strWebhookUrl, json_encode($arrPost));
        if ($_curl->error) {
            $objTransaction->callback_status_id = 3;
            $objTransaction->save();
            TransactionCallback::create([
                "transaction_id" => $objTransaction->id,
                "user_id" => $objTransaction->user_id,
                "message" => ($_curl->errorCode . ': ' . $_curl->errorMessage),
                "param_request" => json_encode(["url" => $strWebhookUrl, "request" => $arrPost]),
                "param_response" => strip_tags($_curl->rawResponse)
            ]);

            return $this->setStatusCode(809)->setMessage("")->setData([])->setErrors([
                [__($_curl->errorCode . ': ' . $_curl->errorMessage)]
            ])->result();
        }

        $objTransaction->callback_status_id = 2;
        $objTransaction->save();
        TransactionCallback::create([
            "transaction_id" => $objTransaction->id,
            "user_id" => $objTransaction->user_id,
            "message" => "Thành công",
            "param_request" => json_encode($arrPost),
            "param_response" => strip_tags($_curl->rawResponse)
        ]);

        return $this->setStatusCode(0)->setMessage(__("Thành công"))->setData([
            'transaction' => $objTransaction,
            'response' => $_curl->rawResponse,
        ])->result();
    }

    public function forControl($date = "")
    {
        $objTransactions = Transaction::select(\DB::raw('SUM(received_amount) as total_amount,user_id,for_control_at,for_control_yyyymmdd'))->where('status_id', 6)->where('for_control_yyyymmdd', $date)->where('for_control_type', 1)->groupBy('user_id')->get();
        $userTransaction = new UserTransactionService();
        foreach ($objTransactions as $objTransaction) {
            \DB::beginTransaction();
            /**
             * Cập nhật tất cả transaction lại trạng thái =2
             */
            $objTransactionUpdate = Transaction::where('status_id', 6)->where('user_id', $objTransaction->user_id)->where('for_control_yyyymmdd', $date)->where('for_control_type', 1)->update(["status_id" => 2]);
            if (!$objTransactionUpdate) {
                dump("update false");
                \DB::rollBack();
                continue;
            }

            $strDate = (date("Y-m-d", strtotime($objTransaction->for_control_at)));
            $strTransCode = $strDate . "|" . $objTransaction->user_id . "|" . $objTransaction->total_amount;
            $resultRecharge = $userTransaction->recharge([
                "user_id" => $objTransaction->user_id,
                "amount" => $objTransaction->total_amount,
                "note" => "Chốt giao dịch ngày " . $strDate . " số tiền " . number_format($objTransaction->total_amount) . "đ",
                "trans_code" => md5($strTransCode)
            ]);
            if ($resultRecharge["error_code"] != 0) {
                dump($resultRecharge);
                \DB::rollBack();
                continue;
            }
            \DB::commit();
        }
        return $this->setStatusCode(0)->setMessage(__("Thành công"))->setData([
        ])->result();
    }

    public function forControlByGpay($date = "")
    {
        $objTransactions = Transaction::select(\DB::raw('SUM(received_amount) as total_amount,user_id,for_control_at,for_control_yyyymmdd'))->where('status_id', 6)->where('for_control_yyyymmdd', $date)->where('for_control_type', 2)->groupBy('user_id')->get();
        $userTransaction = new UserTransactionService();
        foreach ($objTransactions as $objTransaction) {
            \DB::beginTransaction();
            /**
             * Cập nhật tất cả transaction lại trạng thái =2
             */
            $objTransactionUpdate = Transaction::where('status_id', 6)->where('user_id', $objTransaction->user_id)->where('for_control_yyyymmdd', $date)->where('for_control_type', 2)->update(["status_id" => 2]);
            if (!$objTransactionUpdate) {
                dump("update false");
                \DB::rollBack();
                continue;
            }

            $strDate = (date("Y-m-d", strtotime($objTransaction->for_control_at)));
            $strTransCode = $strDate . "|" . $objTransaction->user_id . "|" . $objTransaction->total_amount;
            $resultRecharge = $userTransaction->recharge([
                "user_id" => $objTransaction->user_id,
                "amount" => $objTransaction->total_amount,
                "note" => "Chốt giao dịch ngày " . $strDate . " số tiền " . number_format($objTransaction->total_amount) . "đ",
                "trans_code" => md5($strTransCode)
            ]);
            if ($resultRecharge["error_code"] != 0) {
                dump($resultRecharge);
                \DB::rollBack();
                continue;
            }
            \DB::commit();
        }
        return $this->setStatusCode(0)->setMessage(__("Thành công"))->setData([
            'date' => $date
        ])->result();
    }

    public function forControlByPaymentHot($date = "")
    {
        $objTransactions = Transaction::select(\DB::raw('SUM(received_amount) as total_amount,user_id,for_control_at,for_control_yyyymmdd'))->where('status_id', 6)->where('for_control_type', 3)->groupBy('user_id')->get();
        $userTransaction = new UserTransactionService();
        foreach ($objTransactions as $objTransaction) {
            \DB::beginTransaction();
            /**
             * Cập nhật tất cả transaction lại trạng thái =2
             */
            $objTransactionUpdate = Transaction::where('status_id', 6)->where('user_id', $objTransaction->user_id)->where('for_control_type', 3)->update(["status_id" => 2]);
            if (!$objTransactionUpdate) {
                dump("update false");
                \DB::rollBack();
                continue;
            }

            $strDate = (date("Y-m-d", strtotime($objTransaction->for_control_at)));
            $strTransCode = $strDate . "|" . $objTransaction->user_id . "|" . $objTransaction->total_amount;
            $resultRecharge = $userTransaction->recharge([
                "user_id" => $objTransaction->user_id,
                "amount" => $objTransaction->total_amount,
                "note" => "Chốt giao dịch ngày " . $strDate . " số tiền " . number_format($objTransaction->total_amount) . "đ",
                "trans_code" => md5($strTransCode)
            ]);
            if ($resultRecharge["error_code"] != 0) {
                dump($resultRecharge);
                \DB::rollBack();
                continue;
            }
            \DB::commit();
        }
        return $this->setStatusCode(0)->setMessage(__("Thành công"))->setData([
            'date' => $date
        ])->result();
    }


    public function forControlByGpayRibato($date = "")
    {
        $objTransactions = Transaction::select(\DB::raw('SUM(received_amount) as total_amount,user_id,for_control_at,for_control_yyyymmdd'))->where('status_id', 6)->where('for_control_yyyymmdd', $date)->where('for_control_type', 4)->groupBy('user_id')->get();
        $userTransaction = new UserTransactionService();
        foreach ($objTransactions as $objTransaction) {
            \DB::beginTransaction();
            /**
             * Cập nhật tất cả transaction lại trạng thái =2
             */
            $objTransactionUpdate = Transaction::where('status_id', 6)->where('user_id', $objTransaction->user_id)->where('for_control_yyyymmdd', $date)->where('for_control_type', 4)->update(["status_id" => 2]);
            if (!$objTransactionUpdate) {
                dump("update false");
                \DB::rollBack();
                continue;
            }

            $strDate = (date("Y-m-d", strtotime($objTransaction->for_control_at)));
            $strTransCode = $strDate . "|" . $objTransaction->user_id . "|" . $objTransaction->total_amount;
            $resultRecharge = $userTransaction->recharge([
                "user_id" => $objTransaction->user_id,
                "amount" => $objTransaction->total_amount,
                "note" => "Chốt giao dịch ngày " . $strDate . " số tiền " . number_format($objTransaction->total_amount) . "đ",
                "trans_code" => md5($strTransCode)
            ]);
            if ($resultRecharge["error_code"] != 0) {
                dump($resultRecharge);
                \DB::rollBack();
                continue;
            }
            \DB::commit();
        }
        return $this->setStatusCode(0)->setMessage(__("Thành công"))->setData([
            'date' => $date
        ])->result();
    }


    public function createQrPayment($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
                "amount" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập user_id"),
                "amount.required" => __("Vui lòng nhập số tiền"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserIdQrcodeId = $arrParams["user_id_qrcode_id"] ?? 0;
        $strUserIdQrcodeName = "";
        $strUserIdQrcodeCode = "";
        $intUserId = $arrParams["user_id"];
        $intAmount = $arrParams["amount"] ?? 0;
        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }


        if ($intUserIdQrcodeId != 0) {
            $objUserIdQrcode = UserIdQrcode::where('id', $intUserIdQrcodeId)->first();
            if (!$objUserIdQrcode) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Mã ID QR không tồn tại.")]
                ])->result();
            }

            if ($objUserIdQrcode->user_id != $objUser->id) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Bạn không có quyền quản lý mã QR này.")]
                ])->result();
            }

            $strUserIdQrcodeName = $objUserIdQrcode->name;
            $strUserIdQrcodeCode = $objUserIdQrcode->code;
        }

        $objUserBank = UserBankAccount::select()
            ->join('bank_accounts', 'bank_accounts.id', 'user_bank_accounts.bank_account_id')
            ->join('banks', 'banks.id', 'bank_accounts.bank_id')->
            where('user_bank_accounts.user_id', $intUserId)->where('user_bank_accounts.status_id', 2)->first();
        if (!$objUserBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Bạn chưa được cấu hình ngân hàng nào.")]
            ])->result();
        }

        $objUserToken = UserToken::where('user_id', $intUserId)->first();
        if (!$objUserToken) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản chưa được cấu hình token.")]
            ])->result();
        }

        $resultCreatePayment = $this->createPayment([
            'ref_code' => date('YmdHis') . strtoupper(\Str::random(8)),
            'user_id' => $objUserBank->user_id,
            'amount' => $intAmount,
            'user_token_id' => $objUserToken->id,
            'user_id_qrcode_id' => $intUserIdQrcodeId,
            'user_id_qrcode_name' => $strUserIdQrcodeName,
            'user_id_qrcode_code' => $strUserIdQrcodeCode,
        ]);

        if ($resultCreatePayment["error_code"] != 0) {
            return $resultCreatePayment;
        }

        $strInfoRemark = $resultCreatePayment["data"]["code"];
        $generator = (new Generator())->create()
            ->bankId($objUserBank->short_code)
            ->accountNo($objUserBank->bank_account_number)// Account number
            ->amount($intAmount)// Money
            ->info($strInfoRemark) // Ref
            ->generate();

        $arrGenerator = json_decode($generator, true);
        if ($arrGenerator["code"] != 200) {
            return $this->setStatusCode(404)->setMessage("")->setData($arrGenerator)->setErrors([
                [__("Có lỗi khi tạo mã QR.")]
            ])->result();
        }


        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $arrGenerator["data"] ?? "",
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $arrParams["size"] ?? 200,
            margin: 3,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoResizeToWidth: 50,
            logoPunchoutBackground: true,
            labelText: $arrParams["label"] ?? "",
            labelFont: new OpenSans(20),
            labelAlignment: LabelAlignment::Center
        );
        $result = $builder->build();
        $arrPayment = [
            "bank" => [
                "bank_name" => $objUserBank->name,
                "short_name" => $objUserBank->short_name,
                "bank_account_name" => $objUserBank->bank_account_name,
                "bank_account_number" => $objUserBank->bank_account_number
            ],
            "remark" => $strInfoRemark,
            'payment_qrcode_base64' => base64_encode($result->getString()),
            'payment_qrcode_plantext' => $arrGenerator["data"] ?? "",
        ];
        return $this->setStatusCode(0)->setMessage(__("Lấy thông tin thành công."))->setData($arrPayment)->result();
    }


    public function exportExcel($arrParams = [])
    {
        $objTransactions = Transaction::select();
        $objTransactions = $this->getListBuilder($objTransactions, $arrParams, $this->arrFillable)->orderBy('id', 'ASC')->get();
        if (!$objTransactions) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([[__("Không tìm thấy sữ liệu để xuất.")]])->result();
        }
        $fileName = 'exports/export_transaction_' . time() . '.xlsx';
        $resultExport = \Excel::store(new TransactionExport(['objTransactions' => $objTransactions, 'status' => self::$arrStatusId,]), $fileName, 'export-excel', null);
        if (!$resultExport) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Xuất file thất bại.')]])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xuất giao dịch thành công."))->setData(["url" => url("static/" . $fileName)])->result();
    }


    public function exportExcelFullAccess($arrParams = [])
    {

        ini_set('memory_limit', '-1');
        $objTransactions = Transaction::select();
        $objTransactions = $this->getListBuilder($objTransactions, $arrParams, $this->arrFillable)->orderBy('id', 'ASC')->get();
        if (!$objTransactions) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([[__("Không tìm thấy sữ liệu để xuất.")]])->result();
        }
        $fileName = 'exports/export_transaction_' . time() . '.xlsx';
        $objGateWay = Gateway::get();
        $arrGateWay = [];
        foreach ($objGateWay as $item) {
            $arrGateWay[$item->id] = [
                "name" => $item->name
            ];
        }

        $resultExport = \Excel::store(new TransactionFullaccessExport(['objTransactions' => $objTransactions, 'status' => self::$arrStatusId, 'gateway' => $arrGateWay]), $fileName, 'export-excel', \Maatwebsite\Excel\Excel::XLSX);
        if (!$resultExport) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Xuất file thất bại.')]])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xuất giao dịch thành công."))->setData(["url" => url("static/" . $fileName)])->result();
    }

    public function exportExcelAccountant($arrParams = [])
    {

        ini_set('memory_limit', '-1');
        $objTransactions = Transaction::select();
        $objTransactions = $this->getListBuilder($objTransactions, $arrParams, $this->arrFillable)->orderBy('id', 'ASC')->get();
        if (!$objTransactions) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([[__("Không tìm thấy sữ liệu để xuất.")]])->result();
        }
        $fileName = 'exports/export_transaction_' . time() . '.xlsx';
        $objGateWay = Gateway::get();
        $arrGateWay = [];
        foreach ($objGateWay as $item) {
            $arrGateWay[$item->id] = [
                "name" => $item->name
            ];
        }

        $resultExport = \Excel::store(new TransactionFullaccessExport(['objTransactions' => $objTransactions, 'status' => self::$arrStatusId, 'gateway' => $arrGateWay]), $fileName, 'export-excel', \Maatwebsite\Excel\Excel::XLSX);
        if (!$resultExport) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Xuất file thất bại.')]])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xuất giao dịch thành công."))->setData(["url" => url("static/" . $fileName)])->result();
    }


}