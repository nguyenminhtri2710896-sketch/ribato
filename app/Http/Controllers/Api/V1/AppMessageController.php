<?php

namespace App\Http\Controllers\Api\V1;

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

class AppMessageController extends BaseController
{

        protected $appMessageService;
        protected $transactionService;
        protected $arrAllowSener = ["com.VCB", "Vietcombank", "Techcombank"];
        public function __construct(AppMessageService $appMessageService, TransactionService $transactionService)
        {
                $this->transactionService = $transactionService;
                $this->appMessageService = $appMessageService;
        }

        public function noti(Request $request)
        {
                $arrParams = $request->all();
                $arrParamBodys = json_decode($request->getContent(), true);
                \Log::info("NOTI " . json_encode($arrParams));
                // \Log::info("NOTI " . json_encode($arrParamBodys));
                /**
                 * Format lại data
                 *  {"title":"Test Notification","package":"net.kzxiv.notify.client","options":{"body":"This is some dummy text","badge":"data:image\/png;base64,"}} 
                 */
                $strSender = trim($arrParamBodys["package"] ?? "");
                if (!in_array($strSender, $this->arrAllowSener)) {
                        return "";
                }
                if (!empty($arrParamBodys["options"]["badge"])) {
                        unset($arrParamBodys["options"]["badge"]);
                }
                if (!empty($arrParamBodys["options"]["icon"])) {
                        unset($arrParamBodys["options"]["icon"]);
                }
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
                        \Log::info("SMS MESSAGE ressultAdd" . json_encode($ressultAdd));
                        return $ressultAdd;
                }

                $resultFormatContent = $this->transactionService->formatContentToTransactionCode(["content" => $strBody]);
                if ($resultFormatContent["error_code"] != 0) {
                        \Log::info("SMS MESSAGE resultFormatContent" . json_encode($resultFormatContent));
                        return $resultFormatContent;
                }

                if (empty($resultFormatContent["data"]["amount"])) {
                        \Log::info("SMS MESSAGE không thấy amount" . json_encode($arrParamBodys));
                        return $resultFormatContent;
                }

                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $strCode = $resultFormatContent["data"]["code"];
                $intAmount = $resultFormatContent["data"]["amount"];
                $intTotalBalance = $resultFormatContent["data"]["total_balance"];
                $strReceivedDate = $resultFormatContent["data"]["received_date"] ?? "";

                $resultUpdateTransaction = $this->transactionService->updateResultTransaction(["received_date" => $strReceivedDate, "content" => $strBody, "code" => $strCode, "amount" => $intAmount, "total_balance" => $intTotalBalance]);

                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("SMS MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                return response()->json($resultUpdateTransaction);
        }


        public function sms(Request $request)
        {
                $arrParams = $request->all();
                $arrParamBodys = json_decode($request->getContent(), true);
                \Log::info("SMS " . json_encode($arrParams));
                /**
                 * Format lại data
                 * {"id":"null","address":"SmsForwarder","body":"Test message","date":"1701248841180","dateSent":"null","read":"null","seen":"null","subject":"null","subscriptionId":"null","type":"null","status":"null"}  
                 */
                $strSender = trim($arrParamBodys["address"] ?? "", "+");
                if (!in_array($strSender, $this->arrAllowSener)) {
                        return "";
                }

                $strBody = $arrParamBodys["body"] ?? "";
                $arrParams = [
                        'device' => $arrParams["device"] ?? "",
                        'sender' => $strSender,
                        'receiver' => "",
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 2,
                ];

                $ressultAdd = $this->appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                        \Log::info("SMS MESSAGE ressultAdd" . json_encode($ressultAdd));
                        return $ressultAdd;
                }

                $resultFormatContent = $this->transactionService->formatContentToTransactionCode(["content" => $strBody]);
                if ($resultFormatContent["error_code"] != 0) {
                        \Log::info("SMS MESSAGE resultFormatContent" . json_encode($resultFormatContent));
                        return $resultFormatContent;
                }

                if (empty($resultFormatContent["data"]["amount"])) {
                        \Log::info("SMS MESSAGE không thấy amount" . json_encode($arrParamBodys));
                        return $resultFormatContent;
                }

                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $strCode = $resultFormatContent["data"]["code"];
                $intAmount = $resultFormatContent["data"]["amount"];
                $intTotalBalance = $resultFormatContent["data"]["total_balance"];
                $strReceivedDate = $resultFormatContent["data"]["received_date"] ?? "";

