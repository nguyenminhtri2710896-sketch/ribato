<?php

namespace App\Http\Controllers\Backend\V1;

use App\Models\Bank;
use App\Models\GatewayAccount;
use App\Models\UserYoobilConfig;
use App\Services\BankService;
use App\Services\GatewayAccountService;
use App\Services\GatewayAccountTransactionService;
use App\Services\ToolService;
use App\Services\TransactionService;
use App\Services\UserTransactionService;
use App\Services\UserVirtualAccountService;
use App\Services\UserWithdrawService;
use App\Services\WithdrawGpayLogService;
use App\Services\WithdrawPaymenthotLogService;
use App\Utilities\General;
use App\Utilities\Gpay;
use App\Utilities\Telegram;
use App\Utilities\Yoobil;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Crypt;

use Pay2Pay\Pay2Pay;
class TestController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $transactionService = null;
    public function __construct()
    {
        $this->transactionService = new TransactionService();
    }
    public function detectCodeTransaction($arrParams)
    {


        $strBody = $arrParams["content"] ?? "";
        $intAmount = $arrParams["amount"] ?? 0;
        $strReceivedDate = $arrParams["received_date "] ?? 0;

        /**
         * Gọi qua app transaction đẩy nội dung qua xử lý
         */
        $resultFormatContent = $this->transactionService->formatContentToTransactionCode(["content" => $strBody]);
        if ($resultFormatContent["error_code"] != 0) {
            \Log::info("detectCodeTransaction resultFormatContent" . json_encode($resultFormatContent));
            return $resultFormatContent;
        }

        if (empty($intAmount)) {
            \Log::info("detectCodeTransaction không thấy amount" . json_encode($arrParams));
            return $resultFormatContent;
        }
        /**
         * Gọi qua app transaction đẩy nội dung qua xử lý
         */
        $strCode = $resultFormatContent["data"]["code"];
        $intTotalBalance = $resultFormatContent["data"]["total_balance"];

        $resultUpdateTransaction = $this->transactionService->updateResultTransaction(["received_date" => $strReceivedDate, "content" => $strBody, "code" => $strCode, "amount" => $intAmount, "total_balance" => $intTotalBalance]);

        if ($resultUpdateTransaction["error_code"] != 0) {
            \Log::info("detectCodeTransaction resultUpdateTransaction" . json_encode($resultUpdateTransaction));
        }
        return $resultUpdateTransaction;

    }
    public function index()
    {

        //    dd($yoobil->setSecretKey($objGatewayAccount->secret_key)->setPrivateKey($objGatewayAccount->private_key)->setBusinessId($objGatewayAccount->business_id)->setMerchantId($objGatewayAccount->merchant_id)
        //         ->updateVA(["orderNo" => "WAOei49350", "status" => 1]));



        $objGatewayAccount = GatewayAccount::where('id', 13)->first();

        $pay2pay = Pay2Pay::make([
            'environment' => 'production',              // uat | production
            'tenant' => $objGatewayAccount->tenant,
            'username' => $objGatewayAccount->username,
            'password' => Crypt::decryptString($objGatewayAccount->password),     // mật khẩu GỐC, SDK tự băm
            'private_key' => $objGatewayAccount->private_key,
            'merchant_id' => $objGatewayAccount->merchant_id,
            'merchant_key' => '',
            'secret_key' => $objGatewayAccount->secret_key,
        ]);

        // $qr = $pay2pay->collection()->initializeDynamicQr('DH001', 500000, 'Thanh toan don hang');
        // echo $qr->getData('qrInfo');

      $response = $pay2pay->payout()->banks();

foreach ($response->getData() as $bank) {
    printf("%-12s %-8s %s\n", $bank['bankId'], $bank['binCode'], $bank['shortName']);
}

        dd("done");


        dd(Crypt::decryptString("eyJpdiI6IlFmRTRJa2tPeDlPY1FkUVZrS2Rrd0E9PSIsInZhbHVlIjoiTXRHU0p4Rk5RbjVvSzRoZVJoRmhVZz09IiwibWFjIjoiNTczMTQ1Yzg3YTNkOGQwODE0ZGZlMTY0MzVmNTdmMDU3ZDQ3ZDhjZjhiMzRiYjBkNzNjZDM0ODE0MjUzMzFhNyIsInRhZyI6IiJ9"));

        $yoobill = new Yoobil();
        $yoobill->test1688pays();

        dd("done");
        // 1. Cấu hình thông tin Proxy của bạn
        $proxy_ip = '104.250.122.113'; // Thay bằng IP của Server chạy Docker 3proxy
        $proxy_auth = 'admin:admin@admin123123'; // Thay bằng tài khoản:mật khẩu của bạn

        // Chọn LOẠI PROXY và CỔNG tương ứng bằng cách bỏ comment (bỏ dấu //) dòng bạn muốn test:
// --- Dành cho HTTP Proxy ---
        $proxy_url = "http://" . $proxy_ip . ":22128";
        $proxy_type = CURLPROXY_HTTP;

        // --- Dành cho SOCKS5 Proxy (Nếu muốn test cổng 1080 thì mở 2 dòng dưới này ra) ---
// $proxy_url  = "socks5://" . $proxy_ip . ":1080";
// $proxy_type = CURLPROXY_SOCKS5_HOSTNAME; 


        // 2. Khởi tạo cURL để gọi tới một trang web kiểm tra IP (ví dụ: ifconfig.me)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ifconfig.me");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Giới hạn thời gian chờ 10 giây

        // 3. Cấu hình cURL đi qua Proxy
        curl_setopt($ch, CURLOPT_PROXY, $proxy_url);
        curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy_type);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_auth); // Truyền Username:Password vào đây

        // 4. Thực thi và kiểm tra kết quả
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // 5. Hiển thị kết quả ra màn hình
        echo "--- KẾT QUẢ KIỂM TRA PROXY ---\n";
        if ($error) {
            echo "Lỗi kết nối cURL: " . $error . "\n";
        } else {
            echo "HTTP Status Code: " . $http_code . "\n";
            if ($http_code == 200) {
                echo "Kết nối PROXY THÀNH CÔNG!\n";
                echo "IP hiện tại của bạn sau khi qua proxy là: " . trim($response) . "\n";
            } else {
                echo "Proxy từ chối kết nối (Có thể sai User/Pass hoặc sai Port).\n";
                echo "Phản hồi từ server: " . $response . "\n";
            }
        }

        dd("done");

        $gatewayAccountService = new GatewayAccountService();
        dd($gatewayAccountService->getTransactionCollectionByTransactionId([
            "txnDate" => "20260524222242",
            "auditNumber" => "6c747edaf09a4c53af2d39b630a70a60",
            "gateway_account_id" => 12,
        ]));

        // $userWithdrawService = new UserWithdrawService();
        // dd($userWithdrawService->createBill([
        //     'id' => 10687
        // ]));
        // dd("done");

        print_r(Crypt::encryptString('271089'));
        echo "</br>";
        print_r(Crypt::encryptString('sVAm9@cSn'));
        dd("done");
        $str = "Quỹ phòng kế toán Quý 4/2025";
        dd(General::removeSpecialUnicode($str), strlen(General::removeSpecialUnicode($str)), strlen($str));

        dd("done");
        $objGatewayAccount = GatewayAccount::where('id', 1)->first();
        $yoobil = new Yoobil();
        // dd($objGatewayAccount->gateway_public_key);
        dd($yoobil->setSecretKey($objGatewayAccount->secret_key)->setPrivateKey($objGatewayAccount->private_key)->setBusinessId($objGatewayAccount->business_id)->setMerchantId($objGatewayAccount->merchant_id)
            ->updateVA(["orderNo" => "WAOei49350", "status" => 1]));


        // dd( Crypt::decryptString("eyJpdiI6IlFmRTRJa2tPeDlPY1FkUVZrS2Rrd0E9PSIsInZhbHVlIjoiTXRHU0p4Rk5RbjVvSzRoZVJoRmhVZz09IiwibWFjIjoiNTczMTQ1Yzg3YTNkOGQwODE0ZGZlMTY0MzVmNTdmMDU3ZDQ3ZDhjZjhiMzRiYjBkNzNjZDM0ODE0MjUzMzFhNyIsInRhZyI6IiJ9"));

        // print_r( Crypt::encryptString('271089'));
        print_r(Crypt::encryptString('Waj@Kio9E'));
        dd("done");

        $arrParams["query"]["user_id"] = 4;
        $userVirtualAccountService = new UserVirtualAccountService();
        return response()->json($userVirtualAccountService->getList($arrParams));


        dd(date('N') + 1);
        dd("done");

        //  \App\Jobs\ToolPushBackupWithdrawlJob::dispatch([
        //     'user_withdraw_id' => "1315"
        // ])->onQueue('notification');


        \App\Jobs\ToolPushBackupTransactionJob::dispatch([
            'transaction_id' => "26132"
        ])->onQueue('notification');
        dd("done");
        $toolService = new ToolService();
        dd($toolService->pushWithdrawl(['user_withdraw_id' => 1315]));

        // $WithdrawPaymenthotLogService = new WithdrawPaymenthotLogService();
        // dd($WithdrawPaymenthotLogService->createRequestV2(['user_withdraw_id' => 906]));
        // $arrParams = request(['agree', 'first_name', 'last_name', 'phone', 'email']);
        // dd($arrParams);

        //           $yoobil = new Yoobil();
