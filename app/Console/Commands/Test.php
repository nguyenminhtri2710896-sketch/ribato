<?php

namespace App\Console\Commands;

use App\Jobs\PayoutCallbackResultJob;
use App\Jobs\TransactionCallbackResultJob;
use App\Jobs\WithdrawPaymenthotWebJob;
use App\Models\Bank;
use App\Models\BankYoobilMapping;
use App\Models\BotProducts;
use App\Models\CategoryMapping;
use App\Models\GatewayForward;
use App\Models\ModemPortQueue;
use App\Models\PackageData;
use App\Models\PaymenthotAccount;
use App\Models\Products;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserIdQrcode;
use App\Models\UserToken;
use App\Models\UserYoobilConfig;
use App\Services\DigishopService;
use App\Services\ModemPortQueueService;
use App\Services\ModemPortService;
use App\Services\SettingService;
use App\Services\TransactionService;
use App\Services\UserBankingCallbackConfigServices;
use App\Services\UserBankingTranferServices;
use App\Services\UserService;
use App\Services\UserTokenService;
use App\Services\WithdrawPaymenthotLogService;
use App\Services\WithdrawYoobilLogService;
use App\Utilities\BuildQrPayment;
use App\Utilities\Digishop;
use App\Utilities\General;
use App\Utilities\Neox;
use App\Utilities\Paymenthot;
use App\Utilities\Telegram;
use App\Utilities\Yoobil;
use Curl\Curl;
use Illuminate\Console\Command;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use tttran\viet_qr_generator\Generator;

class Test extends Command
{

