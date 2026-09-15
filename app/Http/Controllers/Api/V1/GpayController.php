<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\UserGpayConfig;
use App\Models\UserToken;
use App\Services\AppMessageService;
use App\Services\TransactionService;
use App\Utilities\General;
use App\Utilities\GpayV2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GpayController extends BaseController
{
    protected $appMessageService;
    protected $transactionService;

    public function __construct(AppMessageService $appMessageService, TransactionService $transactionService)
    {
        $this->appMessageService  = $appMessageService;
        $this->transactionService = $transactionService;
    }

    /**
     * IPN / Webhook Receiver cho GPAY (Cổng thanh toán All-in-one & Thu hộ Virtual Account)
     * Sử dụng thư viện GpayV2 để xác thực chữ ký và xử lý đơn hàng
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ipn(Request $request)
    {
        $arrParams = $request->all();
        $rawContent = $request->getContent();
        $arrParamContents = json_decode($rawContent, true) ?: $arrParams;

        Log::info("GPAY_V2_IPN_RECEIVED: " . json_encode($arrParams));

        // 1. Kiểm tra Token định danh Merchant / User
        $strToken = $arrParams["token"] ?? $request->header('token') ?? $request->query('token');
        if (empty($strToken)) {
            Log::info("GPAY_V2_IPN không tìm thấy token: " . json_encode($arrParams));
            return response()->json([
                'status'  => false,
                'message' => 'Token null'
            ], 400);
        }

        $objUserToken = UserToken::where('token', $strToken)
            ->orWhere('token_gateway', $strToken)
            ->first();

        if (!$objUserToken) {
            Log::info("GPAY_V2_IPN token không hợp lệ: {$strToken}");
            return response()->json([
                'status'  => false,
                'message' => 'Token không hợp lệ trên hệ thống'
            ], 403);
        }

        // 2. Kiểm tra mã giao dịch GPAY (gpay_trans_id hoặc bill_id)
        $strTradeNo = $arrParams["gpay_trans_id"] ?? ($arrParams["data"]["gpay_trans_id"] ?? ($arrParams["bill_id"] ?? ''));
        if (empty($strTradeNo)) {
            Log::info("GPAY_V2_IPN không tìm thấy gpay_trans_id: " . json_encode($arrParams));
            return response()->json([
                'status'  => false,
                'message' => 'gpay_trans_id is required'
            ], 400);
        }

        // 3. Kiểm tra Action (CHANGE_BALANCE cho Virtual Account)
        $strAction = $arrParams["action"] ?? ($arrParams["data"]["action"] ?? "CHANGE_BALANCE");
        if ($strAction !== "CHANGE_BALANCE" && empty($arrParams["bill_id"]) && empty($arrParams["data"]["status"])) {
            Log::info("GPAY_V2_IPN action không hợp lệ: {$strAction}");
            \App\Jobs\TelegramNotificationJob::dispatch([
                'message' => "GPAY_V2_IPN action không hợp lệ: " . json_encode($arrParams),
                'type'    => "custome",
                'chat_id' => '-4161734390',
            ])->onQueue('notification');

            return response()->json([
                'status'  => false,
                'message' => 'Action không hợp lệ'
            ], 400);
        }

        $intUserId            = $objUserToken->user_id;
        $strBody              = $arrParams["message"] ?? ($arrParams["data"]["message"] ?? "");
        $intAmount            = (int)($arrParams["amount"] ?? ($arrParams["data"]["amount"] ?? 0));
        $strBankAccountNumber = $arrParams["account_number"] ?? ($arrParams["data"]["account_number"] ?? "");
        $strReceivedDate      = $arrParams["created_date"] ?? ($arrParams["data"]["created_date"] ?? 0);

        // 4. Khởi tạo GpayV2 và Xác thực Chữ ký số RSA-SHA256
        $objUserGpayConfig = UserGpayConfig::where('user_id', $intUserId)->first();
        $strPublicKey      = $objUserGpayConfig ? General::beautyKey($objUserGpayConfig->gpay_public_key, "PUBLIC KEY") : config('services.gpay.public_key', env('GPAY_PUBLIC_KEY', ''));

        $gpayV2 = new GpayV2([
            'gpay_public_key' => $strPublicKey,
            'merchant_code'   => $objUserGpayConfig->merchant_id ?? '',
        ]);

        $verified = false;

        // Nếu là Webhook Thu hộ Virtual Account (VA Change Balance)
        if (isset($arrParams["action"]) && $arrParams["action"] === "CHANGE_BALANCE") {
            $vaResult = $gpayV2->handleVaWebhook($arrParams);
            $verified = $vaResult['valid'];
        } else {
            // Webhook Cổng thanh toán (JSON signature)
            $gwResult = $gpayV2->handleWebhook($request->headers->all(), $rawContent ?: $arrParams);
            $verified = $gwResult['valid'];
        }

        if (!$verified) {
            $this->appMessageService->add([
                'device'         => $arrParams["device"] ?? "",
                'sender'         => 'gpay',
                'receiver'       => "",
                'content'        => $strBody,
                'content_origin' => $strBody,
                'type_id'        => 3,
            ]);

            \App\Jobs\TelegramNotificationJob::dispatch([
                'message' => "GPAY_V2_IPN Chữ ký số Không hợp lệ: " . json_encode($arrParams),
                'type'    => "custome",
                'chat_id' => '-4161734390',
            ])->onQueue('notification');

            Log::warning("GPAY_V2_IPN Chữ ký không hợp lệ: " . json_encode($arrParams));
        }

        // 5. Chuẩn hóa thời gian nhận giao dịch
        if (!empty($strReceivedDate) && is_numeric($strReceivedDate)) {
            $strReceivedDate = date('Y-m-d H:i:s', strlen((string)$strReceivedDate) > 10 ? round($strReceivedDate / 1000) : $strReceivedDate);
        } else {
            $strReceivedDate = date('Y-m-d H:i:s');
        }

        // 6. Ghi nhận tin nhắn vào App Message Service
        $arrMsgParams = [
            'device'         => $arrParams["device"] ?? "",
            'sender'         => 'gpay',
            'receiver'       => "",
            'content'        => $strBody,
            'content_origin' => $strBody,
            'type_id'        => 3,
        ];

        $resultAdd = $this->appMessageService->add($arrMsgParams);
        if (isset($resultAdd["error_code"]) && $resultAdd["error_code"] != 0) {
            Log::error("GPAY_V2_IPN appMessageService->add Error: " . json_encode($resultAdd));
            return response()->json($resultAdd);
        }

        // 7. Tự động phát hiện mã giao dịch qua nội dung chuyển khoản
        $resultDetectCodeTransaction = $this->detectCodeTransaction([
            "amount"        => $intAmount,
            "content"       => $strBody,
            "received_date" => $strReceivedDate
        ]);

        if (isset($resultDetectCodeTransaction["error_code"]) && $resultDetectCodeTransaction["error_code"] == 0) {
            return response()->json($resultDetectCodeTransaction);
        }

        // 8. Nếu không tự detect được, tạo Payment mới và cập nhật Transaction
        $strBankAccountName = $arrParams["account_name"] ?? ($arrParams["sender_name"] ?? "");
        $intTotalBalance    = 0;

        $resultCreatePayment = $this->transactionService->createPayment([
            "user_id"             => $objUserToken->user_id,
            'ref_code'            => $strTradeNo,
            'user_token_id'       => $objUserToken->id,
            'amount'              => $intAmount,
            "bank_account_name"   => $strBankAccountName,
            "bank_account_number" => $strBankAccountNumber,
        ]);

        if (isset($resultCreatePayment["error_code"]) && $resultCreatePayment["error_code"] != 0) {
            Log::error("GPAY_V2_IPN createPayment Error: " . json_encode($resultCreatePayment));
            return response()->json($resultCreatePayment);
        }

        // 9. Cập nhật kết quả giao dịch
        $strCode = $resultCreatePayment["data"]["code"] ?? "";
        $resultUpdateTransaction = $this->transactionService->updateResultTransaction([
            "bank_account_name"   => $strBankAccountName,
            "bank_account_number" => $strBankAccountNumber,
            "received_date"       => $strReceivedDate,
            "content"             => $strBody,
            "code"                => $strCode,
            "amount"              => $intAmount,
            "total_balance"       => $intTotalBalance
        ]);

        if (isset($resultUpdateTransaction["error_code"]) && $resultUpdateTransaction["error_code"] != 0) {
            Log::error("GPAY_V2_IPN updateResultTransaction Error: " . json_encode($resultUpdateTransaction));
        }

        // 10. Gửi thông báo Telegram Bot
        $strMsgAdmin = "GPAY (V2)\nThời gian : {$strReceivedDate}\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . " ₫\nSố tài khoản : {$strBankAccountNumber}\nMã GD : {$strTradeNo}\nNội dung : {$strBody}";
        \App\Jobs\TelegramNotificationJob::dispatch([
            'message' => $strMsgAdmin,
            'type'    => "notification",
            'chat_id' => '',
        ])->onQueue('notification');

        $strMsgUser = "THÔNG BÁO\nThời gian : {$strReceivedDate}\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . " ₫\nNội dung : {$strBody}";
        \App\Jobs\TelegramNotificationJob::dispatch([
            'message' => $strMsgUser,
            'type'    => "notification",
            'chat_id' => '',
            'user_id' => $objUserToken->user_id
        ])->onQueue('notification');

        return response()->json($resultUpdateTransaction);
    }

    /**
     * Tự động phát hiện mã giao dịch trong nội dung
     *
     * @param array $arrParams
     * @return array
     */
    public function detectCodeTransaction(array $arrParams)
    {
        $strBody              = $arrParams["content"] ?? "";
        $intAmount            = $arrParams["amount"] ?? 0;
        $strReceivedDate      = $arrParams["received_date"] ?? date('Y-m-d H:i:s');
        $strAccountName       = $arrParams["bank_account_name"] ?? "";
        $strBankAccountNumber = $arrParams["bank_account_number"] ?? "";

        $resultFormatContent = $this->transactionService->formatContentToTransactionCode(["content" => $strBody]);
        if (isset($resultFormatContent["error_code"]) && $resultFormatContent["error_code"] != 0) {
            Log::info("detectCodeTransaction resultFormatContent: " . json_encode($resultFormatContent));
            return $resultFormatContent;
        }

        if (empty($intAmount)) {
            return $this->transactionService->setStatusCode(404)->setMessage("")->setData($arrParams)->setErrors([
                [__("Vui lòng nhập số tiền.")]
            ])->result();
        }

        $strCode         = $resultFormatContent["data"]["code"] ?? "";
        $intTotalBalance = $resultFormatContent["data"]["total_balance"] ?? 0;

        $resultUpdateTransaction = $this->transactionService->updateResultTransaction([
            "bank_account_name"   => $strAccountName,
            "bank_account_number" => $strBankAccountNumber,
            "received_date"       => $strReceivedDate,
            "content"             => $strBody,
            "code"                => $strCode,
            "amount"              => $intAmount,
            "total_balance"       => $intTotalBalance
        ]);

        if (isset($resultUpdateTransaction["error_code"]) && $resultUpdateTransaction["error_code"] != 0) {
            Log::info("detectCodeTransaction resultUpdateTransaction: " . json_encode($resultUpdateTransaction));
            return $resultUpdateTransaction;
        }

        return $resultUpdateTransaction;
    }
}