                $resultUpdateTransaction = $this->transactionService->updateResultTransaction(["received_date" => $strReceivedDate, "content" => $strBody, "code" => $strCode, "amount" => $intAmount, "total_balance" => $intTotalBalance]);

                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("SMS MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                return response()->json($resultUpdateTransaction);
        }

        public function yoobil(Request $request)
        {
                $arrParams = request()->all();
                \Log::info("YOOBIL:" . json_encode($arrParams));
                /**
                 * {"code":10000,"msg":"the request is succeed.","result":{"accountName":"NGUYEN HOANG NAM","accountNo":"9631242000000940610","amount":20000,"bankName":"BIDV","businessId":19,"currency":"VND","expireDate":null,"feeId":25,"merchantId":215,"orderNo":"ktDOu73129","purchaseAmount":20000,"purchaseCurrency":"VND","purchaseTime":"1701338882545","remark":"MBVCB.4737834761.094885.nguyen van ck.CT tu 9338499422 BUI THI KIEU toi 9631242000000940610 NGUYEN H","sign":"aJupZK66ti\/dHuxqZbTTo5QYVKA8AuihPLCqPRImcYnf8xLYNG1PYX+wseIC27Ymp\/hE71g08W2XGiIY\/N03ho03RIHVL4UDe+MBwNt0EZJGUg3Hi6Qo\/Z7gs0ArlEVvdxE78nlNj7NctlLrpARY0bkx0rjoY2TWOhlT5IfrGjlWuuDqH4JFzb8N4bvGH9G4qlhFmnKwUo6rja6DL17qdP8Fz5251ydcfeKVa3oVxQKPmDc9kXfhoaYLBcf8IzeNirhCDI9kgPM9QtsZwYYclPimpSBjVE0pYnft5+RAP5LcVzrCO0Agx9UTgdwzRF3dgxjPMb24iBgFb8MCIzu4Kg==","tradeNo":"0050001730166790975262720","userName":"NGUYEN HOANG NAM"},"q":"\/api\/test\/test"}  
                 */
                $strBody = $arrParams["result"]["remark"] ?? "";
                $intAmount = $arrParams["result"]["amount"] ?? 0;
                $strReceivedDate = $arrParams["result"]["purchaseTime"] ?? 0;
                if (!empty($strReceivedDate)) {
                        $strReceivedDate = round($strReceivedDate / 1000);
                }

                $arrParams = [
                        'device' => $arrParams["device"] ?? "",
                        'sender' => 'yoobil', // ($arrParams["result"]["accountNo"] ?? "") . " " . ($arrParams["result"]["accountName"] ?? ""),
                        'receiver' => "",
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 3,
                ];

                $ressultAdd = $this->appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                        \Log::info("YOOBIL MESSAGE ressultAdd" . json_encode($ressultAdd));
                        return $ressultAdd;
                }

                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $resultFormatContent = $this->transactionService->formatContentToTransactionCode(["content" => $strBody]);
                if ($resultFormatContent["error_code"] != 0) {
                        \Log::info("YOOBIL MESSAGE resultFormatContent" . json_encode($resultFormatContent));
                        return $resultFormatContent;
                }

                if (empty($intAmount)) {
                        \Log::info("YOOBIL MESSAGE không thấy amount" . json_encode($arrParams));
                        return $resultFormatContent;
                }

                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $strCode = $resultFormatContent["data"]["code"];
                $intTotalBalance = $resultFormatContent["data"]["total_balance"];
                // $strReceivedDate = $resultFormatContent["data"]["received_date"] ?? "";