    use PrependsOutput,
        PrependsTimestamp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {--type=default} {--id=default} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test {--type=default} {--id=default}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->option('type');
        switch ($type) {
            case 'test':
                $this->test();
                break;
            case 'test1':
                $this->test1();
                break;
            case 'callback':
                $this->callback();
                break;
            case 'check':
                $this->check();
                break;
            default:
                $this->error("Không tìm thấy --type=$type");
        }
    }

    function check()
    {

        //  $objTransaction = \App\Models\Transaction::where('ref_code', '202511180424546')->first();
        //  dd($objTransaction);

        $strToken = "eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJfWnVXT3VnUDR5RUVEb3FSWkgwRFFWdnJVcnBlck1qWEFTcE1yYnhJZExFIn0.eyJleHAiOjE3Njc3NzUyNzAsImlhdCI6MTc2Nzc3NDY3MCwianRpIjoiYmFkMzAzZjAtZmVlZS00Yjg5LWI1MDYtMDE5OWQ0ZThmODMzIiwiaXNzIjoiaHR0cHM6Ly9pZC5wcm9kLnBheW1lbnRob3QuY29tL3JlYWxtcy9wYXlwYXkiLCJhdWQiOiJhY2NvdW50Iiwic3ViIjoiZmFjYzZkYmEtNDJjZS00YzcxLTlmYTctNDg3MjI5MDhlYTc4IiwidHlwIjoiQmVhcmVyIiwiYXpwIjoicGF5cGF5LWNsaWVudCIsInNlc3Npb25fc3RhdGUiOiI1NDE0NzY1Mi1jNjFkLTQ1MjEtODQ2NS0zNmM5N2Y1ZDI0YTUiLCJhY3IiOiIxIiwiYWxsb3dlZC1vcmlnaW5zIjpbIi8qIl0sInJlYWxtX2FjY2VzcyI6eyJyb2xlcyI6WyJvZmZsaW5lX2FjY2VzcyIsInVtYV9hdXRob3JpemF0aW9uIiwiZGVmYXVsdC1yb2xlcy13YWxsZXQiXX0sInJlc291cmNlX2FjY2VzcyI6eyJhY2NvdW50Ijp7InJvbGVzIjpbIm1hbmFnZS1hY2NvdW50IiwibWFuYWdlLWFjY291bnQtbGlua3MiLCJ2aWV3LXByb2ZpbGUiXX19LCJzY29wZSI6ImVtYWlsIHByb2ZpbGUiLCJzaWQiOiI1NDE0NzY1Mi1jNjFkLTQ1MjEtODQ2NS0zNmM5N2Y1ZDI0YTUiLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibWVyY2hhbnRJZCI6IlBQMDAwMDEyMCIsImFwcElkIjpbImFwaS5wYXltZW50aG90LmNvbSJdLCJncm91cHMiOlsiL21lcmNoYW50Il0sInByZWZlcnJlZF91c2VybmFtZSI6Im0xMjB1c2VyMSIsImVtYWlsIjoiYWRtaW5AcmliYXRvLmNvbSJ9.eWoW0tINzyWkVOBcUdfsDIfwM0amkSOXqvfGpoDagXiCyfveO-JMNIRIUAeJ80NWF3CT3_1oZVakak8lCsCVOlb4RKIoavan7eOAGBpnHNWyReGySjUt5bSmeuEu7SjZGGXPGuJh0fIqS--L2q6Yl7fre8mvtYBfil7rtP5fRUzUpVCWPJ4jm1jUthZph62phixrYnplVOImEXXyyg85LvLZUnxzVKqPxW2F3hWICSpapVUJiB6nYsw-SEZ-HM0t2LWVWUggZ2dd1VSn-MOk9IqZt7KugrqNIAEJcKi7QyvOb9KtHCjIqRL5YC2j1iLRsLz7VibOORpVG0-lNf6XBw";
        for ($i = 200; $i < 300; $i++) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://merchant.paymenthot.com/api/transaction/search-transactions',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{"pageRequest":{"pageSize":20,"pageNumber":0,"searchAfter":null},"sortRequest":[{"direction":"DESC","property":"createdAt"}],"fromDate":"20260106","toDate":"20260107","status":["SUCCESS"]}',
                CURLOPT_HTTPHEADER => array(
                    'accept: application/json, text/plain, */*',
                    'accept-language: en-US,en;q=0.9,vi;q=0.8',
                    'authorization: Bearer ' . $strToken,
                    'content-type: application/json',
                    'open-search: true',
                    'origin: https://merchant.paymenthot.com',
                    'p-lang: en',
                    'priority: u=1, i',
                    'referer: https://merchant.paymenthot.com/management/transactions?from=20251118&to=20251118&page=2',
                    'sec-ch-ua: "Chromium";v="142", "Google Chrome";v="142", "Not_A Brand";v="99"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "macOS"',
                    'sec-fetch-dest: empty',
                    'sec-fetch-mode: cors',
                    'sec-fetch-site: same-origin',
                    'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36',
                    'Cookie: _ga=GA1.1.406885620.1759984283; _ga_08E8B8QGHB=GS2.1.s1760952457$o4$g1$t1760954001$j59$l0$h0; _ga_WWPNKWFZGS=GS2.1.s1760952457$o4$g1$t1760954001$j59$l0$h0'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $arrResult = json_decode($response, true);
            foreach ($arrResult["data"]["content"] ?? [] as $row) {
                $objTransaction = \App\Models\Transaction::where('ref_code', $row['txnId'])->first();
                if ($objTransaction) {
                    $this->info("Tồn tại giao dịch " . $row['txnId']);
                    continue;
                }

                $merchantOrderId = $row['merchantOrderId'];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://merchant.paymenthot.com/api/ipn/retry',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{"orderId":"' . $merchantOrderId . '"}',
                    CURLOPT_HTTPHEADER => array(
                        'accept: application/json, text/plain, */*',
                        'accept-language: en-US,en;q=0.9,vi;q=0.8',
                        'authorization: Bearer ' . $strToken,
                        'content-type: application/json',
                        'open-search: true',
                        'origin: https://merchant.paymenthot.com',
                        'p-lang: en',
                        'priority: u=1, i',
                        'referer: https://merchant.paymenthot.com/management/ipn?orderId=00e7ed1572524a5f9d7c2e065cb1e5c3',
                        'sec-ch-ua: "Chromium";v="142", "Google Chrome";v="142", "Not_A Brand";v="99"',
                        'sec-ch-ua-mobile: ?0',
                        'sec-ch-ua-platform: "macOS"',
                        'sec-fetch-dest: empty',
                        'sec-fetch-mode: cors',
                        'sec-fetch-site: same-origin',
                        'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36',
                        'Cookie: _ga=GA1.1.406885620.1759984283; _ga_08E8B8QGHB=GS2.1.s1760952457$o4$g1$t1760954001$j59$l0$h0; _ga_WWPNKWFZGS=GS2.1.s1760952457$o4$g1$t1760954001$j59$l0$h0'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                $arrResultIPN = json_decode($response, true);
                dump($merchantOrderId, $arrResultIPN);
            }
        }
    }


    function callback()
    {
        $id = $this->option('id');
        dispatch(new TransactionCallbackResultJob([
            'id' => $id,
        ]))->onQueue('callback');
        dump($id);

    }

    function SHA256withRSA($data, $privateKeyPath)
    {
        $data .= VNPAY_API_TOKEN;
        // Load the private key 
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));

        // Check if private key is loaded successfully 
        if ($privateKey === false) {
            die('Unable to load private key');
        }

        // Sign the data using SHA256withRSA 
        $signature = null;
        $success = openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        // Check if the signing operation was successful 
        if ($success === false) {
            die('Unable to sign data');
        }

        // Free the private key from memory 
        openssl_free_key($privateKey);

        // Base64 encode the signature and return 
        return base64_encode($signature);
    }
    public function test()
    {



        for ($i = 0; $i <= 22; $i++) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://merchant.paymenthot.com/api/transaction/search-transactions',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{"pageRequest":{"pageSize":20,"pageNumber":' . $i . ',"searchAfter":null},"sortRequest":[{"direction":"DESC","property":"createdAt"}],"fromDate":"20260805","toDate":"20260807","status":["SUCCESS","FAIL","CANCELED","PENDING"],"type":["PAYMENT","REFUND"]}',
                CURLOPT_HTTPHEADER => array(
                    'accept: application/json, text/plain, */*',
                    'accept-language: en-US,en;q=0.9',
                    'authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJfWnVXT3VnUDR5RUVEb3FSWkgwRFFWdnJVcnBlck1qWEFTcE1yYnhJZExFIn0.eyJleHAiOjE3ODYwODgzNzgsImlhdCI6MTc4NjA4Nzc3OCwianRpIjoiNmY4YjNmNWQtMjM2Zi00NWM3LWI4MmEtMjU0NThkZjAxOTFhIiwiaXNzIjoiaHR0cHM6Ly9pZC5wcm9kLnBheW1lbnRob3QuY29tL3JlYWxtcy9wYXlwYXkiLCJhdWQiOiJhY2NvdW50Iiwic3ViIjoiYjY0NWMzYWUtMThiOC00YjgwLWJiODYtMmE2ZTc1MjcxZmUxIiwidHlwIjoiQmVhcmVyIiwiYXpwIjoicGF5cGF5LWNsaWVudCIsInNlc3Npb25fc3RhdGUiOiI5YzUxMzU3Mi1kM2JlLTRkOTEtYjNjYS04NWZlYWIyMjc4NjYiLCJhY3IiOiIxIiwiYWxsb3dlZC1vcmlnaW5zIjpbIi8qIl0sInJlYWxtX2FjY2VzcyI6eyJyb2xlcyI6WyJvZmZsaW5lX2FjY2VzcyIsInVtYV9hdXRob3JpemF0aW9uIiwiZGVmYXVsdC1yb2xlcy13YWxsZXQiXX0sInJlc291cmNlX2FjY2VzcyI6eyJhY2NvdW50Ijp7InJvbGVzIjpbIm1hbmFnZS1hY2NvdW50IiwibWFuYWdlLWFjY291bnQtbGlua3MiLCJ2aWV3LXByb2ZpbGUiXX19LCJzY29wZSI6ImVtYWlsIHByb2ZpbGUiLCJzaWQiOiI5YzUxMzU3Mi1kM2JlLTRkOTEtYjNjYS04NWZlYWIyMjc4NjYiLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibWVyY2hhbnRJZCI6IlBQMDAwMDE2MSIsImFwcElkIjpbImFwaS5wYXltZW50aG90LmNvbSJdLCJncm91cHMiOlsiL21lcmNoYW50Il0sInByZWZlcnJlZF91c2VybmFtZSI6Im0xNjF1c2VyMSIsImVtYWlsIjoiaW5mb0AxOTAwcGF5LmNvbSJ9.e95J9cFNczr21WIfhsiZmXpVizZkuWEfZOsDEwVSnZLMtlClZ8uXV0eGnfYDwRsb6T8NQUE0YnntyCYS9Ep-IcgEA5GAwMOIVAsEjxdUa42YfoMiyNpDXFlgUEAYG6ZvfaNTF7Y2gtc_-vv5hW_WxpicUQ7jhp0S2Spg7S4pZpN3ku4GQkAXDjNYccGQAPRrhM5aP1W4uuNoJeQnX8NmyQRzY3-MD29mXVvUYWYOsF6F474NYXx3vpqqbZJ4Y_3MGx9d0C5Q3jfpCWydK9rLgdsXM56a2lK93ILPsRmQD_w2jEl94qV2c9Z0MKaFkD9iKZ9CHZuyejBhFDoOAhFxoA',
                    'content-type: application/json',
                    'open-search: true',
                    'origin: https://merchant.paymenthot.com',
                    'p-lang: en',
                    'priority: u=1, i',
                    'referer: https://merchant.paymenthot.com/management/transactions?from=20260805&to=20260807',
                    'sec-ch-ua: "Not;A=Brand";v="8", "Chromium";v="150", "Google Chrome";v="150"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "macOS"',
                    'sec-fetch-dest: empty',
                    'sec-fetch-mode: cors',
                    'sec-fetch-site: same-origin',
                    'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',
                    'Cookie: merchant_status=ACTIVE; is_auth=1'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $arrResponse = json_decode($response, true);
            foreach ($arrResponse["data"]["content"] as $row) {
                $objTransaction = Transaction::where('ref_code', $row["txnId"])->first();
                if (empty($objTransaction)) {

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://merchant.paymenthot.com/api/ipn/retry',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => '{"txnId":' . $row["txnId"] . '}',
                        CURLOPT_HTTPHEADER => array(
                            'accept: application/json, text/plain, */*',
                            'accept-language: en-US,en;q=0.9',
                            'authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJfWnVXT3VnUDR5RUVEb3FSWkgwRFFWdnJVcnBlck1qWEFTcE1yYnhJZExFIn0.eyJleHAiOjE3ODYwODgzNzgsImlhdCI6MTc4NjA4Nzc3OCwianRpIjoiNmY4YjNmNWQtMjM2Zi00NWM3LWI4MmEtMjU0NThkZjAxOTFhIiwiaXNzIjoiaHR0cHM6Ly9pZC5wcm9kLnBheW1lbnRob3QuY29tL3JlYWxtcy9wYXlwYXkiLCJhdWQiOiJhY2NvdW50Iiwic3ViIjoiYjY0NWMzYWUtMThiOC00YjgwLWJiODYtMmE2ZTc1MjcxZmUxIiwidHlwIjoiQmVhcmVyIiwiYXpwIjoicGF5cGF5LWNsaWVudCIsInNlc3Npb25fc3RhdGUiOiI5YzUxMzU3Mi1kM2JlLTRkOTEtYjNjYS04NWZlYWIyMjc4NjYiLCJhY3IiOiIxIiwiYWxsb3dlZC1vcmlnaW5zIjpbIi8qIl0sInJlYWxtX2FjY2VzcyI6eyJyb2xlcyI6WyJvZmZsaW5lX2FjY2VzcyIsInVtYV9hdXRob3JpemF0aW9uIiwiZGVmYXVsdC1yb2xlcy13YWxsZXQiXX0sInJlc291cmNlX2FjY2VzcyI6eyJhY2NvdW50Ijp7InJvbGVzIjpbIm1hbmFnZS1hY2NvdW50IiwibWFuYWdlLWFjY291bnQtbGlua3MiLCJ2aWV3LXByb2ZpbGUiXX19LCJzY29wZSI6ImVtYWlsIHByb2ZpbGUiLCJzaWQiOiI5YzUxMzU3Mi1kM2JlLTRkOTEtYjNjYS04NWZlYWIyMjc4NjYiLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibWVyY2hhbnRJZCI6IlBQMDAwMDE2MSIsImFwcElkIjpbImFwaS5wYXltZW50aG90LmNvbSJdLCJncm91cHMiOlsiL21lcmNoYW50Il0sInByZWZlcnJlZF91c2VybmFtZSI6Im0xNjF1c2VyMSIsImVtYWlsIjoiaW5mb0AxOTAwcGF5LmNvbSJ9.e95J9cFNczr21WIfhsiZmXpVizZkuWEfZOsDEwVSnZLMtlClZ8uXV0eGnfYDwRsb6T8NQUE0YnntyCYS9Ep-IcgEA5GAwMOIVAsEjxdUa42YfoMiyNpDXFlgUEAYG6ZvfaNTF7Y2gtc_-vv5hW_WxpicUQ7jhp0S2Spg7S4pZpN3ku4GQkAXDjNYccGQAPRrhM5aP1W4uuNoJeQnX8NmyQRzY3-MD29mXVvUYWYOsF6F474NYXx3vpqqbZJ4Y_3MGx9d0C5Q3jfpCWydK9rLgdsXM56a2lK93ILPsRmQD_w2jEl94qV2c9Z0MKaFkD9iKZ9CHZuyejBhFDoOAhFxoA',
                            'content-type: application/json',
                            'open-search: true',
                            'origin: https://merchant.paymenthot.com',
                            'p-lang: en',
                            'priority: u=1, i',
                            'referer: https://merchant.paymenthot.com/management/ipn?txnId=' . $row["txnId"] . '',
                            'sec-ch-ua: "Not;A=Brand";v="8", "Chromium";v="150", "Google Chrome";v="150"',
                            'sec-ch-ua-mobile: ?0',
                            'sec-ch-ua-platform: "macOS"',
                            'sec-fetch-dest: empty',
                            'sec-fetch-mode: cors',
                            'sec-fetch-site: same-origin',
                            'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',
                            'Cookie: merchant_status=ACTIVE; is_auth=1'
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                    $arrResponse1 = json_decode($response, true);
                    dump($arrResponse1);


                    $this->error("Không tồn tại giao dịch " . $row["txnId"]);
                } else {
                    $this->info("Tồn tại giao dịch " . $row["txnId"]);
                }
            }

        }

        // $objPaymenthotAccount = \App\Models\GatewayAccount::where('id',12)->first();

        // $paymenthot = new Paymenthot();
        // $resultLogin = $paymenthot->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPassword($objPaymenthotAccount->password)->setPrivateKey($objPaymenthotAccount->private_key)->login();
        // dd($resultLogin);



        $objTransactions = Transaction::where('content', '')
            // ->where('id', '>=', 220005)
            ->where('created_at', '>=', '2026-08-06 00:00:00')
            ->get();

        foreach ($objTransactions as $num => $objTransaction) {

            if (!empty($arrResponse["data"]["content"]) && count($arrResponse["data"]["content"]) == 1) {
                $data = $arrResponse["data"]["content"][0];
                if ($data["orgAmount"] == $objTransaction->amount) {
                    $objTransaction->content = $data["providerData"]["remark"] ?? "";
                    $objTransaction->save();
                    dump("$num ID: $objTransaction->id Hợp lệ " . $objTransaction->content);
                } else {
                    $this->error("Không hợp lệ");
                }
            } else {
                $this->error("Nhiều hơn 2 kết quả " . $objTransaction->ref_code);
                dd($arrResponse);

            }

        }

        dd("done");
        die();
        $yoobil = new Yoobil();
        $intUserId = 11;
        $objUserYoobilConfig = UserYoobilConfig::where('user_id', $intUserId)->first();
        $getBank = $yoobil->setBusinessId($objUserYoobilConfig->business_id)
            ->setMerchantId($objUserYoobilConfig->merchant_id)
            ->setSecretKey($objUserYoobilConfig->secret_key)
            ->setPrivateKey($objUserYoobilConfig->private_key)
            ->getBanks();

        dd($getBank);

        dd("done");
        exit;
        // $transactionService = new TransactionService();
        // $resultTransaction  = $transactionService->callbackResultTransaction([
        //     "transaction_id" => 35668
        // ]);

        // dd($resultTransaction);

        // $arrTest = [
        //     35670,
        //     35668,
        //     35666,
        //     35665,
        //     35664,
        //     35663,
        //     35661,
        //     35656,
        //     35655,
        //     35654,
        //     35653,
        //     35650,
        //     35647,
        //     35645,
        //     35643,
        //     35640,
        //     35639,
        //     35637,
        //     35627,
        //     35625,
        //     35620,
        //     35619,
        //     35615,
        //     35614,
        //     35611,
        //     35602,
        //     35599,
        //     35584,
        //     35580,
        //     35579,
        //     35013
        // ];
        // foreach ($arrTest as $id) {
        //     dump($id);
        //     dispatch(new TransactionCallbackResultJob([
        //         'id' => $id,
        //     ]))->onQueue('request');
        // }
        // exit;

        // $GatewayForward = GatewayForward::where("gateway_souce_code", "gpay")->where("bank_account_number", "963699264700000003")->first();
        // dd($GatewayForward);
        //openssl genpkey -out paymenthot_m95user1_rsa_private_key.pem -outform PEM -algorithm RSA -pkeyopt rsa_keygen_bits:2048
//openssl rsa -in paymenthot_m95user1_rsa_private_key.pem -pubout -out paymenthot_m95user1_rsa_public_key.pem

        //paymenthot_m95user1_rsa_private_key
//paymenthot_m95user1_rsa_public_key
//         dd(General::httpBuildQuery(json_decode('{
//     "limit": 1,
//     "page": 1,
//     "query": {
//         "napas_code": "970441"
//     }
// }', true)));

        dd(General::getSignDebug("amount=10000&bank_account_name=Vuong%20Dinh%20Thi%20Huong&bank_account_number=03201010482382&bank_id=32&remark=CK", "a668f99b9a4670bb01f15ef0405e6ccf882a8c8789c238a57bd16d2a24d345a0", "-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCU0064w4lyAy3b
0f30NeAvqjov0gOlEeF+C/S+CLWXFXygngLQ6/LWp0D7Cd23JHGQs29hGB0+8CWM
k219p/K4oL2v46og4m7vOyUhgTAm0hKKMciXnPbNLhjThZ7YfhyLd/Uu9AHDZIcV
2NqpnBM/U4sr/0yC5fzOSqtY48VRj/6WSYB8WYaDGbcEQOeFjDzEjAeLIx3oTY/h
qH7AjnpmmLk6an7dTXOh2gFA4NwvS0nYNyzKd1J+XwRQRkh2yNob3WAMwnDIwxZz
he7T2GC+R4qE+JgHZ64/+HIsGr/ByjSw5qCYXToUruPsRSnv6Wj8ib/bnPa71+/2
QL4AXwUFAgMBAAECggEASkfvnkOmifvxOyrkJKxxUYkW0sBxZkX29Ok1xlXlgNvO
IQLM6AckZSQEyGfDvIHZlv4gZOdazYuiSjhZaWLWwHUmSDCLPS+XbBtqrH7lEDNA
4q33b0j+XCmaZZTnVCoZyDje7QkICkTWMb7TYN2QM8bYi2S5BQqStEpLnKnO1iaU
Scd8oATT9a2LDx0T3zqcvAkmw/rIBu60iF31rBf3DHH5EikImh1k7HWRlsJzqSet
2wo7n9R2m63SIEwVR87lahyg/997gNgUbc7zishJ42aZAeaNc1DdbaEiem6HL85B
D/keqEyYJ4Kj9z95O/Ijrw8Pp3/IpK+meewjsKqi/wKBgQDLXqbtpYXETFhaN49Q
6uiaj5n78CPJ7lUR6J7tYhml8C7jvzs1Uy5kY5E9G2zJHCtrhgJTGUQVbmG+sVo2
TFnrvTgnuh8dewURAiquGVJt9NmrjXnYmdhc83MabhQ0vpuUxASVKGtUtw2D9tZq
mxkk2H4hUe/HJAXH2xD69sDRswKBgQC7VxEks1bpLD6imbxsgBy/r+SA629eSy2B
26NgYlhNXOdU0x+U9bRlQDal6TbuxJnqIBImxrO451Ym7+cHvvhNomyUqBzCGdyM
nEzIhw8rFldzFAhylU2KQx7+jNNt0GXuCIQX+LzXoD+2Il4N3kFCo/mZBBNLLUW0
sDF3lBvCZwKBgB1pVO7tj0jyWZzCiEGFl5oyxmw/MI3VoAv0/ncKRvdOoxlg4Kk3
nG/8TlbuDm+PWWA1g0SNVOFck1pGv+s2Y5LKTMLCEhnrXrI1BAMlulwYmxpKXaLL
rRTUSIM8BEQUhN+g8aC4tb0MtDGvkxbb02kWvjcHaIv+U6+xqOUCpqQtAoGBAIDB
HEUvBMKI6Bt0sa2Ydym/R7rbFNWnsYUYYTbzIGE7Qrpnx8LnA6667VQl7t03PdlN
ZWxFEGw6glmMdCo8tAcXZKzexbUZdR0mpxcCzAf++Odck1m9B2fJLdohC4bjvcpV
DAQ1rgQ8XM1e3WmGoS+d5wOla2U1njzLA5R9tH5tAoGABQGB+PnM2XxmdiS3uol8
J6PWGB2wCNCGWW/+aWEPpX6GySokvrAL3R/D3X4Jc7wFi01aOZETDsYRGcDUZ496
Qvcwgy23s0euihMFPA0TWkqOpvnRbMdd1WImxQ9xAZgfDVhEvvB8sfSUqSd5ruUz
evZL0/AJiIt/GrrCI4OaPkQ=
-----END PRIVATE KEY-----
"));




        $appTransaction = new TransactionService();
        dd($appTransaction->callbackResultTransaction(["transaction_id" => 26396]));



        $objUser = User::find(910);
        $intUserId = $objUser->id;
        $strTradeNo = date('YmdHis') . rand(1000, 9999);

        $objUserToken = UserToken::where('user_id', $intUserId)->first();
        $intUserTokenId = $objUserToken->id;
        $intAmount = 940300;
        $receiverBankRefName = "GIAONHAN247";
        $receiverBankRefNumber = "96311300002854328";
        $strBody = "605958 chuyen khoan";
        $transactionService = new TransactionService();
        $resultCreatePayment = $transactionService->createPayment([
            "user_id" => $intUserId,
            'ref_code' => $strTradeNo,
            'user_token_id' => $intUserTokenId,
            'amount' => $intAmount,
            "bank_account_name" => $receiverBankRefName,
            "bank_account_number" => $receiverBankRefNumber,
        ]);


        if ($resultCreatePayment["error_code"] != 0) {
            dd($resultCreatePayment);
        }
        /**
         * Gọi qua app transaction đẩy nội dung qua xử lý
         * 
         *       "user_id_qrcode_id" => $userIdQrcodeId,
         *       "user_id_qrcode_name" => $userIdQrcodeName,
         *       Không cần vì nó không có ra 
         */
        $strCode = $resultCreatePayment["data"]["code"];
        $resultUpdateTransaction = $transactionService->updateResultTransaction([
            "received_date" => date('Y-m-d H:i:s'),
            "content" => $strBody,
            "code" => $strCode,
            "amount" => $intAmount,
            "total_balance" => 0
        ]);

        dd($resultUpdateTransaction);



        $withdrawGpayLogService = new WithdrawPaymenthotLogService();
        $resultCreateRequest = $withdrawGpayLogService->createRequest([
            "user_withdraw_id" => 38
        ]);

        dd($resultCreateRequest);


        $strBody = "MBVCB.9809523218.066663.FFF3.CT tu 0441003986682 NGUYEN MINH TRI toi 96311300008896825 WEALIFY tai BIDV";

        $arrCodeTemp = explode(" ", $strBody);
        $arrCodeTemp = array_merge(explode(".", $strBody), $arrCodeTemp);
        $arrCode = [];
        foreach ($arrCodeTemp as $key) {
            if (strlen($key) < 4 || strlen($key) > 10) {
                continue;
            }
            $arrCode[] = $key;
        }


        if (!empty($arrCode)) {
            $objUserIdQrcode = UserIdQrcode::whereIn('code', $arrCode)->first();
            dd($objUserIdQrcode, $arrCode);
        }



        dd($arrCode);

        $userService = new UserService();
        $objUsers = User::get();
        foreach ($objUsers as $objUser) {
            $objUser->payment_code = $userService->createPaymentCode();
            $objUser->save();
        }

        dd("Done");


        dispatch(new PayoutCallbackResultJob([
            'id' => 27,
        ]))->onQueue('request');

        dd("done");



        $withdrawGpayLogService = new WithdrawPaymenthotLogService();
        $resultCreateRequest = $withdrawGpayLogService->createRequest([
            "user_withdraw_id" => 8
        ]);

        dd($resultCreateRequest);

        dispatch(new WithdrawPaymenthotWebJob([
            'id' => 8,
        ]))->onQueue('request');

        dd("done");



        // $test = new Generator()->create()
        //     ->bankId("VCB")
        //     ->accountNo("0441003986682")// Account number
        //     ->amount(10000)// Money
        //     ->info("toto") // Ref
        //     ->generate();
        // dd($test);




        // $userService = new UserService();
        // $objUsers    = User::get();
        // foreach ($objUsers as $objUser) {
        //     $objUser->payment_code = $userService->createPaymentCode();
        //     $objUser->save();
        // }

        // dd("Done");

        $transactionService = new TransactionService();
        dd($transactionService->createQrPayment(["user_id" => 1, "amount" => 10000]));



        $userService = new UserService();
        dd($userService->createQrPayment(["user_id" => 1]));


        $appTransaction = new TransactionService();
        dd($appTransaction->callbackResultTransaction(["transaction_id" => 26396]));

        $settingService = new SettingService();
        dd($settingService->authy2Factor(['user_id' => 1]));

        exit;


        foreach ($arr as $item) {
            $objTransaction = \App\Models\Transaction::where('ref_code', $item['tran_code'])->first();
            if ($objTransaction) {
                $objTransaction->fee = $item['fee'];
                $objTransaction->amount_after_fee = $objTransaction->amount - $item['fee'];
                $objTransaction->received_amount = $objTransaction->amount - $item['fee'];
                if ($objTransaction->save()) {
                    dump($objTransaction->amount_after_fee);
                }
            }
            if (!$objTransaction) {
                // $arrIsert = [
                //     'user_id' => 904,
                //     'amount' => $item['amount'],
                //     'fee' => $item['fee'],
                //     'amount_after_fee' => (int) $item['amount'] - (int) $item['fee'],
                //     'received_amount' => (int) $item['amount'] - (int) $item['fee'],
                //     'ref_code' => $item['tran_code'],
                //     'code_hashed' => md5($item['tran_code']),
                //     'code' => $item['tran_code'],
                //     'content' => "NULL",
                //     'created_at' => implode("-", array_reverse(explode("/", explode(" ", $item['created_at'])[0]))) . " " . explode(" ", $item['created_at'])[1],
                //     'status_id' => 2
                // ];
                // \App\Models\Transaction::create($arrIsert);
                // dump($arrIsert);
            }
        }

        dd("done");

        // for ($i = 24890; $i <= 24901; $i++) {
        //     dispatch(new TransactionCallbackResultJob([
        //         'id' => $i,
        //     ]))->onQueue('callback');
        // }

        // dd("done");

        // $withdrawGpayLogService = new WithdrawPaymenthotLogService();

        // dd($withdrawGpayLogService->createRequest([
        //     "user_withdraw_id" => 3246
        // ]));
        // dd($withdrawGpayLogService->checkTokenV2(908));

        $objPaymenthotAccount = PaymenthotAccount::where('id', 4)->first();

        $paymenthot = new Paymenthot();
        $resultLogin = $paymenthot->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPassword($objPaymenthotAccount->password)->setPrivateKey($objPaymenthotAccount->private_key)->login();
        dd($resultLogin);
        // $balanceTechnicalWallet = $paymenthot->setAuthorization($resultLogin["data"]["data"]["accessToken"])->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPassword($objPaymenthotAccount->password)->setPrivateKey($objPaymenthotAccount->private_key)->balanceTechnicalWallet();
        // dd($balanceTechnicalWallet);
        // dd($resultLogin );
        // $bodGetName  = $paymenthot->setAuthorization($resultLogin["data"]["data"]["accessToken"])->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPassword($objPaymenthotAccount->password)->setPrivateKey($objPaymenthotAccount->private_key)->bodGetName([
        //     "bankId" => "VCB",
        //     "bankRefNumber" => "0441003986682",
        // ]);

        // dd( $bodGetName);

        // $bodGetName  = $paymenthot->setAuthorization($resultLogin["data"]["data"]["accessToken"])->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPassword($objPaymenthotAccount->password)->setPrivateKey($objPaymenthotAccount->private_key)->paymentMethod();
        // $bodGetName = $paymenthot->setAuthorization($resultLogin["data"]["data"]["accessToken"])->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPassword($objPaymenthotAccount->password)->setPrivateKey($objPaymenthotAccount->private_key)->initialize([
        //     "currency" => "10000",
        //     "issuerId" => "PAYMENTHOT",
        //     "command" => "PAY",
        //     "paymentMethod" => "QRBANK",
        //     "merchantData" => [
        //         "orderId" =>  "TEST" . time(),
        //         "orderDesc" => "NOI DUNG",
        //         "amount" => $arrParams["merchantData"]["amount"] ?? 0
        //     ]
        // ]);

        // dd($bodGetName);
        $bodGetName = $paymenthot->setAuthorization($resultLogin["data"]["data"]["accessToken"])->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPassword($objPaymenthotAccount->password)->setPrivateKey($objPaymenthotAccount->private_key)->bodGetName([
            "bankId" => "VCB",
            "bankRefNumber" => "1016553127",
        ]);

        dd($bodGetName);

        // $imploreAuth = $paymenthot->setAuthorization($resultLogin["data"]["data"]["accessToken"])->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPasscode($objPaymenthotAccount->password_payout)->setPrivateKey($objPaymenthotAccount->private_key)->imploreAuth([]);
        // $tranfer247 = $paymenthot->setAuthorization($resultLogin["data"]["data"]["accessToken"])->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPasscode($objPaymenthotAccount->password_payout)->setPrivateKey($objPaymenthotAccount->private_key)
        // ->tranfer247([
        //     "verification" => $imploreAuth["data"]["data"]["verifiedKey"] ?? "",
        //     "audit" => time() . rand(1111, 9999),//8755886270480207,
        //     "amount" => 10000,//200000,
        //     "bankCode" => 970436,//"970418",
        //     "bankId" =>  "VCB",//"BIDV",
        //     "bankRefName" => $bodGetName["data"]["data"]["bankRefName"] ?? "",//"NGUYEN VAN A",
        //     "bankRefNumber" => $bodGetName["data"]["data"]["bankRefNumber"] ?? "",
        //     "content" => "CK"
        // ]);

        $imploreAuth = $paymenthot->setAuthorization($resultLogin["data"]["data"]["accessToken"])->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPasscode($objPaymenthotAccount->password_payout)->setPrivateKey($objPaymenthotAccount->private_key)->imploreAuth(["api" => "/merchant-transaction-service/api/v2.0/transfer_247"]);
        dd($imploreAuth);
        $tranfer247 = $paymenthot->setAuthorization($resultLogin["data"]["data"]["accessToken"])->setTenant($objPaymenthotAccount->tenant)->setUsername($objPaymenthotAccount->username)->setPasscode($objPaymenthotAccount->password_payout)->setPrivateKey($objPaymenthotAccount->private_key)
            ->tranfer247V2([
                "verification" => $imploreAuth["data"]["data"]["verifiedKey"] ?? "",
                "audit" => time() . rand(1111, 9999),//8755886270480207,
                "amount" => 10000,//200000,
                "bankCode" => 970436,//"970418",
                "bankId" => "VCB",//"BIDV",
                "bankRefName" => $bodGetName["data"]["data"]["bankRefName"] ?? "",//"NGUYEN VAN A",
                "bankRefNumber" => $bodGetName["data"]["data"]["bankRefNumber"] ?? "",
                "content" => "CK"
            ]);


        dd($tranfer247);



        dd($bodGetName);
        $withdrawGpayLogService = new WithdrawPaymenthotLogService();
        $resultCreateRequest = $withdrawGpayLogService->createRequest([
            "user_withdraw_id" => 3001
        ]);
        dd($resultCreateRequest);

        $yoobil = new Yoobil();
        dd($yoobil->test());

        // $yoobil              = new Yoobil();
        // $intUserId           = 895;
        // $objUserYoobilConfig = UserYoobilConfig::where('user_id', $intUserId)->first();

        // $getBank = $yoobil->test();

        // dd($getBank);



        //       $createCashOutOrder = $yoobil->setBusinessId($objUserYoobilConfig->business_id)
        //     ->setMerchantId($objUserYoobilConfig->merchant_id)
        //     ->setSecretKey($objUserYoobilConfig->secret_key)
        //     ->setPrivateKey($objUserYoobilConfig->private_key)
        //     ->createCashOutOrder([
        //         "return_url" => "https://staging-uat.vnpay.biz/api/test/callback",
        //         "amount" => 200000,
        //         "order_no" => \Str::random(5) . rand(11111, 99999),
        //         "bank_no" => 30,
        //         "phone_number" => "0912312323",
        //         "remark" => "test",
        //         "id_no" => rand(10, 99) . time(),
        //         "account_name" => "NGUYEN VAN TEST",
        //         "account_no" => "10000000",
        //     ]); 

        // dd($createCashOutOrder);

        // $neox    = new Neox();

        // // $arrParams = json_decode('{"transId":"FT350410629233","type":"TRANSACTION","merchantCode":"BJEDAV","transDate":"2024-12-01T04:56:07.187Z","virtualAccountRequestId":"9b50b990-a137-43b6-ae1b-4e3aedb8d23a","virtualAccountId":"NEO00028819","accountName":"TRIN NGUYEN","amount":100000,"note":"NEO1733028945828","serviceInformation":{"code":"VND","desc":null,"groupId":"default"}}', true);
        // // dd(base64_encode(hash('sha256', General::httpBuildQuery($arrParams) . $neox->getClientSecretKey())));

        // $resultAccessToken = $neox->createAccessToken();
        // $strAccessToken    = $resultAccessToken["data"]["access_token"] ?? "";
        // $resultAccessToken = $neox->setAuthentication($strAccessToken)->createVA(["account_name" => "TRIN NGUYEN"]);
        // dd($resultAccessToken);

        $yoobil = new Yoobil();
        $intUserId = 895;
        $objUserYoobilConfig = UserYoobilConfig::where('user_id', $intUserId)->first();

        $getBank = $yoobil->setBusinessId($objUserYoobilConfig->business_id)
            ->setMerchantId($objUserYoobilConfig->merchant_id)
            ->setSecretKey($objUserYoobilConfig->secret_key)
            ->setPrivateKey($objUserYoobilConfig->private_key)
            ->getTransaction(["orderNo" => "VPJS5654020165"]);

        dd($getBank);


        //                   dd(General::getSign(["test" => 1], "375f6a5d37842987958c20f3504699a2", "-----BEGIN PRIVATE KEY-----
// MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCc6ChLd4i5UGyy
// M0HnyIezniTZo8e+GRm+t6e43wIjZNq9A6LcptpaNNFIU4kmYLLBp8adFdsXDOFj
// mhRk1EEmlE+USUnwkvlj4scqCV4XWjl+r5lv5QViIydLGgcRxBkeFgLzYZj6EIj7
// XuYBeiuN5V9FNqpnZ53w8l8HyaYN/VZ/OkP9qAQmLWiGi4ka93ey1o9dk4YlgLg3
// YklUcOe8nts4ipG2d8CjyoyQjrOisd/OwQKGDiFTC+effXZDQ+KCWnrVaGCcDJDW
// v+Ckco/lvFsCiw/4Tor34J5coETKbMlHw2NmYbsFF0Y89OZLoDH1cKNUy5ecyHqG
// FySHL6aPAgMBAAECggEASXhPfd7Pz24AJ4TmxEBagll3ic0tty6LpojaaS9LEgWk
// gsLUHJuoPHrk0AgFfUnkCdekoWNdfjKSyw+XowrcItNpW9bhX1uCmSnT9jQCsc6W
// g2J35zQGzEgHmxvp2YDH/hVydkHp3pWlfoaWDnUFNeEENiV9rwOOqgbjRoM+VOfi
// F6+Y6k+hgM6djBdG8pBw1wObV505r+NKV8BYCCmhVcMdz/3u/hco/NarHCulL82H
// h9euf7q2qWiEpKKWCQ1jv++WkcRaI2rmk0lbva4P8AIFp0LU24dlXM2PV9WV7Q4R
// QYXY8Sn1c4FQSxwXRf84qDQxCnq1ieCKSNuFDw0tqQKBgQC/acwaR1+mFCAUzKul
// Fo/8SZARojpXF8KlUVC7aBkjf+5Y4ysuqZ6j2jqNpFVTeKrdovUPdn4Kd7jXGqed
// lieC2/sg7GXb3R0tmX5Ud6bRX5N0HEa3Mbugj1rvSLJ9k1KPuXjY/spAR3UUvrEo
// s0IIaymfsmdGMrq5BfYExN2HWwKBgQDR2bXeSCKC/WdlVs+wi+vrxRH7VOIx9LL3
// 4tFUMLgXQ8Br4irp/JItYVQQr1GunqWNDxBlJ6l2makoFafZh7eVa+6PJdNpD0eI
// S1A4r1q7/uoyHHrBFbCqSaop3WMyrwXiO2r/I2cShD+2112Oiz3DgIKEis9u1Sjo
// n193e1v33QKBgDGp1ZcL5blFh31eDTrsO7eNrp+ko9ZtB8e07WlyfPNFAiZ16oJU
// 6CBDQuX1OV5K9KpE2aiFafZ1UbQd6lds2huNz/6e117QY/2s0aZA1TuFvNBndcGa
// WcAy8bkb95O1YbqAuOY0VW2QHMShJX4V5JAinc4dj8Ya62+OKGLcYU35AoGBAM+p
// Bl0JuwUu6COTTYEiZXrxkELLE+9le7jrvkP21iVHiWH49IiJxMmdd8fvBgCrw52c
// G5hOMFdJ5efhzjDoKZZ2sSL8xoE/eoT4KlF9zWcd8flWz27FHQbWUMqO5vwf0M24
// CMCj8vqS3k38Pvuw9JTDVeT9TBocXBgepiUhcLgNAoGAMwRcsxWPY3NuT97sSujo
// BmljlA04IyE8Mo89DrPh0CWnN4D7vo2VCPMW4vvmKLCEZIuBCnVOZumoL8DL5Uu6
// GWPBTEZc5Ko0m2bGr0TlIFqcIOrul/2ii4AJ0pPS1w36Md1YvcyuDldyWAsewqh6
// EacbWob9azcAiycXRChK6Zo=
// -----END PRIVATE KEY-----"));


        dd(General::getSign(["test" => 1], "375f6a5d37842987958c20f3504699a2", file_get_contents(base_path('app/Utilities/Yoobil/rsa_private_key_dev.pem'))));
        $y = new Yoobil();
        dd($y->createGiaoNhan(""));

        define("VNPAY_API_TOKEN", "4fa8fa08723446f843fcac7921506d3e");

        dd($this->SHA256withRSA("limit=20&page=1&query=", base_path('app/Utilities/Yoobil/rsa_private_key_dev.pem')));

        //     $strPrivateKey = file_get_contents(base_path('app/Utilities/Yoobil/rsa_private_key.pem'));

        //     $arrData = json_decode('{
        //   "merchantId": "215",
        //   "businessId": "19",
        //   "timestamp": 1705985493251,
        //   "version": "2.0",
        //   "returnUrl": "https://vnpay.biz/api/withdraw-yoobil/callback",
        //   "orderNo": "wit46be1aaee19682f98033",
        //   "currency": "VND",
        //   "clientId": "",
        //   "amount": 1000000,
        //   "bankLocation": "VN",
        //   "bankNo": 19,
        //   "accountName": "21312313",
        //   "accountNo": "123123",
        //   "phoneNumber": "0924111111",
        //   "accountType": 0,
        //   "remark": "",
        //   "idNo": "631705985493"
        // }', true);
        //     //      "sign": "c7o+XqSK5DYXJSLcwhpsow+sLEwbSaa81n+SfvbPanZrco0ZSWtUJOM+p6DLCHy/y3WnlptCxL9Zvr8LGaUXytxAFUn0mH0deUenHS5dWOuNaqtO/m5jYGyFGQhqvym8YSsKJNDEhp3kR5oH+SblGKVovf6lJniNnMwgWxKV52twjO74ILiCE2lVRMU2Htb3oFMLnEZRK/f/Z/CQN6MoShgkU28q0mEmEtjbkYymRkyJ/r+GjFYCWSc3IIZHv0bNJ6pDHpVex18u/TbhygwtE0zGhgWTcworutKHO4gvF40syC75ybokHc42gD8F3MK2UgeJhsDMq6+wTr6hwndOaw=="

        //     dd(General::getSign($arrData, "j0D03AlJx67VPSr122581v8m75y68o0O8792EG14", $strPrivateKey));


        $strPrivateKey = file_get_contents(base_path('app/Utilities/Yoobil/rsa_test_private_key_dev.pem'));
        $strPublicKey = file_get_contents(base_path('app/Utilities/Yoobil/rsa_test_public_key_dev.pem'));



        $strSign = "X/LfaHzDbHC9gGzfs57nzjkiR3Rb0BEUZz5LJ52BZLjnEM0DXUNOreM7MjMA/tmBPYO1ms9Jnm3apDFuEa0NICT7kfwarxSlD2nICbJVTIgqFZ5rtSdSTDKbbHBWLu13/vtTo7QZVLdWBIVu/1iJJSjcI5KDTQ34BQCX70Fm7NeKtXdWbOT8Kw4FZ9C3zijtOWi+kQlyRo/p1wCbc8wYwjRKZAo+DSJzogZ+NFnLIL/dQBX6db6fUyEBmyO3W6brPCh2dbiazwuDbCcauL5OfcDPqBTTqugmXEjeeq3OzCXGVYxP3iOVbgBv+J94NGa0qshDHRDmcyLsF6UyF5pkWA==";
        $verified = openssl_verify("limit=100&page=1&query=4fa8fa08723446f843fcac7921506d3e", base64_decode($strSign), openssl_pkey_get_public($strPublicKey), OPENSSL_ALGO_SHA256);

        dd($verified);

        $strOpenSSLData = "limit=20&page=1&query=";
        $strSecretKey = "4fa8fa08723446f843fcac7921506d3e";
        $strOpenSSLBinary = "";
        openssl_sign($strOpenSSLData . $strSecretKey, $strOpenSSLBinary, $strPrivateKey, "SHA256");
        $strSigniture = base64_encode($strOpenSSLBinary);

        dump($strOpenSSLData . $strSecretKey, $strSigniture);

        $arrData = [
            "limit" => 20,
            "page" => 1,
            "query" => [],
            "sign" => $strSigniture
        ];
        dd(General::verifySign($arrData, $strSecretKey, $strPublicKey));
        $verified = openssl_verify($strOpenSSLData . $strSecretKey, base64_decode($strSigniture), $strPublicKey, OPENSSL_ALGO_SHA256);
        dd($verified);




        $withdrawYoobilLogService = new WithdrawYoobilLogService();
        dd($withdrawYoobilLogService->createRequest(["user_withdraw_id" => 28]));
        // $config = [
        //     "private_key_bits" => 2048,
        //     "private_key_type" => OPENSSL_KEYTYPE_RSA,
        // ];

        // $keypair = openssl_pkey_new($config);

        // openssl_pkey_export($keypair, $private_key);

        // $public_key = openssl_pkey_get_details($keypair);
        // $public_key = $public_key["key"];

        // echo "Private key: " . $private_key . "\n";
        // echo "Public key: " . $public_key . "\n";


        // $res = openssl_pkey_new();
        // openssl_pkey_export($res, $strPrivateKey);
        // $publicKeyPem = openssl_pkey_get_details($res)['key'];


        // $intUserId           = 888;
        // $objUserYoobilConfig = UserYoobilConfig::where('user_id', $intUserId)->first();

        // $jsonData = '{"code":10000,"msg":"the request is succeed.","result":{"accountName":"NGUYEN HOANG NAM","accountNo":"9631242000000967957","amount":10000,"bankName":"BIDV","businessId":19,"currency":"VND","expireDate":null,"feeId":25,"merchantId":215,"orderNo":"6Jlf085464","purchaseAmount":10000,"purchaseCurrency":"VND","purchaseTime":"1705633305948","remark":"Thanh toan QR Ck","sign":"LyecaP8pel6reXYILU0B7S3ZDoRA9px9CaVV4JxQALO0CAN1B2l0y8wPMdX6NvG656p0ifjeqCMexRWen599fb6mcvO7g3wnOZY62TpI6g9SOa0YG8IciWoORfCkpNwAEUIUiIQZKTa9olUHL0hgJnwZnvMU4gP7mEewgcWgcs9a+LbWZn7a3KUYDwVo7zYgJrdR5\/H3PDiuS7YrTy8B\/TJPsBdL4RyOmNq1ZQRPzrlypy9A+eaLDWzWcF7ops3jp\/c0dLq65itzJUWCkjUJ1Ll5Y1TttMYy2AAuWEJTAg1ipXX0uvLhZ\/d5m4DNNqL6\/JyPXUBP389BQ0Mf+0oamw==","tradeNo":"0050001748178908186021888","userName":"NGUYEN HOANG NAM"},"q":"\/api\/app-message\/yoobil-forward","device":"yoobil","token":"4fa8fa08723446f843fcac7921506d3e","user_businesse_id":"1"} ';
        // $arrData  = json_decode($jsonData, true);

        $yoobil = new Yoobil();
        $intUserId = 895;
        $objUserYoobilConfig = UserYoobilConfig::where('user_id', $intUserId)->first();
        // $strPublicKey = (General::beautyKey($objUserYoobilConfig->yoobil_public_key, "PUBLIC KEY"));
        // $verified     = $yoobil->setSecretKey($objUserYoobilConfig->secret_key)->setPublicKey($strPublicKey)->verifySign($arrData["result"]);
        // dd($verified);


        $createCashOutOrder = $yoobil->setBusinessId($objUserYoobilConfig->business_id)
            ->setMerchantId($objUserYoobilConfig->merchant_id)
            ->setSecretKey($objUserYoobilConfig->secret_key)
            ->setPrivateKey($objUserYoobilConfig->private_key)
            ->createCashOutOrder([
                "return_url" => "https://uat.vnpay.biz/api/app-message/yoobil-forward?device=yoobil&token=82228c07552734e13b61a2cc18601c2d",
                "amount" => 200000,
                "order_no" => \Str::random(5) . rand(11111, 99999),
                "bank_no" => 30,
                "phone_number" => "091231233",
                "remark" => "test",
                "id_no" => rand(10, 99) . time(),
                "account_name" => "NGUYEN VAN TEST",
                "account_no" => "10000000",
            ]);

        dd($createCashOutOrder);
        $getBank = $yoobil->setBusinessId($objUserYoobilConfig->business_id)
            ->setMerchantId($objUserYoobilConfig->merchant_id)
            ->setSecretKey($objUserYoobilConfig->secret_key)
            ->setPrivateKey($objUserYoobilConfig->private_key)
            ->getBanks();




        foreach ($getBank["data"]["result"]["bankList"] as $arrBank) {
            $shortName = str_replace(" ", "", $arrBank["abbreviation"] ?? "");
            $objBank = Bank::whereRaw('REPLACE(short_name," ","") like "%' . $shortName . '%"')->first();
            if ($objBank) {
                // $this->info($shortName);
                $objBankYobil = BankYoobilMapping::where('bank_id', $objBank->id)->first();
                if (!$objBankYobil) {
                    BankYoobilMapping::create([
                        'bank_id' => $objBank->id,
                        'yoobil_bank_id' => $arrBank["bankID"]
                    ]);
                }
            } else {
                $this->error($shortName);
            }
        }

        // // dd(urldecode(General::httpBuildQuery(["test" => ["tes1" => ["test2" => "222"]], "test3" => ["tes1" => ["test2" => null]]])));
        // // dd(urldecode(http_build_query(array_filter(["test" => ["tes1" => ["test2" => "222"]], "test3" => ["tes1" => ["test2" => null]]]))));
        // $result = $arrData["result"];
        // ksort($result);

        // // $parameters = array();
        // // foreach ($result as $key => $value) {
        // //     $parameters[] = ($key) . '=' . ($value);
        // // }

        // // dump($parameters);
        // // $strParameter = implode('&', $parameters);
        // // dump(urldecode(http_build_query($result)));
        // // dd($strParameter);

        // $strPublicKey = (General::beautyKey($objUserYoobilConfig->yoobil_public_key, "PUBLIC KEY"));
        // $strSecretKey = $objUserYoobilConfig->secret_key;
        // // echo urlencode(http_build_query($arrData)) . $strSecretKey;exit;

        // $verified = openssl_verify(urldecode(General::httpBuildQuery($result)) . $strSecretKey, base64_decode($strSign), $strPublicKey, OPENSSL_ALGO_SHA256);
        // dd($verified);



        $yoobil = new Yoobil();
        dd($yoobil->setBusinessId($objUserYoobilConfig->business_id)
            ->setMerchantId($objUserYoobilConfig->merchant_id)
            ->setSecretKey($objUserYoobilConfig->secret_key)
            ->setPublicKey($objUserYoobilConfig->public_key)
            ->verifySign($arrData["result"]));


        $yoobil = new Yoobil();
        dd($yoobil->setBusinessId($objUserYoobilConfig->business_id)
            ->setMerchantId($objUserYoobilConfig->merchant_id)
            ->setSecretKey($objUserYoobilConfig->secret_key)
            ->setPrivateKey($objUserYoobilConfig->private_key)
            ->getSign($arrData["result"]));


        // $strPlaintext = urlencode(http_build_query($arrData["result"]));
        // $strSecretKey = $objUserYoobilConfig->secret_key;
        // $strSigniture = $sign;
        // $strPublicKey = $objUserYoobilConfig->public_key;
        // $verified     = openssl_verify($strPlaintext . $strSecretKey, base64_decode($strSigniture), $strPublicKey, OPENSSL_ALGO_SHA256);
        // dump($strPlaintext, $strSecretKey, $strSigniture, $strPublicKey);
        // dd($verified);



        // if ($this->request->isPost()) {
        //     $params  = file_get_contents("php://input");
        //     $logPath = LOG_PATH . "/yoobill/";
        //     $strLog  = '[' . date('d/m/Y H:i:s', time()) . '] : ' . json_encode($params);
        //     shell_exec('echo ' . $strLog . ' >> ' . $logPath . 'sms_' . md5($params) . '.log 2>&1 & echo $!');

        //     $result = json_decode($params, true);
        //     $result = $result['result'];
        //     $sign   = $result['sign'];
        //     unset($result['sign']);
        //     ksort($result);
        //     $parameters = array();
        //     foreach ($result as $key => $value) {
        //         $parameters[] = ($key) . '=' . ($value);
        //     }
        //     $strParameter = implode('&', $parameters);
        //     // Load your public key (replace 'public_key.pem' with your actual public key file)
        //     $publicKey = openssl_pkey_get_public(file_get_contents(APP_PATH . 'yoobil_public_key.pem'));

        //     // The original data that was signed
        //     $originalData = $strParameter . YOOBILL_SECRECT_KEY;
        //     // The Base64-encoded signature (replace 'base64Signature' with your actual signature)
        //     $base64Signature = $sign; // Replace this with the actual Base64-encoded signature
        //     // Decode the Base64 signature to obtain the binary signature
        //     $binarySignature = base64_decode($base64Signature);
        //     // Verify the signature using SHA256withRSA
        //     $signatureVerified = openssl_verify($originalData, $binarySignature, $publicKey, OPENSSL_ALGO_SHA256);
        //     if ($signatureVerified === 1) {
        //         $arrSms      = [
        //             'sms_content' => $result['bankName'] . ' ' . $result['userName'] . ' ' . $result['remark'] . ' +'
        //                 . $result['purchaseAmount'] . $result['purchaseCurrency'],
        //             'raw_data' => json_encode($params),
        //             'sms_from' => $result['bankName'],
        //             'sms_amount' => $result['purchaseAmount'],
        //             'is_analysis' => 0,
        //             'sms_note' => $result['remark'],
        //             'sms_receive_date' => $result['purchaseTime'] / 1000,
        //             'created_date' => time(),
        //             'is_fado' => 0,
        //             'is_warning' => 0,
        //         ];
        //         $instanceSms = new Sms();
        //         $result      = $instanceSms->add($arrSms);
        //         if ($result) {
        //             return $this->response->setContent(json_encode(['success' => 1, 'message' => 'Luu du lieu thanh cong.....']));

        //         } else {
        //             return $this->response->setContent(json_encode(['error' => 1, 'message' => 'Luu du lieu that bai.....']));

        //         }
        //     } elseif ($signatureVerified === 0) {
        //         return $this->response->setContent(json_encode(['error' => 1, 'message' => 'Signature is invalid']));
        //         echo "Signature is invalid.";
        //     } else {
        //         return $this->response->setContent(json_encode(['error' => 1, 'message' => 'An error occurred during signature verification']));
        //     }

        // }



        // $arrData         = ["test"];
        // $sign            = ($yoobil->setBusinessId($objUserYoobilConfig->business_id)
        //     ->setMerchantId($objUserYoobilConfig->merchant_id)
        //     ->setSecretKey("8888888888888888888")
        //     ->setPrivateKey($objUserYoobilConfig->private_key)
        //     ->getSign(["test"]));
        // $arrData["sign"] = $sign;

        // dd($arrData);

        dd($yoobil->setBusinessId($objUserYoobilConfig->business_id)
            ->setMerchantId($objUserYoobilConfig->merchant_id)
            ->setSecretKey($objUserYoobilConfig->secret_key)
            ->setPublicKey($objUserYoobilConfig->public_key)
            ->verifySign($arrData["result"]));

        dd("done");
        dd($yoobil->setBusinessId($objUserYoobilConfig->business_id)
            ->setMerchantId($objUserYoobilConfig->merchant_id)
            ->setSecretKey($objUserYoobilConfig->secret_key)
            ->setPrivateKey($objUserYoobilConfig->private_key)
            ->getBanks());

        \App\Jobs\TelegramNotificationJob::dispatch([
            'message' => "NỘI DUNG",
            'type' => "notification",
            'chat_id' => ''
        ])->onQueue('notification');

        dd("done");

        // dispatch(new TransactionCallbackResultJob([
        //     'id' => 11,
        // ]))->onQueue('callback');

        // dd("done");

        $transactionService = new TransactionService();

        dd($transactionService->formatContentToTransactionCode(["content" => "Số dư TK VCB 1037310220 +31,000 VND lúc 04-12-2023 14:53:28. Số dư 20,687,888 VND. Ref 486913.041223.145327.QR - MUA hoang503 quan", 'bypass_check_exist_code' => true]));
        $telegram = new Telegram();
        dd($telegram->setToken("6594265786:AAF-I3dFH5pvdwm_gQG6TkPAvvMQ_8L7uEQ")->sendMessage(["chat_id" => "-4003476354", "message" => "test"]));
        exit;

    }

}