//         $objUserYoobilConfig = GatewayAccount::where('id', 1)->first();


        //         $reusltTransactions = $yoobil->setBusinessId($objUserYoobilConfig->business_id)
//             ->setMerchantId($objUserYoobilConfig->merchant_id)
//             ->setSecretKey($objUserYoobilConfig->secret_key)
//             ->setPrivateKey($objUserYoobilConfig->private_key)
//             ->getBanks();

        // dd($reusltTransactions);

        dd("done");
        $gpay = new Gpay();
        $strPrivateKey = file_get_contents(base_path("rsa_prod_gpay_private_key.pem")); //NGUYEN THI XUAN
        // dd($gpay->setPrivateKey($strPrivateKey)->fundTransfersFtToBank(["amount" => 10000, "account_number" => "9704542000234196", 'bank_code' => '', 'type' => "CARD_NUMBER", 'transaction_id' => "M" . time()]));
        // dd($gpay->setPrivateKey($strPrivateKey)->fundTransfersFtToBank(["amount" => 100000, "account_number" => "0687041020972", 'bank_code' => 'VCCB', "full_name"=>"NGUYEN THI XUAN", 'type' => "ACCOUNT_NUMBER", 'transaction_id' => "M" . time()]));
        // dd($gpay->setPrivateKey($strPrivateKey)->fundTransfersInquiry(["account_number" => "0687041020972", 'bank_code' => 'VCCB', 'type' => "ACCOUNT_NUMBER",'request_id' => "M" . time()]));
        $resultTOken = ($gpay->createToken());
        dump($resultTOken);
        $strToken = ($resultTOken["data"]["token"] ?? "");

        $arrBank = [
            ['M686262300000011', 'PHAN VUONG TEST'],
            ['963699264700000006', 'GVA NGUYEN VAN TEST'],
            ['963699264700000005', 'GVA NGUYEN VAN TEST'],
            ['963699264700000004', 'GVA BUI THI KIEU'],
            ['M686262300000010', 'LAMPHONGCHINA'],
            ['M686262300000009', 'POST THANG LOI'],
            ['963699264700000003', 'GVA POST THANG LOI'],
            ['963699264700000002', 'GVA LAMPHONGCHINA'],
            ['963699264700000001', 'GVA CONG TY TNHH UNIVERSAL ECOM'],
            ['M686262300000008', '1800HOA'],
            ['M686262300000007', 'FDSHOP'],
            ['M686262300000006', 'CONG TY TNHH UNIVERSAL ECOM'],
            ['M686262300000005', 'SHIP247'],
            ['M686262300000004', 'GIAONHAN247'],
            ['M686262300000003', 'NGUYEN HONG PHI'],
            ['M686262300000002', 'NGUYEN HONG PHI'],
            ['M686262300000001', 'NGUYEN VAN B'],
        ];
        // dd($gpay->setAuthentication($strToken)->setPrivateKey($strPrivateKey)->closeVirtualAccount(["account_name" => "SHIP247", "account_number" => "M686262300000005", 'close_reason' => "HR REQUEST CLOSE"]));
        // dd($gpay->setAuthentication($strToken)->setPrivateKey($strPrivateKey)->createVirtualAccount(["account_name" => "SHIP247", 'bank_code' => "TCB"]));
        foreach ($arrBank as $bank) {
            dump($gpay->setAuthentication($strToken)->setPrivateKey($strPrivateKey)->closeVirtualAccount(["account_name" => $bank[1], "account_number" => $bank[0], 'close_reason' => "ADMIN CLOSE"]));
        }


        dd("done");
        //         $telegram = new Telegram();