                $resultUpdateTransaction = $this->transactionService->updateResultTransaction(["received_date" => $strReceivedDate, "content" => $strBody, "code" => $strCode, "amount" => $intAmount, "total_balance" => $intTotalBalance]);

                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("YOOBIL MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }
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
                /**
                 * Format lại data
                 *  {"title":"Test Notification","package":"net.kzxiv.notify.client","options":{"body":"This is some dummy text","badge":"data:image\/png;base64,"}} 
                 */
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



                $resultCreatePayment = $this->transactionService->createPayment([
                        "user_id" => $objUserToken->user_id,
                        'ref_code' => md5($intAmount . "|" . $intTotalBalance . "|" . $strReceivedDate),
                        'user_token_id' => $objUserToken->id,
                        'amount' => $intAmount,
                ]);

                if ($resultCreatePayment["error_code"] != 0) {
                        \Log::info("notiForward MESSAGE resultUpdateTransaction" . json_encode($resultCreatePayment));
                        return $resultCreatePayment;
                }

                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $strCode = $resultCreatePayment["data"]["code"];

                $resultUpdateTransaction = $this->transactionService->updateResultTransaction(["bank_short_name" => $strSender, "received_date" => $strReceivedDate, "content" => $strBody, "code" => $strCode, "amount" => $intAmount, "total_balance" => $intTotalBalance]);

                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("notiForward MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                $strMsg = "notiForward\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
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


        public function smsForward(Request $request)
        {
                $arrParams = $request->all();
                $arrParamBodys = json_decode($request->getContent(), true);
                \Log::info("smsForward " . json_encode($arrParams));
                if (empty($arrParams["token"])) {
                        \Log::info("smsForward không tìm thấy token:" . json_encode($arrParams));
                        return;
                }


                $strToken = $arrParams["token"];
                $objUserToken = UserToken::where('token', $strToken)->first();
                if (!$objUserToken) {
                        \Log::info("smsForward token không hợp lệ");
                        return;
                }
                /**
                 * Format lại data
                 *  {"title":"Test Notification","package":"net.kzxiv.notify.client","options":{"body":"This is some dummy text","badge":"data:image\/png;base64,"}} 
                 */
                $strSender = trim($arrParamBodys["address"] ?? "", "+");
                if (!in_array($strSender, $this->arrAllowSener)) {
                        return "";
                }

                $strBody = $arrParamBodys["body"] ?? "";
                $arrParams = [
                        'device' => $arrParams["device"] ?? "",
                        'sender' => $strSender,
                        'receiver' => "",
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 2,
                ];

                $ressultAdd = $this->appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                        \Log::info("smsForward MESSAGE ressultAdd" . json_encode($ressultAdd));
                        return $ressultAdd;
                }

                $resultFormatContent = $this->transactionService->formatContentToTransactionCode(["content" => $strBody, 'bypass_check_exist_code' => true]);
                if ($resultFormatContent["error_code"] != 0) {
                        \Log::info("smsForward MESSAGE resultFormatContent" . json_encode($resultFormatContent));
                        return $resultFormatContent;
                }

                if (empty($resultFormatContent["data"]["amount"])) {
                        \Log::info("smsForward MESSAGE không thấy amount" . json_encode($arrParamBodys));
                        return $resultFormatContent;
                }

                /**
                 * Tạo một giao dịch với số tiền tương ứng
                 */
                $strReceivedDate = $resultFormatContent["data"]["received_date"] ?? "";
                $intAmount = $resultFormatContent["data"]["amount"];
                $intTotalBalance = $resultFormatContent["data"]["total_balance"];

                if ($intAmount < 0) {
                        \Log::info("smsForward Amount <0" . json_encode($arrParamBodys));
                        return [];
                }
                $resultCreatePayment = $this->transactionService->createPayment([
                        "user_id" => $objUserToken->user_id,
                        'ref_code' => md5($intAmount . "|" . $intTotalBalance . "|" . $strReceivedDate),
                        'user_token_id' => $objUserToken->id,
                        'amount' => $intAmount,
                ]);

                if ($resultCreatePayment["error_code"] != 0) {
                        \Log::info("smsForward MESSAGE resultCreatePayment" . json_encode($resultCreatePayment));
                        return $resultCreatePayment;
                }

                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $strCode = $resultCreatePayment["data"]["code"];
                $resultUpdateTransaction = $this->transactionService->updateResultTransaction(["received_date" => $strReceivedDate, "content" => $strBody, "code" => $strCode, "amount" => $intAmount, "total_balance" => $intTotalBalance]);

                if ($resultUpdateTransaction["error_code"] != 0) {
                        \Log::info("smsForward MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
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


        public function yoobilForward(Request $request)
        {
                $arrParams = request()->all();
                \Log::info("YOOBIL FW:" . json_encode($arrParams));

                if (empty($arrParams["token"])) {
                        \Log::info("yoobilForward không tìm thấy token:" . json_encode($arrParams));
                        return;
                }

                if (empty($arrParams["result"])) {
                        \Log::info("yoobilForward không tìm thấy result:" . json_encode($arrParams));
                        return;
                }





                $strToken = $arrParams["token"];
                $objUserToken = UserToken::where('token', $strToken)->first();
                if (!$objUserToken) {
                        \Log::info("yoobilForward token không hợp lệ");
                        return;
                }
                /**
                 * {"code":10000,"msg":"the request is succeed.","result":{"accountName":"NGUYEN HOANG NAM","accountNo":"9631242000000940610","amount":20000,"bankName":"BIDV","businessId":19,"currency":"VND","expireDate":null,"feeId":25,"merchantId":215,"orderNo":"ktDOu73129","purchaseAmount":20000,"purchaseCurrency":"VND","purchaseTime":"1701338882545","remark":"MBVCB.4737834761.094885.nguyen van ck.CT tu 9338499422 BUI THI KIEU toi 9631242000000940610 NGUYEN H","sign":"aJupZK66ti\/dHuxqZbTTo5QYVKA8AuihPLCqPRImcYnf8xLYNG1PYX+wseIC27Ymp\/hE71g08W2XGiIY\/N03ho03RIHVL4UDe+MBwNt0EZJGUg3Hi6Qo\/Z7gs0ArlEVvdxE78nlNj7NctlLrpARY0bkx0rjoY2TWOhlT5IfrGjlWuuDqH4JFzb8N4bvGH9G4qlhFmnKwUo6rja6DL17qdP8Fz5251ydcfeKVa3oVxQKPmDc9kXfhoaYLBcf8IzeNirhCDI9kgPM9QtsZwYYclPimpSBjVE0pYnft5+RAP5LcVzrCO0Agx9UTgdwzRF3dgxjPMb24iBgFb8MCIzu4Kg==","tradeNo":"0050001730166790975262720","userName":"NGUYEN HOANG NAM"},"q":"\/api\/test\/test"}  
                 */
                $strBody = $arrParams["result"]["remark"] ?? "";
                $intAmount = $arrParams["result"]["amount"] ?? 0;
                $strReceivedDate = $arrParams["result"]["purchaseTime"] ?? 0;
                $strTradeNo = $arrParams["result"]["tradeNo"] ?? "";
                $intBusinessId = $arrParams["result"]["businessId"] ?? "0";
                $strBankAccountNumber = $arrParams["result"]["accountNo"] ?? "";
                $strBankAccountName = $arrParams["result"]["accountName"] ?? "";

                $intUserId = $objUserToken->user_id;

                $yoobil = new Yoobil();
                // $objUserYoobilConfig = UserYoobilConfig::where('user_id', $intUserId)->where('business_id', $intBusinessId)->first();
                // $strPublicKey        = (General::beautyKey($objUserYoobilConfig->yoobil_public_key, "PUBLIC KEY"));
                // $verified            = $yoobil->setSecretKey($objUserYoobilConfig->secret_key)->setPublicKey($strPublicKey)->verifySign($arrParams["result"] ?? []);

                // if (empty($arrParams["bypas_verified"])) {
                //         if (!$verified) {
                //                 $this->appMessageService->add([
                //                         'device' => $arrParams["device"] ?? "",
                //                         'sender' => 'yoobil', // ($arrParams["result"]["accountNo"] ?? "") . " " . ($arrParams["result"]["accountName"] ?? ""),
                //                         'receiver' => "",
                //                         'content' => $strBody,
                //                         'content_origin' => $strBody,
                //                         'type_id' => 3,
                //                 ]);
                //                 \App\Jobs\TelegramNotificationJob::dispatch([
                //                         'message' => "verified Không hợp lệ " . json_encode($arrParams["result"] ?? []),
                //                         'type' => "custome",
                //                         'chat_id' => '-4161734390',
                //                 ])->onQueue('notification');
                //                 \Log::info("verified Không hợp lệ " . json_encode($arrParams));
                //                 // return;
                //         }
                // }

                if (!empty($strReceivedDate)) {
                        $strReceivedDate = date('Y-m-d H:i:s', round($strReceivedDate / 1000));
                }

                $arrParams = [
                        'device' => $arrParams["device"] ?? "",
                        'sender' => 'yoobil', // ($arrParams["result"]["accountNo"] ?? "") . " " . ($arrParams["result"]["accountName"] ?? ""),
                        'receiver' => "",
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 3,
                ];

                $ressultAdd = $this->appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                        \Log::info("YOOBIL FW MESSAGE ressultAdd" . json_encode($ressultAdd));
                        return $ressultAdd;
                }

                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $resultFormatContent = $this->transactionService->formatContentToTransactionCode(["content" => $strBody, 'bypass_check_exist_code' => true]);
                if ($resultFormatContent["error_code"] != 0) {
                        \Log::info("YOOBIL FW MESSAGE resultFormatContent" . json_encode($resultFormatContent));
                        return $resultFormatContent;
                }

                if (empty($intAmount)) {
                        \Log::info("YOOBIL FW MESSAGE không thấy amount" . json_encode($arrParams));
                        return $resultFormatContent;
                }


                $intTotalBalance = $resultFormatContent["data"]["total_balance"];

                $resultCreatePayment = $this->transactionService->createPayment([
                        "user_id" => $objUserToken->user_id,
                        'ref_code' => $strTradeNo,
                        'user_token_id' => $objUserToken->id,
                        'amount' => $intAmount,
                        "bank_account_name" => $strBankAccountName,
                        "bank_account_number" => $strBankAccountNumber,
                ]);

                if ($resultCreatePayment["error_code"] != 0) {
                        \Log::info("yoobilForward MESSAGE resultCreatePayment" . json_encode($resultCreatePayment));
                        return $resultCreatePayment;
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

        public function gpayForward(Request $request)
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

                /**
                 * Cheat tạm cho shophoa
                 */
                if ($strBankAccountNumber == "M686262300000008") {
                        \Log::info("SHO HOA:" . json_encode($arrParamsOrigin));
                        $intTotalBalance = 0;
                        $objUserTokenHoa = UserToken::where('user_id', 901)->first();
                        if ($objUserTokenHoa) {
                                $resultCreatePayment = $this->transactionService->createPayment([
                                        "user_id" => 901,
                                        'ref_code' => "HOA" . $strTradeNo,
                                        'user_token_id' => $objUserTokenHoa->id,
                                        'amount' => $intAmount,
                                        "bank_account_name" => "",
                                        "bank_account_number" => $strBankAccountNumber,
                                ]);

                                if ($resultCreatePayment["error_code"] == 0) {
                                        $strCode = $resultCreatePayment["data"]["code"];
                                        $resultUpdateTransaction = $this->transactionService->updateResultTransaction(["received_date" => $strReceivedDate, "content" => $strBody, "code" => $strCode, "amount" => $intAmount, "total_balance" => $intTotalBalance]);
                                }

                                $strMsg = "THÔNG BÁO\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
                                \App\Jobs\TelegramNotificationJob::dispatch([
                                        'message' => $strMsg,
                                        'type' => "notification",
                                        'chat_id' => '',
                                        'user_id' => $objUserTokenHoa->user_id
                                ])->onQueue('notification');
                        }
                }
                // Chuyển tiếp giao dịch

                $objGatewayForward = GatewayForward::where("gateway_source_code", "gpay")->where("bank_account_number", $strBankAccountNumber)->first();
                $strBankAccountName = "";

                if ($objGatewayForward) {
                        $strBankAccountName = $objGatewayForward->bank_account_name;
                        $arrParamsOrigin["bank_account_number"] = $strBankAccountNumber;
                        $arrParamsOrigin["bank_account_name"] = $strBankAccountName;
                        \App\Jobs\GatewayForwardJob::dispatch([
                                'params' => $arrParamsOrigin,
                                'dest' => $objGatewayForward->gateway_desc_code,
                                'url_forward' => $objGatewayForward->url_forward,
                        ])->onQueue('forward');
                }


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

        public function neoxForward(Request $request)
        {
                $arrParams = request()->all();
                $arrParamContents = json_decode(request()->getContent(), true);
                \Log::info("neoxForward không tìm thấy token:" . json_encode($arrParams));
                if (empty($arrParams["token"])) {
                        \Log::info("neoxForward không tìm thấy token:" . json_encode($arrParams));
                        return response()->json(["token null"]);
                }

                if (empty($arrParams["transId"])) {
                        \Log::info("neoxForward không tìm thấy result:" . json_encode($arrParams));
                        return;
                }


                $strToken = $arrParams["token"];
                $objUserToken = UserToken::where('token', $strToken)->first();
                if (!$objUserToken) {
                        \Log::info("neoxForward token không hợp lệ");
                        return;
                }
                /**
                 * {"code":10000,"msg":"the request is succeed.","result":{"accountName":"NGUYEN HOANG NAM","accountNo":"9631242000000940610","amount":20000,"bankName":"BIDV","businessId":19,"currency":"VND","expireDate":null,"feeId":25,"merchantId":215,"orderNo":"ktDOu73129","purchaseAmount":20000,"purchaseCurrency":"VND","purchaseTime":"1701338882545","remark":"MBVCB.4737834761.094885.nguyen van ck.CT tu 9338499422 BUI THI KIEU toi 9631242000000940610 NGUYEN H","sign":"aJupZK66ti\/dHuxqZbTTo5QYVKA8AuihPLCqPRImcYnf8xLYNG1PYX+wseIC27Ymp\/hE71g08W2XGiIY\/N03ho03RIHVL4UDe+MBwNt0EZJGUg3Hi6Qo\/Z7gs0ArlEVvdxE78nlNj7NctlLrpARY0bkx0rjoY2TWOhlT5IfrGjlWuuDqH4JFzb8N4bvGH9G4qlhFmnKwUo6rja6DL17qdP8Fz5251ydcfeKVa3oVxQKPmDc9kXfhoaYLBcf8IzeNirhCDI9kgPM9QtsZwYYclPimpSBjVE0pYnft5+RAP5LcVzrCO0Agx9UTgdwzRF3dgxjPMb24iBgFb8MCIzu4Kg==","tradeNo":"0050001730166790975262720","userName":"NGUYEN HOANG NAM"},"q":"\/api\/test\/test"}  
                 */
                $strType = $arrParams["type"] ?? "";
                if ($strType != "TRANSACTION") {
                        \Log::info("neoxForward action không hợp lệ");
                        \App\Jobs\TelegramNotificationJob::dispatch([
                                'message' => "neoxForward action không hợp lệ " . json_encode($arrParams),
                                'type' => "custome",
                                'chat_id' => '-4161734390',
                        ])->onQueue('notification');
                        return;
                }

                $strBody = $arrParams["debitorInfomation"]["bankRemark"] ?? "";
                if (empty($strBody)) {
                        $strBody = $arrParams["debitorInformation"]["bankRemark"] ?? "";
                }
                $intAmount = $arrParams["amount"] ?? 0;
                $strBankAccountNumber = $arrParams["virtualAccountId"] ?? "";
                $strReceivedDate = strtotime($arrParams["transDate"]);
                $strTradeNo = $arrParams["transId"] ?? "";
                $strAccountName = $arrParams["accountName"] ?? "";
                $intUserId = $objUserToken->user_id;
                $objUserNeoxConfig = UserNeoxConfig::where('user_id', $intUserId)->first();

                if (!empty($strReceivedDate)) {
                        $strReceivedDate = date('Y-m-d H:i:s', $strReceivedDate);
                }

                if (empty($strReceivedDate)) {
                        $strReceivedDate = date('Y-m-d H:i:s', time());
                }

                $arrParams = [
                        'device' => $arrParams["device"] ?? "",
                        'sender' => 'neox',
                        'receiver' => "",
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 3,
                ];

                $ressultAdd = $this->appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                        \Log::info("neoxForward FW MESSAGE ressultAdd" . json_encode($ressultAdd));
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
                        "bank_account_name" => $strAccountName,
                        "bank_account_number" => $strBankAccountNumber,
                ]);

                if ($resultDetectCodeTransaction["error_code"] == 0) {
                        return $resultDetectCodeTransaction;
                }

                $intTotalBalance = 0;
                $resultCreatePayment = $this->transactionService->createPayment([
                        "user_id" => $objUserToken->user_id,
                        'ref_code' => $strTradeNo,
                        'user_token_id' => $objUserToken->id,
                        'amount' => $intAmount,
                        "bank_account_name" => $strAccountName,
                        "bank_account_number" => $strBankAccountNumber,
                ]);

                if ($resultCreatePayment["error_code"] != 0) {
                        \Log::info("neoxForward MESSAGE resultCreatePayment" . json_encode($resultCreatePayment));
                        return $resultCreatePayment;
                }
                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */
                $strCode = $resultCreatePayment["data"]["code"];
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
                        \Log::info("neoxForward MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                $strMsg = "NEOX\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
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

        public function paymenthotForward(Request $request)
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


        public function paymenthotForwardV2(Request $request)
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

                /**
                 * Kiểm tra số tài khoản có nãy nằm ở user nào sẽ chuyển về user đó
                 */

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
                \Log::info("paymenthotPayout arrParams " . rand(111111, 99999) . ":" . json_encode($arrParams));
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
                $strReceivedDate = $arrParams["received_date "] ?? 0;
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


        public function ribatoGpayForward(Request $request)
        {
                $arrParams = request()->all();
                \Log::info("ribatoForward INFO:" . json_encode($arrParams));
                if (empty($arrParams["token"])) {
                        \Log::info("ribatoForward không tìm thấy token:" . json_encode($arrParams));
                        return response()->json(["token null"]);
                }

                if (empty($arrParams["gpay_trans_id"])) {
                        \Log::info("ribatoForward không tìm thấy result:" . json_encode($arrParams));
                        return;
                }


                $strToken = $arrParams["token"];
                $objUserToken = UserToken::where('token_forward', $strToken)->first();
                if (!$objUserToken) {
                        \Log::info("ribatoForward token không hợp lệ");
                        return;
                }
                /**
                 * {"code":10000,"msg":"the request is succeed.","result":{"accountName":"NGUYEN HOANG NAM","accountNo":"9631242000000940610","amount":20000,"bankName":"BIDV","businessId":19,"currency":"VND","expireDate":null,"feeId":25,"merchantId":215,"orderNo":"ktDOu73129","purchaseAmount":20000,"purchaseCurrency":"VND","purchaseTime":"1701338882545","remark":"MBVCB.4737834761.094885.nguyen van ck.CT tu 9338499422 BUI THI KIEU toi 9631242000000940610 NGUYEN H","sign":"aJupZK66ti\/dHuxqZbTTo5QYVKA8AuihPLCqPRImcYnf8xLYNG1PYX+wseIC27Ymp\/hE71g08W2XGiIY\/N03ho03RIHVL4UDe+MBwNt0EZJGUg3Hi6Qo\/Z7gs0ArlEVvdxE78nlNj7NctlLrpARY0bkx0rjoY2TWOhlT5IfrGjlWuuDqH4JFzb8N4bvGH9G4qlhFmnKwUo6rja6DL17qdP8Fz5251ydcfeKVa3oVxQKPmDc9kXfhoaYLBcf8IzeNirhCDI9kgPM9QtsZwYYclPimpSBjVE0pYnft5+RAP5LcVzrCO0Agx9UTgdwzRF3dgxjPMb24iBgFb8MCIzu4Kg==","tradeNo":"0050001730166790975262720","userName":"NGUYEN HOANG NAM"},"q":"\/api\/test\/test"}  
                 */
                $strAction = $arrParams["action"] ?? "";
                if ($strAction != "CHANGE_BALANCE") {
                        \Log::info("ribatoForward action không hợp lệ");
                        \App\Jobs\TelegramNotificationJob::dispatch([
                                'message' => "ribatoForward action không hợp lệ " . json_encode($arrParams),
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
                $strBankAccountName = $arrParams["bank_account_name"] ?? "";
                if (!empty($strReceivedDate)) {
                        $strReceivedDate = date('Y-m-d H:i:s', round($strReceivedDate / 1000));
                }

                if (empty($strReceivedDate)) {
                        $strReceivedDate = date('Y-m-d H:i:s', time());
                }

                $arrParams = [
                        'device' => $arrParams["device"] ?? "",
                        'sender' => 'ribato',
                        'receiver' => "",
                        'content' => $strBody,
                        'content_origin' => $strBody,
                        'type_id' => 3,
                ];

                $ressultAdd = $this->appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                        \Log::info("ribatoForward FW MESSAGE ressultAdd" . json_encode($ressultAdd));
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
                        "bank_account_name" => $strBankAccountName,
                        "bank_account_number" => $strBankAccountNumber,
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
                                $objUserIdQrcode = UserIdQrcode::whereIn('code', $arrCode)->where('bank_account_number', $strBankAccountNumber)->first();
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
                        "bank_account_name" => $strBankAccountName,
                        "bank_account_number" => $strBankAccountNumber,
                        "user_id_qrcode_id" => $userIdQrcodeId,
                        "user_id_qrcode_name" => $userIdQrcodeName,
                        "user_id_qrcode_code" => $userIdQrcodeCode,
                        "gateway_id" => 2,
                ]);

                if ($resultCreatePayment["error_code"] != 0) {
                        \Log::info("ribatoForward MESSAGE resultCreatePayment" . json_encode($resultCreatePayment));
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
                        \Log::info("ribatoForward resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                $strMsg = "RIBATO\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
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
