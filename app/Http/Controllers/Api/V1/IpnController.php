<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\GatewayAccount;
use App\Models\GatewayForward;
use App\Models\User;
use App\Models\UserGpayConfig;
use App\Models\UserIdQrcode;
use App\Models\UserNeoxConfig;
use App\Models\UserToken;
use App\Models\UserVirtualAccount;
use App\Models\UserWithdraw;
use App\Models\UserYoobilConfig;
use App\Models\WithdrawPaymenthotLog;
use App\Services\AppMessageService;
use App\Services\TransactionService;
use App\Services\UserWithdrawService;
use App\Utilities\General;
use App\Utilities\Gpay;
use App\Utilities\Neox;
use App\Utilities\Yoobil;
use Illuminate\Http\Request;

class IpnController extends BaseController
{

        protected $appMessageService;
        protected $transactionService;
        protected $arrAllowSener = ["com.VCB", "Vietcombank", "Techcombank", "VIB"];
        public function __construct(AppMessageService $appMessageService, TransactionService $transactionService)
        {
                $this->transactionService = $transactionService;
                $this->appMessageService = $appMessageService;
        }

        public function yoobilCollection(Request $request)
        {
                $arrParams = request()->all();
                \Log::info("YOOBIL COLLECTION:" . json_encode($arrParams));

                if (empty($arrParams["token"])) {
                        return $this->transactionService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                                [__("Token Params NULL.")]
                        ])->result();
                }

                if (empty($arrParams["result"])) {
                        return $this->transactionService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                                [__("Không có result.")]
                        ])->result();
                }

                $strToken = $arrParams["token"];
                $objUserToken = UserToken::where('token_gateway', $strToken)->first();
                if (!$objUserToken) {
                        return $this->transactionService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                                [__("Token không tồn tại trên hệ thống.")]
                        ])->result();
                }
                /**
                 * {"code":10000,"msg":"the request is succeed.","result":{"accountName":"NGUYEN HOANG NAM","accountNo":"9631242000000940610","amount":20000,"bankName":"BIDV","businessId":19,"currency":"VND","expireDate":null,"feeId":25,"merchantId":215,"orderNo":"ktDOu73129","purchaseAmount":20000,"purchaseCurrency":"VND","purchaseTime":"1701338882545","remark":"MBVCB.4737834761.094885.nguyen van ck.CT tu 9338499422 BUI THI KIEU toi 9631242000000940610 NGUYEN H","sign":"aJupZK66ti\/dHuxqZbTTo5QYVKA8AuihPLCqPRImcYnf8xLYNG1PYX+wseIC27Ymp\/hE71g08W2XGiIY\/N03ho03RIHVL4UDe+MBwNt0EZJGUg3Hi6Qo\/Z7gs0ArlEVvdxE78nlNj7NctlLrpARY0bkx0rjoY2TWOhlT5IfrGjlWuuDqH4JFzb8N4bvGH9G4qlhFmnKwUo6rja6DL17qdP8Fz5251ydcfeKVa3oVxQKPmDc9kXfhoaYLBcf8IzeNirhCDI9kgPM9QtsZwYYclPimpSBjVE0pYnft5+RAP5LcVzrCO0Agx9UTgdwzRF3dgxjPMb24iBgFb8MCIzu4Kg==","tradeNo":"0050001730166790975262720","userName":"NGUYEN HOANG NAM"},"q":"\/api\/test\/test"}  
                 */
                $strBody = $arrParams["result"]["remark"] ?? "";
                $intAmount = $arrParams["result"]["amount"] ?? 0;
                $strReceivedDate = $arrParams["result"]["purchaseTime"] ?? 0;
                $strTradeNo = $arrParams["result"]["tradeNo"] ?? "";
                $strBankAccountNumber = $arrParams["result"]["accountNo"] ?? "";
                $strBankAccountName = $arrParams["result"]["accountName"] ?? "";

                if (!empty($strReceivedDate)) {
                        $strReceivedDate = date('Y-m-d H:i:s', round($strReceivedDate / 1000));
                }

                $arrInserAppMessage = [
                        'device' => "",
                        'sender' => 'yoobil',
                        'receiver' => $strReceivedDate,
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 3,
                ];

                $ressultAdd = $this->appMessageService->add($arrInserAppMessage);
                if ($ressultAdd["error_code"] != 0) {
                        return $this->transactionService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                                [__("Tạo App message thất bại.")]
                        ])->result();
                }


                /**
                 * @var 
                 * Nếu có transaction sẽ không đẩy qua bên dịch vụ gốc nữa
                 */
                $resultDetectCodeTransaction = $this->detectCodeTransaction([
                        "amount" => $intAmount,
                        "content" => $strBody,
                        "received_date" => $strReceivedDate,
                        "bank_account_name" => $strBankAccountName,
                        "bank_account_number" => $strBankAccountNumber,
                ]);

                if ($resultDetectCodeTransaction["error_code"] == 0) {
                        return $resultDetectCodeTransaction;
                }


                /**
                 * Kiểmt tra account number được cấu hình trên  nào
                 */

                $objUserVirtualAccount = UserVirtualAccount::where('bank_account_number', $strBankAccountNumber)
                        ->first();
                if (!$objUserVirtualAccount) {
                        return $this->transactionService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                                [__("Tài khoản chưa có VA.")]
                        ])->result();
                }


                $objGatewayAccount = GatewayAccount::where('id', $objUserVirtualAccount->gateway_account_id)->where('gateway_id', 3)->first();
                if (!$objGatewayAccount) {
                        return $this->transactionService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                                [__("Không tìm thấy cổng.")]
                        ])->result();
                }

                $yoobil = new Yoobil();
                $strPublicKey = (General::beautyKey($objGatewayAccount->gateway_public_key, "PUBLIC KEY"));
                $verified = $yoobil->setSecretKey($objGatewayAccount->secret_key)->setPublicKey($strPublicKey)->verifySign($arrParams["result"] ?? []);

                if (empty($arrParams["bypas_verified"])) {
                        if (!$verified) {
                                \App\Jobs\TelegramNotificationJob::dispatch([
                                        'message' => "verified Không hợp lệ " . json_encode($arrParams["result"] ?? []),
                                        'type' => "custome",
                                        'chat_id' => '-4161734390',
                                ])->onQueue('notification');
                                \Log::info("verified Không hợp lệ " . json_encode($arrParams));
                        }
                }

                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $resultFormatContent = $this->transactionService->formatContentToTransactionCode(["content" => $strBody, 'bypass_check_exist_code' => true]);
                if ($resultFormatContent["error_code"] != 0) {
                        \Log::info("YOOBIL COLLECTION MESSAGE resultFormatContent" . json_encode($resultFormatContent));
                        return $this->transactionService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                                [__("Có lỗi xảy ra resultFormatContent.")]
                        ])->result();

                }

                if (empty($intAmount)) {
                        \Log::info("YOOBIL COLLECTION MESSAGE không thấy amount" . json_encode($arrParams));
                        return $this->transactionService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                                [__("Không tìm thấy amount.")]
                        ])->result();
                }


                $intTotalBalance = 0;
                $resultCreatePayment = $this->transactionService->createPayment([
                        "user_id" => $objUserToken->user_id,
                        'ref_code' => $strTradeNo,
                        'user_token_id' => $objUserToken->id,
                        'amount' => $intAmount,
                        "bank_account_name" => $strBankAccountName,
                        "bank_account_number" => $strBankAccountNumber,
                        "gateway_id" => 3,
                ]);

                if ($resultCreatePayment["error_code"] != 0) {
                        \Log::info("YOOBIL COLLECTION resultCreatePayment" . json_encode($resultCreatePayment));
                        return $this->transactionService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                                [__("Có lỗi khi khởi tạo thanh toán.")]
                        ])->result();
                }

                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $strCode = $resultCreatePayment["data"]["code"];
                $resultUpdateTransaction = $this->transactionService->updateResultTransaction(["received_date" => $strReceivedDate, "content" => $strBody, "code" => $strCode, "amount" => $intAmount, "total_balance" => $intTotalBalance]);
                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("YOOBIL MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                $strMsg = "YOOBIL\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                ])->onQueue('notification');

                $strMsg = "THÔNG BÁO\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                        'user_id' => $objUserToken->user_id
                ])->onQueue('notification');

                return response()->json($resultUpdateTransaction);

        }


        public function yoobilPayout()
        {
                $arrParams = request()->all();
                $withdrawYoobilLogService = new \App\Services\WithdrawYoobilLogService();
                return response()->json($withdrawYoobilLogService->updateCallbackV2($arrParams));
        }


        public function gpayCollection(Request $request)
        {
                $arrParamsOrigin = $arrParams = request()->all();
                $arrParamContents = json_decode(request()->getContent(), true);
                \Log::info("gpayForward INFO:" . json_encode($arrParams));
                if (empty($arrParams["token"])) {
                        \Log::info("gpayForward không tìm thấy token:" . json_encode($arrParams));
                        return response()->json(["token null"]);
                }

                if (empty($arrParams["gpay_trans_id"])) {
                        \Log::info("gpayForward không tìm thấy result:" . json_encode($arrParams));
                        return;
                }


                $strToken = $arrParams["token"];
                $objUserToken = UserToken::where('token', $strToken)->first();
                if (!$objUserToken) {
                        \Log::info("gpayForward token không hợp lệ");
                        return;
                }
                /**
                 * {"code":10000,"msg":"the request is succeed.","result":{"accountName":"NGUYEN HOANG NAM","accountNo":"9631242000000940610","amount":20000,"bankName":"BIDV","businessId":19,"currency":"VND","expireDate":null,"feeId":25,"merchantId":215,"orderNo":"ktDOu73129","purchaseAmount":20000,"purchaseCurrency":"VND","purchaseTime":"1701338882545","remark":"MBVCB.4737834761.094885.nguyen van ck.CT tu 9338499422 BUI THI KIEU toi 9631242000000940610 NGUYEN H","sign":"aJupZK66ti\/dHuxqZbTTo5QYVKA8AuihPLCqPRImcYnf8xLYNG1PYX+wseIC27Ymp\/hE71g08W2XGiIY\/N03ho03RIHVL4UDe+MBwNt0EZJGUg3Hi6Qo\/Z7gs0ArlEVvdxE78nlNj7NctlLrpARY0bkx0rjoY2TWOhlT5IfrGjlWuuDqH4JFzb8N4bvGH9G4qlhFmnKwUo6rja6DL17qdP8Fz5251ydcfeKVa3oVxQKPmDc9kXfhoaYLBcf8IzeNirhCDI9kgPM9QtsZwYYclPimpSBjVE0pYnft5+RAP5LcVzrCO0Agx9UTgdwzRF3dgxjPMb24iBgFb8MCIzu4Kg==","tradeNo":"0050001730166790975262720","userName":"NGUYEN HOANG NAM"},"q":"\/api\/test\/test"}  
                 */
                $strAction = $arrParams["action"] ?? "";
                if ($strAction != "CHANGE_BALANCE") {
                        \Log::info("gpayForward action không hợp lệ");
                        \App\Jobs\TelegramNotificationJob::dispatch([
                                'message' => "gpayForward action không hợp lệ " . json_encode($arrParams),
                                'type' => "custome",
                                'chat_id' => '-4161734390',
                        ])->onQueue('notification');
                        return;
                }

                $strBody = $arrParams["message"] ?? "";
                $intAmount = $arrParams["amount"] ?? 0;
                $strBankAccountNumber = $arrParams["account_number"] ?? "";
                $strReceivedDate = 0;
                $strTradeNo = $arrParams["gpay_trans_id"] ?? "";
                $intUserId = $objUserToken->user_id;
                $gpay = new Gpay();
                $objUserGpayConfig = UserGpayConfig::where('user_id', $intUserId)->first();
                $strPublicKey = (General::beautyKey($objUserGpayConfig->gpay_public_key, "PUBLIC KEY"));
                $verified = $gpay->setPublicKey($strPublicKey)->verifySign($arrParamContents ?? []);
                if (!$verified) {
                        $this->appMessageService->add([
                                'device' => $arrParams["device"] ?? "",
                                'sender' => 'gpay', // ($arrParams["result"]["accountNo"] ?? "") . " " . ($arrParams["result"]["accountName"] ?? ""),
                                'receiver' => "",
                                'content' => $strBody,
                                'content_origin' => $strBody,
                                'type_id' => 3,
                        ]);
                        \App\Jobs\TelegramNotificationJob::dispatch([
                                'message' => "verified Không hợp lệ " . json_encode($arrParams["result"] ?? []),
                                'type' => "custome",
                                'chat_id' => '-4161734390',
                        ])->onQueue('notification');
                        \Log::info("verified Không hợp lệ " . json_encode($arrParams));
                        // return;
                }

                if (!empty($strReceivedDate)) {
                        $strReceivedDate = date('Y-m-d H:i:s', round($strReceivedDate / 1000));
                }

                if (empty($strReceivedDate)) {
                        $strReceivedDate = date('Y-m-d H:i:s', time());
                }

                $arrParams = [
                        'device' => $arrParams["device"] ?? "",
                        'sender' => 'gpay',
                        'receiver' => "",
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 3,
                ];

                $ressultAdd = $this->appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                        \Log::info("gpayForward FW MESSAGE ressultAdd" . json_encode($ressultAdd));
                        return $ressultAdd;
                }


                /**
                 * @var 
                 * Nếu có transaction sẽ không đẩy qua bên dịch vụ gốc nữa
                 */
                $resultDetectCodeTransaction = $this->detectCodeTransaction([
                        "amount" => $intAmount,
                        "content" => $strBody,
                        "received_date" => $strReceivedDate
                ]);

                if ($resultDetectCodeTransaction["error_code"] == 0) {
                        return $resultDetectCodeTransaction;
                }

                $strBankAccountName = "";
                $intTotalBalance = 0;
                $resultCreatePayment = $this->transactionService->createPayment([
                        "user_id" => $objUserToken->user_id,
                        'ref_code' => $strTradeNo,
                        'user_token_id' => $objUserToken->id,
                        'amount' => $intAmount,
                        "bank_account_name" => $strBankAccountName,
                        "bank_account_number" => $strBankAccountNumber,
                ]);

                if ($resultCreatePayment["error_code"] != 0) {
                        \Log::info("gpayForward MESSAGE resultCreatePayment" . json_encode($resultCreatePayment));
                        return $resultCreatePayment;
                }
                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $strCode = $resultCreatePayment["data"]["code"];
                $resultUpdateTransaction = $this->transactionService->updateResultTransaction([
                        "bank_account_name" => $strBankAccountName,
                        "bank_account_number" => $strBankAccountNumber,
                        "received_date" => $strReceivedDate,
                        "content" => $strBody,
                        "code" => $strCode,
                        "amount" => $intAmount,
                        "total_balance" => $intTotalBalance
                ]);
                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("GPAY MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                $strMsg = "GPAY\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                ])->onQueue('notification');

                $strMsg = "THÔNG BÁO\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                        'user_id' => $objUserToken->user_id
                ])->onQueue('notification');

                return response()->json($resultUpdateTransaction);

        }

        public function paymenthotCollection(Request $request)
        {
                $arrParams = request()->all();
                \Log::info("paymenthotForward arrParams:" . json_encode($arrParams));
                if (empty($arrParams["token"])) {
                        \Log::info("paymenthotForward không tìm thấy token:" . json_encode($arrParams));
                        return response()->json(["token null"]);
                }

                if (empty($arrParams["orderId"])) {
                        \Log::info("paymenthotForward không tìm thấy orderId:" . json_encode($arrParams));
                        return;
                }


                $strToken = $arrParams["token"];
                $objUserToken = UserToken::where('token', $strToken)->first();
                if (!$objUserToken) {
                        \Log::info("paymenthotForward token không hợp lệ");
                        return;
                }

                $strCode = $arrParams["code"] ?? "";
                if ($strCode != "SUCCESS") {
                        \Log::info("paymenthotForward giao dịch thất bại " . json_encode($arrParams));
                        return;
                }

                $strBody = $arrParams["content"] ?? "";
                $intAmount = $arrParams["amount"] ?? 0;
                $strTradeNo = $arrParams["txnId"] ?? "";
                $receiverBankRefName = $arrParams["receiverBankRefName"] ?? "";
                $receiverBankRefNumber = $arrParams["receiverBankRefNumber"] ?? "";



                $intReceivedYear = substr($arrParams["txnDate"], 0, 4);
                $intReceivedMonth = substr($arrParams["txnDate"], 4, 2);
                $intReceivedDay = substr($arrParams["txnDate"], 6, 2);
                $intReceivedHour = substr($arrParams["txnDate"], 8, 2);
                $intReceivedMunite = substr($arrParams["txnDate"], 10, 2);
                $intReceivedSecond = substr($arrParams["txnDate"], 12, 2);

                $strReceivedDate = $intReceivedYear . "-" . $intReceivedMonth . "-" . $intReceivedDay . " " . $intReceivedHour . ":" . $intReceivedMunite . ":" . $intReceivedSecond;

                $arrParams = [
                        'device' => $arrParams["device"] ?? "",
                        'sender' => 'paymenthot',
                        'receiver' => "",
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 3,
                ];

                $ressultAdd = $this->appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                        \Log::info("paymenthotForward FW MESSAGE ressultAdd" . json_encode($ressultAdd));
                        return $ressultAdd;
                }

                /**
                 * @var 
                 * Nếu có transaction sẽ không đẩy qua bên dịch vụ gốc nữa
                 */
                $resultDetectCodeTransaction = $this->detectCodeTransaction([
                        "amount" => $intAmount,
                        "content" => $strBody,
                        "received_date" => $strReceivedDate,
                        "bank_account_name" => $receiverBankRefName,
                        "bank_account_number" => $receiverBankRefNumber,
                ]);

                if ($resultDetectCodeTransaction["error_code"] == 0) {
                        return $resultDetectCodeTransaction;
                }

                $intUserId = $objUserToken->user_id;
                $intUserTokenId = $objUserToken->id;


                /**
                 * Kiểm tra nội dung có mã transaction hoặc có mã user code hay không
                 */

                /**
                 * Kiểm tra QR định danh xem có dữ liệu khoong
                 */
                $userIdQrcodeId = "";
                $userIdQrcodeName = "";
                $userIdQrcodeCode = "";


                preg_match('/RBT([A-Z0-9])\w+/', $strBody, $arrResult);
                if (!empty($arrResult[0])) {
                        $strCode = $arrResult[0] ?? "";
                        $strCode = str_replace("RBT", "", $strCode);
                        $objUser = User::where('payment_code', $strCode)->first();
                        if ($objUser) {
                                $intUserId = $objUser->id;
                                $objUserToken = UserToken::where('user_id', $intUserId)->first();
                                if ($objUserToken) {
                                        $intUserTokenId = $objUserToken->id;
                                }

                        }

                } else {
                        $arrCodeTemp = explode(" ", $strBody);
                        $arrCodeTemp = array_merge(explode(".", $strBody), $arrCodeTemp);
                        $arrCodeTemp = array_merge(explode("-", $strBody), $arrCodeTemp);
                        $arrCode = [];
                        foreach ($arrCodeTemp as $key) {
                                if (strlen($key) < 3 || strlen($key) > 10) {
                                        continue;
                                }
                                $arrCode[] = $key;
                        }
                        if (!empty($arrCode)) {
                                $objUserIdQrcode = UserIdQrcode::whereIn('code', $arrCode)->where('bank_account_number', $receiverBankRefNumber)->first();
                                if ($objUserIdQrcode) {
                                        $userIdQrcodeId = $objUserIdQrcode->id;
                                        $userIdQrcodeName = $objUserIdQrcode->name;
                                        $userIdQrcodeCode = $objUserIdQrcode->code;
                                        $intUserId = $objUserIdQrcode->user_id;
                                        $objUserToken = UserToken::where('user_id', $intUserId)->first();
                                        if ($objUserToken) {
                                                $intUserTokenId = $objUserToken->id;
                                        }
                                }
                        }
                }

                $intTotalBalance = 0;
                $resultCreatePayment = $this->transactionService->createPayment([
                        "user_id" => $intUserId,
                        'ref_code' => $strTradeNo,
                        'user_token_id' => $intUserTokenId,
                        'amount' => $intAmount,
                        "bank_account_name" => $receiverBankRefName,
                        "bank_account_number" => $receiverBankRefNumber,
                        "user_id_qrcode_id" => $userIdQrcodeId,
                        "user_id_qrcode_name" => $userIdQrcodeName,
                        "user_id_qrcode_code" => $userIdQrcodeCode,
                        "gateway_id" => 1,
                ]);


                if ($resultCreatePayment["error_code"] != 0) {
                        \Log::info("paymenthotForward MESSAGE resultCreatePayment" . json_encode($resultCreatePayment));
                        return $resultCreatePayment;
                }
                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 * 
                 *       "user_id_qrcode_id" => $userIdQrcodeId,
                 *       "user_id_qrcode_name" => $userIdQrcodeName,
                 *       Không cần vì nó không có ra 
                 */
                $strCode = $resultCreatePayment["data"]["code"];
                $resultUpdateTransaction = $this->transactionService->updateResultTransaction([
                        "received_date" => $strReceivedDate,
                        "content" => $strBody,
                        "code" => $strCode,
                        "amount" => $intAmount,
                        "total_balance" => $intTotalBalance
                ]);
                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("paymenthotForward MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                $strMsg = "paymenthotForward\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                ])->onQueue('notification');

                $strMsg = "THÔNG BÁO\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                        'user_id' => $objUserToken->user_id
                ])->onQueue('notification');

                return response()->json($resultUpdateTransaction);

        }

        public function paymenthotCollectionV2(Request $request)
        {
                $arrParams = request()->all();
                \Log::info("paymenthotForward arrParams:" . json_encode($arrParams));
                if (empty($arrParams["token"])) {
                        \Log::info("paymenthotForward không tìm thấy token:" . json_encode($arrParams));
                        return response()->json(["token null"]);
                }

                if (empty($arrParams["orderId"])) {
                        \Log::info("paymenthotForward không tìm thấy orderId:" . json_encode($arrParams));
                        return;
                }


                $strToken = $arrParams["token"];
                $objUserToken = UserToken::where('token_gateway', $strToken)->first();
                if (!$objUserToken) {
                        \Log::info("paymenthotForward token không hợp lệ");
                        return;
                }

                $strCode = $arrParams["code"] ?? "";
                if ($strCode != "SUCCESS") {
                        \Log::info("paymenthotForward giao dịch thất bại " . json_encode($arrParams));
                        return;
                }

                $strBody = $arrParams["content"] ?? "";
                $intAmount = $arrParams["amount"] ?? 0;
                $strTradeNo = $arrParams["txnId"] ?? "";
                $receiverBankRefName = $arrParams["receiverBankRefName"] ?? "";
                $receiverBankRefNumber = $arrParams["receiverBankRefNumber"] ?? "";



                $intReceivedYear = substr($arrParams["txnDate"], 0, 4);
                $intReceivedMonth = substr($arrParams["txnDate"], 4, 2);
                $intReceivedDay = substr($arrParams["txnDate"], 6, 2);
                $intReceivedHour = substr($arrParams["txnDate"], 8, 2);
                $intReceivedMunite = substr($arrParams["txnDate"], 10, 2);
                $intReceivedSecond = substr($arrParams["txnDate"], 12, 2);

                $strReceivedDate = $intReceivedYear . "-" . $intReceivedMonth . "-" . $intReceivedDay . " " . $intReceivedHour . ":" . $intReceivedMunite . ":" . $intReceivedSecond;

                $arrParams = [
                        'device' => $arrParams["device"] ?? "",
                        'sender' => 'paymenthot',
                        'receiver' => "",
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 3,
                ];

                $ressultAdd = $this->appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                        \Log::info("paymenthotForward FW MESSAGE ressultAdd" . json_encode($ressultAdd));
                        return $ressultAdd;
                }

                /**
                 * @var 
                 * Nếu có transaction sẽ không đẩy qua bên dịch vụ gốc nữa
                 */
                $resultDetectCodeTransaction = $this->detectCodeTransaction([
                        "amount" => $intAmount,
                        "content" => $strBody,
                        "received_date" => $strReceivedDate,
                        "bank_account_name" => $receiverBankRefName,
                        "bank_account_number" => $receiverBankRefNumber,
                ]);

                if ($resultDetectCodeTransaction["error_code"] == 0) {
                        return $resultDetectCodeTransaction;
                }

                $intUserId = $objUserToken->user_id;
                $intUserTokenId = $objUserToken->id;

                $strBankShortName = "";
                $strBankShortCode = "";
                $objUserVirtualAccount = UserVirtualAccount::where('bank_account_number', $receiverBankRefNumber)->first();
                if ($objUserVirtualAccount) {
                        $objUserToken = UserToken::where('user_id', $objUserVirtualAccount->user_id)->first();
                        $intUserId = $objUserToken->user_id;
                        $intUserTokenId = $objUserToken->id;

                        $strBankShortName = $objUserVirtualAccount->bank_short_name;
                        $strBankShortCode = $objUserVirtualAccount->bank_short_code;
                }


                /**
                 * Kiểm tra nội dung có mã transaction hoặc có mã user code hay không
                 */

                /**
                 * Kiểm tra QR định danh xem có dữ liệu khoong
                 */
                $userIdQrcodeId = "";
                $userIdQrcodeName = "";
                $userIdQrcodeCode = "";


                preg_match('/RBT([A-Z0-9])\w+/', $strBody, $arrResult);
                if (!empty($arrResult[0])) {
                        $strCode = $arrResult[0] ?? "";
                        $strCode = str_replace("RBT", "", $strCode);
                        $objUser = User::where('payment_code', $strCode)->first();
                        if ($objUser) {
                                $intUserId = $objUser->id;
                                $objUserToken = UserToken::where('user_id', $intUserId)->first();
                                if ($objUserToken) {
                                        $intUserTokenId = $objUserToken->id;
                                }

                        }

                } else {
                        $arrCodeTemp = explode(" ", $strBody);
                        $arrCodeTemp = array_merge(explode(".", $strBody), $arrCodeTemp);
                        $arrCodeTemp = array_merge(explode("-", $strBody), $arrCodeTemp);
                        $arrCode = [];
                        foreach ($arrCodeTemp as $key) {
                                if (strlen($key) < 3 || strlen($key) > 10) {
                                        continue;
                                }
                                $arrCode[] = $key;
                        }
                        if (!empty($arrCode)) {
                                $objUserIdQrcode = UserIdQrcode::whereIn('code', $arrCode)->where('bank_account_number', $receiverBankRefNumber)->first();
                                if ($objUserIdQrcode) {
                                        $userIdQrcodeId = $objUserIdQrcode->id;
                                        $userIdQrcodeName = $objUserIdQrcode->name;
                                        $userIdQrcodeCode = $objUserIdQrcode->code;
                                        $intUserId = $objUserIdQrcode->user_id;
                                        $objUserToken = UserToken::where('user_id', $intUserId)->first();
                                        if ($objUserToken) {
                                                $intUserTokenId = $objUserToken->id;
                                        }
                                }
                        }
                }

                $intTotalBalance = 0;
                $resultCreatePayment = $this->transactionService->createPayment([
                        "user_id" => $intUserId,
                        'ref_code' => $strTradeNo,
                        'user_token_id' => $intUserTokenId,
                        'amount' => $intAmount,
                        "bank_account_name" => $receiverBankRefName,
                        "bank_account_number" => $receiverBankRefNumber,
                        "user_id_qrcode_id" => $userIdQrcodeId,
                        "user_id_qrcode_name" => $userIdQrcodeName,
                        "user_id_qrcode_code" => $userIdQrcodeCode,
                        "bank_short_name" => $strBankShortName,
                        "bank_short_code" => $strBankShortCode,
                        "gateway_id" => 1,
                ]);


                if ($resultCreatePayment["error_code"] != 0) {
                        \Log::info("paymenthotForward MESSAGE resultCreatePayment" . json_encode($resultCreatePayment));
                        return $resultCreatePayment;
                }
                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 * 
                 *       "user_id_qrcode_id" => $userIdQrcodeId,
                 *       "user_id_qrcode_name" => $userIdQrcodeName,
                 *       Không cần vì nó không có ra 
                 */
                $strCode = $resultCreatePayment["data"]["code"];
                $resultUpdateTransaction = $this->transactionService->updateResultTransaction([
                        "received_date" => $strReceivedDate,
                        "content" => $strBody,
                        "code" => $strCode,
                        "amount" => $intAmount,
                        "total_balance" => $intTotalBalance
                ]);
                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("paymenthotForward MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                $strMsg = "paymenthotForward\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                ])->onQueue('notification');

                $strMsg = "THÔNG BÁO\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                        'user_id' => $objUserToken->user_id
                ])->onQueue('notification');

                return response()->json($resultUpdateTransaction);

        }


        public function paymenthotPayout(Request $request)
        {
                $arrParams = request()->all();
                if (empty($arrParams["token"])) {
                        \Log::info("paymenthotPayout không tìm thấy token:" . json_encode($arrParams));
                        return response()->json(["token null"]);
                }
                if (empty($arrParams["auditNumber"])) {
                        \Log::info("paymenthotPayout không tìm thấy auditNumber:" . json_encode($arrParams));
                        return;
                }

                $strOrderNo = $arrParams["auditNumber"];
                $strCode = $arrParams["code"] ?? "";
                $strMessage = $arrParams["message"] ?? "";

                $objUserWithdraw = UserWithdraw::where('trans_code', $strOrderNo)->first();
                if (!$objUserWithdraw) {
                        \Log::info("paymenthotPayout Giao dịch không tồn tại:" . json_encode($arrParams));
                        return;
                }
                /**
                 * CẬP NHẬT CALLBACK
                 */
                WithdrawPaymenthotLog::where('trans_code', $strOrderNo)->update(["data_callback_response" => json_encode($arrParams)]);
                $userWithdrawService = new UserWithdrawService();
                if ($strCode == "SUCCESS") {
                        \Log::info("paymenthotPayout Thành công:" . json_encode($arrParams));
                        $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 2, "note" => "Thành công"]);
                } else {
                        $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 5, "note" => "Có lỗi ( $strMessage   ) vui lòng báo quản trị viên"]);
                }
                return response()->json([]);
        }

        public function paymenthotPayoutV2(Request $request)
        {
                $arrParams = request()->all();
                if (empty($arrParams["token"])) {
                        \Log::info("paymenthotPayout không tìm thấy token:" . json_encode($arrParams));
                        return response()->json(["token null"]);
                }
                if (empty($arrParams["auditNumber"])) {
                        \Log::info("paymenthotPayout không tìm thấy auditNumber:" . json_encode($arrParams));
                        return;
                }

                $strOrderNo = $arrParams["auditNumber"];
                $strCode = $arrParams["code"] ?? "";
                $strMessage = $arrParams["message"] ?? "";

                $objUserWithdraw = UserWithdraw::where('trans_code', $strOrderNo)->first();
                if (!$objUserWithdraw) {
                        \Log::info("paymenthotPayout Giao dịch không tồn tại:" . json_encode($arrParams));
                        return;
                }
                /**
                 * CẬP NHẬT CALLBACK
                 */
                WithdrawPaymenthotLog::where('trans_code', $strOrderNo)->update(["data_callback_response" => json_encode($arrParams)]);
                $userWithdrawService = new UserWithdrawService();
                if ($strCode == "SUCCESS") {
                        \Log::info("paymenthotPayout Thành công:" . json_encode($arrParams));
                        $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 2, "note" => "Thành công"]);
                } else {
                        $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 5, "note" => "Có lỗi ( $strMessage   ) vui lòng báo quản trị viên"]);
                }
                return response()->json([]);
        }


        public function detectCodeTransaction($arrParams)
        {

                $strBody = $arrParams["content"] ?? "";
                $intAmount = $arrParams["amount"] ?? 0;
                $strReceivedDate = $arrParams["received_date"] ?? 0;
                $strAccountName = $arrParams["bank_account_name"] ?? "";
                $strBankAccountNumber = $arrParams["bank_account_number"] ?? "";
                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $resultFormatContent = $this->transactionService->formatContentToTransactionCode(["content" => $strBody]);
                if ($resultFormatContent["error_code"] != 0) {
                        \Log::info("detectCodeTransaction resultFormatContent" . json_encode($resultFormatContent));
                        return $resultFormatContent;
                }

                if (empty($intAmount)) {
                        return $this->transactionService->setStatusCode(404)->setMessage("")->setData($arrParams)->setErrors([
                                [__("Vui lòng nhập số tiền.")]
                        ])->result();
                }

                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $strCode = $resultFormatContent["data"]["code"];
                $intTotalBalance = $resultFormatContent["data"]["total_balance"];

                $resultUpdateTransaction = $this->transactionService->updateResultTransaction([
                        "bank_account_name" => $strAccountName,
                        "bank_account_number" => $strBankAccountNumber,
                        "received_date" => $strReceivedDate,
                        "content" => $strBody,
                        "code" => $strCode,
                        "amount" => $intAmount,
                        "total_balance" => $intTotalBalance
                ]);

                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("detectCodeTransaction resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                        return $resultUpdateTransaction;
                }
                return $resultUpdateTransaction;

        }



        public function seapayCollection(Request $request)
        {
                $arrParams = request()->all();
                \Log::info("seapayCollection arrParams:" . json_encode($arrParams));
                if (empty($arrParams["token"])) {
                        \Log::info("seapayCollection không tìm thấy token:" . json_encode($arrParams));
                        return response()->json(["token null"]);
                }

                $strToken = $arrParams["token"];
                $objUserToken = UserToken::where('token_gateway', $strToken)->first();
                if (!$objUserToken) {
                        \Log::info("seapayCollection token không hợp lệ");
                        return;
                }

                //   {
//   "gateway": "VPBank",
//   "transactionDate": "2026-02-27 08:51:00",
//   "accountNumber": "0858554372",
//   "subAccount": null,
//   "code": null,
//   "content": "NHAN TU 3336789666 TRACE 959153 ND gia han goi cuoc SIMDEPMI 2702",
//   "transferType": "in",
//   "description": "BankAPINotify NHAN TU 3336789666 TRACE 959153 ND gia han goi cuoc SIMDEPMI 2702",
//   "transferAmount": 500000,
//   "referenceCode": "FT26058070035003",
//   "accumulated": 0,
//   "id": 43456380
// }


                $strBody = $arrParams["content"] ?? "";
                $intAmount = $arrParams["transferAmount"] ?? 0;
                $strTradeNo = $arrParams["referenceCode"] ?? "";
                $receiverBankRefName = $arrParams["accountNumber"] ?? "";
                $receiverBankRefNumber = $arrParams["accountNumber"] ?? "";
                $strReceivedDate = $arrParams['transactionDate'] ?? "";


                $arrParams = [
                        'device' => "seapay",
                        'sender' => 'seapay',
                        'receiver' => "",
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 3,
                ];

                $ressultAdd = $this->appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                        \Log::info("seapayCollection FW MESSAGE ressultAdd" . json_encode($ressultAdd));
                        return $ressultAdd;
                }

                /**
                 * @var 
                 * Nếu có transaction sẽ không đẩy qua bên dịch vụ gốc nữa
                 */
                $resultDetectCodeTransaction = $this->detectCodeTransaction([
                        "amount" => $intAmount,
                        "content" => $strBody,
                        "received_date" => $strReceivedDate,
                        "bank_account_name" => $receiverBankRefName,
                        "bank_account_number" => $receiverBankRefNumber,
                ]);

                if ($resultDetectCodeTransaction["error_code"] == 0) {
                        return $resultDetectCodeTransaction;
                }

                $intUserId = $objUserToken->user_id;
                $intUserTokenId = $objUserToken->id;

                $strBankShortName = "";
                $strBankShortCode = "";
                $objUserVirtualAccount = UserVirtualAccount::where('bank_account_number', $receiverBankRefNumber)->first();
                if ($objUserVirtualAccount) {
                        $objUserToken = UserToken::where('user_id', $objUserVirtualAccount->user_id)->first();
                        $intUserId = $objUserToken->user_id;
                        $intUserTokenId = $objUserToken->id;

                        $strBankShortName = $objUserVirtualAccount->bank_short_name;
                        $strBankShortCode = $objUserVirtualAccount->bank_short_code;
                }


                /**
                 * Kiểm tra nội dung có mã transaction hoặc có mã user code hay không
                 */

                /**
                 * Kiểm tra QR định danh xem có dữ liệu khoong
                 */
                $userIdQrcodeId = "";
                $userIdQrcodeName = "";
                $userIdQrcodeCode = "";


                preg_match('/RBT([A-Z0-9])\w+/', $strBody, $arrResult);
                if (!empty($arrResult[0])) {
                        $strCode = $arrResult[0] ?? "";
                        $strCode = str_replace("RBT", "", $strCode);
                        $objUser = User::where('payment_code', $strCode)->first();
                        if ($objUser) {
                                $intUserId = $objUser->id;
                                $objUserToken = UserToken::where('user_id', $intUserId)->first();
                                if ($objUserToken) {
                                        $intUserTokenId = $objUserToken->id;
                                }

                        }

                } else {
                        $arrCodeTemp = explode(" ", $strBody);
                        $arrCodeTemp = array_merge(explode(".", $strBody), $arrCodeTemp);
                        $arrCodeTemp = array_merge(explode("-", $strBody), $arrCodeTemp);
                        $arrCode = [];
                        foreach ($arrCodeTemp as $key) {
                                if (strlen($key) < 3 || strlen($key) > 10) {
                                        continue;
                                }
                                $arrCode[] = $key;
                        }
                        if (!empty($arrCode)) {
                                $objUserIdQrcode = UserIdQrcode::whereIn('code', $arrCode)->where('bank_account_number', $receiverBankRefNumber)->first();
                                if ($objUserIdQrcode) {
                                        $userIdQrcodeId = $objUserIdQrcode->id;
                                        $userIdQrcodeName = $objUserIdQrcode->name;
                                        $userIdQrcodeCode = $objUserIdQrcode->code;
                                        $intUserId = $objUserIdQrcode->user_id;
                                        $objUserToken = UserToken::where('user_id', $intUserId)->first();
                                        if ($objUserToken) {
                                                $intUserTokenId = $objUserToken->id;
                                        }
                                }
                        }
                }

                $intTotalBalance = 0;
                $resultCreatePayment = $this->transactionService->createPayment([
                        "user_id" => $intUserId,
                        'ref_code' => $strTradeNo,
                        'user_token_id' => $intUserTokenId,
                        'amount' => $intAmount,
                        "bank_account_name" => $receiverBankRefName,
                        "bank_account_number" => $receiverBankRefNumber,
                        "user_id_qrcode_id" => $userIdQrcodeId,
                        "user_id_qrcode_name" => $userIdQrcodeName,
                        "user_id_qrcode_code" => $userIdQrcodeCode,
                        "bank_short_name" => $strBankShortName,
                        "bank_short_code" => $strBankShortCode,
                        "gateway_id" => 1,
                ]);


                if ($resultCreatePayment["error_code"] != 0) {
                        \Log::info("paymenthotForward MESSAGE resultCreatePayment" . json_encode($resultCreatePayment));
                        return $resultCreatePayment;
                }
                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 * 
                 *       "user_id_qrcode_id" => $userIdQrcodeId,
                 *       "user_id_qrcode_name" => $userIdQrcodeName,
                 *       Không cần vì nó không có ra 
                 */
                $strCode = $resultCreatePayment["data"]["code"];
                $resultUpdateTransaction = $this->transactionService->updateResultTransaction([
                        "received_date" => $strReceivedDate,
                        "content" => $strBody,
                        "code" => $strCode,
                        "amount" => $intAmount,
                        "total_balance" => $intTotalBalance
                ]);
                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("paymenthotForward MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                $strMsg = "paymenthotForward\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                ])->onQueue('notification');

                $strMsg = "THÔNG BÁO\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                        'user_id' => $objUserToken->user_id
                ])->onQueue('notification');

                return response()->json($resultUpdateTransaction);

        }

        public function notiForward(Request $request)
        {
                $arrParams = $request->all();
                $arrParamBodys = json_decode($request->getContent(), true);
                \Log::info("notiForward " . json_encode($arrParams));
                if (empty($arrParams["token"])) {
                        \Log::info("notiForward không tìm thấy token:" . json_encode($arrParams));
                        return;
                }


                $strToken = $arrParams["token"];
                $objUserToken = UserToken::where('token', $strToken)->first();
                if (!$objUserToken) {
                        \Log::info("notiForward token không hợp lệ ");
                        return;
                }

                if (!empty($arrParamBodys["options"]["badge"])) {
                        unset($arrParamBodys["options"]["badge"]);
                }
                if (!empty($arrParamBodys["options"]["icon"])) {
                        unset($arrParamBodys["options"]["icon"]);
                }

                $strSender = trim($arrParamBodys["package"] ?? "");
                if (!in_array($strSender, $this->arrAllowSener)) {
                        $strJsonParramBody = str_replace(["\u2068", "\u2069"], "", json_encode($arrParamBodys));
                        $arrParamBodys = json_decode($strJsonParramBody, true);
                        $strSender = trim($arrParamBodys["title"] ?? "", " ");
                        if (!in_array($strSender, $this->arrAllowSener)) {
                                \Log::info("REJECT SENDER $strSender " . json_encode($arrParamBodys));
                                return "";
                        }
                }

                $receiverBankRefName = $arrParams["bank_account_name"] ?? "";
                $receiverBankRefNumber = $arrParams["bank_account_number"] ?? "";


                $strBody = $arrParamBodys["options"]["body"] ?? "";
                $arrParams = [
                        'device' => $arrParams["device"] ?? "",
                        'sender' => $strSender,
                        'receiver' => "",
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 1,
                ];


                $ressultAdd = $this->appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                        \Log::info("notiForward MESSAGE ressultAdd" . json_encode($ressultAdd));
                        return $ressultAdd;
                }

                $resultFormatContent = $this->transactionService->formatContentToTransactionCode(["content" => $strBody, 'bypass_check_exist_code' => true]);
                \Log::info("notiForward MESSAGE ressultAdd" . json_encode($resultFormatContent));

                if ($resultFormatContent["error_code"] != 0) {
                        \Log::info("notiForward MESSAGE resultFormatContent" . json_encode($resultFormatContent));
                        return $resultFormatContent;
                }

                if (empty($resultFormatContent["data"]["amount"])) {
                        \Log::info("notiForward MESSAGE không thấy amount" . json_encode($arrParamBodys));
                        return $resultFormatContent;
                }

                /**
                 * Tạo một giao dịch với số tiền tương ứng
                 */
                $intAmount = $resultFormatContent["data"]["amount"];
                $intTotalBalance = $resultFormatContent["data"]["total_balance"];
                $strReceivedDate = $resultFormatContent["data"]["received_date"] ?? "";
                if ($intAmount < 0) {
                        \Log::info("notiForward Amount <0" . json_encode($arrParamBodys));
                        return [];
                }

                /**
                 * @var 
                 * Nếu có transaction sẽ không đẩy qua bên dịch vụ gốc nữa
                 */
                $resultDetectCodeTransaction = $this->detectCodeTransaction([
                        "amount" => $intAmount,
                        "content" => $strBody,
                        "received_date" => $strReceivedDate,
                        "bank_account_name" => $receiverBankRefName,
                        "bank_account_number" => $receiverBankRefNumber,
                ]);

                if ($resultDetectCodeTransaction["error_code"] == 0) {
                        return $resultDetectCodeTransaction;
                }

                $intUserId = $objUserToken->user_id;
                $intUserTokenId = $objUserToken->id;

                $strBankShortName = "";
                $strBankShortCode = "";
                $objUserVirtualAccount = UserVirtualAccount::where('bank_account_number', $receiverBankRefNumber)->first();
                if ($objUserVirtualAccount) {
                        $objUserToken = UserToken::where('user_id', $objUserVirtualAccount->user_id)->first();
                        $intUserId = $objUserToken->user_id;
                        $intUserTokenId = $objUserToken->id;

                        $strBankShortName = $objUserVirtualAccount->bank_short_name;
                        $strBankShortCode = $objUserVirtualAccount->bank_short_code;
                }


                /**
                 * Kiểm tra nội dung có mã transaction hoặc có mã user code hay không
                 */

                /**
                 * Kiểm tra QR định danh xem có dữ liệu khoong
                 */
                $userIdQrcodeId = "";
                $userIdQrcodeName = "";
                $userIdQrcodeCode = "";


                preg_match('/RBT([A-Z0-9])\w+/', $strBody, $arrResult);
                if (!empty($arrResult[0])) {
                        $strCode = $arrResult[0] ?? "";
                        $strCode = str_replace("RBT", "", $strCode);
                        $objUser = User::where('payment_code', $strCode)->first();
                        if ($objUser) {
                                $intUserId = $objUser->id;
                                $objUserToken = UserToken::where('user_id', $intUserId)->first();
                                if ($objUserToken) {
                                        $intUserTokenId = $objUserToken->id;
                                }

                        }

                } else {
                        $arrCodeTemp = explode(" ", $strBody);
                        $arrCodeTemp = array_merge(explode(".", $strBody), $arrCodeTemp);
                        $arrCodeTemp = array_merge(explode("-", $strBody), $arrCodeTemp);
                        $arrCode = [];
                        foreach ($arrCodeTemp as $key) {
                                if (strlen($key) < 3 || strlen($key) > 10) {
                                        continue;
                                }
                                $arrCode[] = $key;
                        }
                        if (!empty($arrCode)) {
                                $objUserIdQrcode = UserIdQrcode::whereIn('code', $arrCode)->where('bank_account_number', $receiverBankRefNumber)->first();
                                if ($objUserIdQrcode) {
                                        $userIdQrcodeId = $objUserIdQrcode->id;
                                        $userIdQrcodeName = $objUserIdQrcode->name;
                                        $userIdQrcodeCode = $objUserIdQrcode->code;
                                        $intUserId = $objUserIdQrcode->user_id;
                                        $objUserToken = UserToken::where('user_id', $intUserId)->first();
                                        if ($objUserToken) {
                                                $intUserTokenId = $objUserToken->id;
                                        }
                                }
                        }
                }

                $intTotalBalance = 0;
                $resultCreatePayment = $this->transactionService->createPayment([
                        "user_id" => $intUserId,
                        'ref_code' => md5($strBody),
                        'user_token_id' => $intUserTokenId,
                        'amount' => $intAmount,
                        "bank_account_name" => $receiverBankRefName,
                        "bank_account_number" => $receiverBankRefNumber,
                        "user_id_qrcode_id" => $userIdQrcodeId,
                        "user_id_qrcode_name" => $userIdQrcodeName,
                        "user_id_qrcode_code" => $userIdQrcodeCode,
                        "bank_short_name" => $strBankShortName,
                        "bank_short_code" => $strBankShortCode,
                        "gateway_id" => 4,
                ]);


                if ($resultCreatePayment["error_code"] != 0) {
                        \Log::info("paymenthotForward MESSAGE resultCreatePayment" . json_encode($resultCreatePayment));
                        return $resultCreatePayment;
                }
                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 * 
                 *       "user_id_qrcode_id" => $userIdQrcodeId,
                 *       "user_id_qrcode_name" => $userIdQrcodeName,
                 *       Không cần vì nó không có ra 
                 */
                $strCode = $resultCreatePayment["data"]["code"];
                $resultUpdateTransaction = $this->transactionService->updateResultTransaction([
                        "received_date" => $strReceivedDate,
                        "content" => $strBody,
                        "code" => $strCode,
                        "amount" => $intAmount,
                        "total_balance" => $intTotalBalance
                ]);
                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("paymenthotForward MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                $strMsg = "paymenthotForward\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                ])->onQueue('notification');

                $strMsg = "THÔNG BÁO\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                \App\Jobs\TelegramNotificationJob::dispatch([
                        'message' => $strMsg,
                        'type' => "notification",
                        'chat_id' => '',
                        'user_id' => $objUserToken->user_id
                ])->onQueue('notification');

                return response()->json($resultUpdateTransaction);

        }
}