//        $rs= $telegram->setToken('8409524834:AAHHiSgv1oEGobkVx3egx_khqBDgEp3nihg')->sendMessage([
//             "chat_id" => "-4969581114",
//             "message" => "test"
//         ]);
// dd($rs);


        \App\Jobs\TelegramNotificationJob::dispatch([
            'message' => "Yêu cầu rút tiền vui lòng xử lý giao dịch",
            'type' => "notification-partner",
            'bot_token' => '8409524834:AAHHiSgv1oEGobkVx3egx_khqBDgEp3nihg',
            'user_id' => 22
        ])->onQueue('notification');


        exit;

        $userWithdrawService = new UserWithdrawService();
        $objUserWithdraw = $userWithdrawService->withdrawIndividual([
            "user_withdraw_id" => 382
        ]);

        dd($objUserWithdraw);

        $yoobil = new Yoobil();
        $objUserYoobilConfig = GatewayAccount::where('id', 1)->first();


        $reusltTransactions = $yoobil->setBusinessId($objUserYoobilConfig->business_id)
            ->setMerchantId($objUserYoobilConfig->merchant_id)
            ->setSecretKey($objUserYoobilConfig->secret_key)
            ->setPrivateKey($objUserYoobilConfig->private_key)
            ->getTransaction([
                "startTime" => (time() - (60 * 60 * 48)) * 1000,
                "endTime" => (time() + (60 * 60 * 48)) * 1000,
                "pageSize" => 1500,
            ]);

        $creditedAmount = 0;
        foreach ($reusltTransactions["data"]["result"]["transactions"] ?? [] as $reusltTransaction) {
            if ($reusltTransaction["creditedStatus"] != 1) {
                continue;
            }
            $creditedAmount += $reusltTransaction["creditedAmount"] ?? 0;
        }

        dd($creditedAmount);



        $gatewayService = new GatewayAccountService();
        // dd($gatewayService->updateBalance(["id" => 6]));
        dd($gatewayService->updateBalance(["id" => 4]));


        $gatewayAccountTransaction = new GatewayAccountTransactionService();
        dd($gatewayAccountTransaction->withDrawal([
            "gateway_account_id" => 10,
            "amount" => 10000,
            "note" => "test"
        ]));
        exit;

        $userTransaction = new UserTransactionService();
        dd($userTransaction->transfer([
            "from_user_id" => 18,
            "to_user_id" => 17,
            "amount" => 10000,
        ]));

        // $gatewayService = new GatewayAccountService();
        // // dd($gatewayService->updateBalance(["id" => 6]));
        // dd($gatewayService->updateBalance(["id" => 7]));
        exit;
        $objGatewayAccount = GatewayAccount::where('id', 2)->first();
        $yoobil = new Yoobil();
        $result = $yoobil->setSecretKey($objGatewayAccount->secret_key)
            ->setPrivateKey($objGatewayAccount->private_key)
            ->setBusinessId($objGatewayAccount->business_id)
            ->setMerchantId($objGatewayAccount->merchant_id)->createVA([
                    "userName" => "TRAN VAN BINH",
                    "returnUrl" => "https://uat.ribato.com",
                    "user_id" => 1
                ]);
        dd($result);



        $gatewayService = new GatewayAccountService();
        // dd($gatewayService->updateBalance(["id" => 6]));
        dd($gatewayService->updateBalance(["id" => 7]));
        $yoobil = new Yoobil();
        $intUserId = 11;
        $objUserYoobilConfig = UserYoobilConfig::where('user_id', $intUserId)->first();


        $getBalance = $yoobil->setBusinessId($objUserYoobilConfig->business_id)
            ->setMerchantId($objUserYoobilConfig->merchant_id)
            ->setSecretKey($objUserYoobilConfig->secret_key)
            ->setPrivateKey($objUserYoobilConfig->private_key)
            ->getBalance();

        dd($getBalance);


        exit;
        //  [accountName] => GIAONHAN247
        //     [accountNo] => 96312420791301
        //     [amount] => 10000
        //     [bankName] => BIDV
        //     [businessId] => 25

        $yoobil = new Yoobil();
        dd($yoobil->createGiaoNhan());

        // $arrTradeNo = [
        //     '0050001952174730480062464',
        //     '0050001952174402053476352',
        //     '0050001952171889266921472',
        //     '0050001952170668758011904',
        //     '0050001952170146466500608',
        //     '0050001952166444804476928',
        //     '0050001952050031183925248',
        //     '0050001952048215574581248'
        // ];

        // foreach ($arrTradeNo as $tradeNo) {
        //     $curl = curl_init();

        //     curl_setopt_array($curl, array(
        //         CURLOPT_URL => 'https://www.yoobil.com/prod-api/trade/va/receipt/merchant?pageNum=1&pageSize=10&tradeNo='.$tradeNo,
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => '',
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 0,
        //         CURLOPT_FOLLOWLOCATION => true,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => 'GET',
        //         CURLOPT_HTTPHEADER => array(
        //             'sec-ch-ua-platform: "macOS"',
        //             'Authorization: Bearer eyJhbGciOiJIUzUxMiJ9.eyJ1c2VyX2lkIjoxMTUsInVzZXJfa2V5IjoiZGI3MjEzODQtMzM1OC00NzJkLTgzZDctMzMzZjUyYzQ1ODUwIiwidXNlcm5hbWUiOiJGcmFuayJ9.rX6nl2B3-pq2NvqqrqAtpROi9oqVJy5TfxG5CWEp3H5zqvvf6cDquhH1-0CGNNcG4GxFx823QiENfIbDrr1kVQ',
        //             'Referer: https://www.yoobil.com/transactions/collectionRecords',
        //             'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
        //             'Accept: application/json, text/plain, */*',
        //             'sec-ch-ua: "Not)A;Brand";v="8", "Chromium";v="138", "Google Chrome";v="138"',
        //             'sec-ch-ua-mobile: ?0'
        //         ),
        //     ));
        //     $response = curl_exec($curl);

        //     curl_close($curl);
        //     $arrResponse = json_decode($response, true);
        //     if (!empty($arrResponse["rows"][0])) {
        //         $row        = $arrResponse["rows"][0];
        //         $arrRequest = [
        //             "code" => 10000,
        //             "msg" => "the request is succeed.",
        //             "result" => [
        //                 "accountName" => $row["accountName"],
        //                 "accountNo" => $row["accountNo"],
        //                 "amount" => $row["amount"],
        //                 "bankName" => "BIDV",
        //                 "businessId" => $row["businessId"],
        //                 "currency" => "VND",
        //                 "expireDate" => null,
        //                 "feeId" => $row["feeId"],
        //                 "merchantId" => $row["merchantId"],
        //                 "orderNo" => $row["orderNo"],
        //                 "purchaseAmount" => $row["amount"],
        //                 "purchaseCurrency" => "VND",
        //                 "purchaseTime" => strtotime($row["payTime"]) * 100,
        //                 "remark" => $row["remark"],
        //                 "sign" => "DtUcwK0PMCaZfxMb5I+02l45QoLmnfzuPq885lsdmF/C6D/XqinJRv76sQV4n/cRvDWp8XoiISxvADk+dUOMHQzEW7o9imcPcB8pC3Zs2HqdoaSnqq/wRpHm+ewumAdIw4OdQtSrUoN9YKGWOFWrrgg/cmhVKrj0AC97Fe+D8TX7VnFwHscgdooblYkIRcf4zUdzqtXMVmk4OXVdgoWIbxa+a/PnQbdsTMqa91qpuM3+Czpj1gnRF8imx2nMqnp/YDqGOArwfK+yfP7OGzkmxwL/vGG1ivu6hag4QcQ9Od2wHCni9Oubw0J5i0mt7uGLQhRimr/4zbng5wShEzDuDg==",
        //                 "tradeNo" => $row["tradeNo"],
        //                 "userName" => $row["accountName"],
        //             ]
        //         ];


        //         $curl = curl_init();

        //         curl_setopt_array($curl, array(
        //             CURLOPT_URL => 'https://uat.ribato.com/api/app-message/yoobil-forward?token=2c9057da4fd75e16abe8ef2e037a5c04&device=yoobil',
        //             CURLOPT_RETURNTRANSFER => true,
        //             CURLOPT_ENCODING => '',
        //             CURLOPT_MAXREDIRS => 10,
        //             CURLOPT_TIMEOUT => 0,
        //             CURLOPT_FOLLOWLOCATION => true,
        //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //             CURLOPT_CUSTOMREQUEST => 'POST',
        //             CURLOPT_POSTFIELDS => json_encode($arrRequest),
        //             CURLOPT_HTTPHEADER => array(
        //                 'Content-Type: application/json'
        //             ),
        //         ));

        //         $response = curl_exec($curl);

        //         curl_close($curl);

        //         $arrResponse = json_decode($response, true);
        //         dump($arrResponse);
        //     }
        // }
        // exit;


        // $privateKeyPath = base_path('app/Utilities/Yoobil/rsa_private_key.pem');
        // $publicKeyPath  = base_path('app/Utilities/Yoobil/rsa_public_key.pem');
        // $secretKey      = 'j0D03AlJx67VPSr122581v8m75y68o0O8792EG14';

        // $businessId = 19;
        // $merchantId = 215;


        // $yoobil = new Yoobil();
        // $result = $yoobil->setSecretKey($secretKey)->setPrivateKey(file_get_contents($privateKeyPath))->setBusinessId($businessId)->setMerchantId($merchantId)->getTransaction(["tradeNo" => "0050001952940460448288768"]);
        // dd($result);
        exit;

        // dd("");
        // dd(  $this->transactionService->callbackResultTransaction([
        //     "transaction_id" => 14043
        // ]));

        // dd($this->detectCodeTransaction([
        //     "amount" => 100000,
        //     "content" => "FPA9E3672027990",
        //     "received_date" => date('Y-m-d H:i:s')
        // ]));
        // dd("");
        // $wdGpayLogSv = new WithdrawGpayLogService();
        // dd($wdGpayLogSv->createRequest(["user_withdraw_id"=> 788]));
        //https://vnpay.biz/api/app-message/yoobil-forward?user_businesse_id=2&token=b6137e526ce2d05a43c09b1208ab5461
        // $tran = new TransactionService();
        // dd($tran->forControl('20231206'));
        // dd(date('d-m-Y H:i:s'));

        $strPrivateKey = file_get_contents(base_path("rsa_prod_gpay_private_key.pem")); //NGUYEN THI XUAN
        // dd($gpay->setPrivateKey($strPrivateKey)->fundTransfersFtToBank(["amount" => 10000, "account_number" => "9704542000234196", 'bank_code' => '', 'type' => "CARD_NUMBER", 'transaction_id' => "M" . time()]));
        // dd($gpay->setPrivateKey($strPrivateKey)->fundTransfersFtToBank(["amount" => 100000, "account_number" => "0687041020972", 'bank_code' => 'VCCB', "full_name"=>"NGUYEN THI XUAN", 'type' => "ACCOUNT_NUMBER", 'transaction_id' => "M" . time()]));
        // dd($gpay->setPrivateKey($strPrivateKey)->fundTransfersInquiry(["account_number" => "0687041020972", 'bank_code' => 'VCCB', 'type' => "ACCOUNT_NUMBER",'request_id' => "M" . time()]));
        $resultTOken = ($gpay->createToken());
        dump($resultTOken);
        $strToken = ($resultTOken["data"]["token"] ?? "");

        // dd($gpay->setAuthentication($strToken)->setPrivateKey($strPrivateKey)->closeVirtualAccount(["account_name" => "SHIP247", "account_number" => "M686262300000005", 'close_reason' => "HR REQUEST CLOSE"]));
        // dd($gpay->setAuthentication($strToken)->setPrivateKey($strPrivateKey)->createVirtualAccount(["account_name" => "SHIP247", 'bank_code' => "TCB"]));
        dd($gpay->setAuthentication($strToken)->setPrivateKey($strPrivateKey)->createVirtualAccount(["account_name" => "LAMPHONGCHINA", 'bank_code' => "TCB", 'account_type' => "M"]));
        // dd($gpay->setAuthentication($strToken)->setPrivateKey($strPrivateKey)->updateVirtualAccount(["account_name" => "HOANG THIEN", 'account_number' => "M123691100000013", 'account_type' => "O", "equal_amount" => 100000]));

        // dd($gpay->setAuthentication($strToken)->setPrivateKey($strPrivateKey)->reOpenVirtualAccount(["account_name" => "NGUYEN HONG PHI", "account_number" => "M123691100000009", 'account_type' => "O", "equal_amount" => 1000]));
        dd($gpay->createToken());
        return request()->getContent();
    }

    public function test()
    {



        // $ts = new TransactionService();
        // dd($ts->formatContentToTransactionCode([
        //     "content"=>"MBVCB.4738169182.068043.FXFKKK3M7U.CT tu 1037310220 TAN TUAN ANH toi 9631242000000940962 NGUYEN HOAN"
        // ]));
        \Log::info("DEBBUG CALLBACK" . json_encode(request()->all()));
        \Log::info("DEBBUG CALLBACK" . json_encode(request()->getContent()));
    }


    public function callback()
    {

        // openssl genpkey -out paymenthot_m89user1_rsa_private_key.pem -outform PEM -algorithm RSA -pkeyopt rsa_keygen_bits:2048
        //openssl rsa -in paymenthot_m85user1_rsa_private_key.pem -pubout -out paymenthot_m85user1_rsa_public_key.pem



        //https://vnpay.biz/api/app-message/yoobil-forward?user_businesse_id=2&token=b6137e526ce2d05a43c09b1208ab5461
        // $tran = new TransactionService();
        // dd($tran->forControl('20231206'));
        // dd(date('d-m-Y H:i:s'));
        \Log::info("DEBBUG CALLBACK" . json_encode(request()->all()));
        \Log::info("DEBBUG CALLBACK" . json_encode(request()->getContent()));
        return "test callback payout";
    }
}