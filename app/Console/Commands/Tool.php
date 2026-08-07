<?php

namespace App\Console\Commands;

use App\Jobs\PayoutCallbackResultJob;
use App\Jobs\TransactionCallbackResultJob;
use App\Jobs\UpdateBalanceGatewayAccountJob;
use App\Models\GatewayAccount;
use App\Models\GatewayFee;
use App\Models\PaymenthotAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserFee;
use App\Models\UserReferalFee;
use App\Models\UserWithdraw;
use App\Services\AppMessageService;
use App\Services\GatewayAccountService;
use App\Services\TransactionService;
use App\Utilities\S3ClloudBizStorage;
use App\Utilities\PaymenthotWeb;
use App\Utilities\Telegram;
use Illuminate\Console\Command;


class Tool extends Command
{

    use PrependsOutput,
        PrependsTimestamp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tool {--type=default} {--id=default} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'tool {--type=default} {--id=default}';

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
        ini_set('memory_limit', '2048M');
        $type = $this->option('type');
        switch ($type) {
            case 'check-transaction':
                $this->checkTransaction();
                break;
            case 'migrate-profit':
                $this->migrateProfit();
                break;
            case 'backup-database':
                $this->backupDatabase();
                break;
            case 'sync-report-revenue':
                $this->syncReportRevenue();
                break;
            case 'sync-gateway-account-balance':
                $this->syncGatewayAccountBalance();
                break;
            case 'collection-callback':
                $this->collectionCallback();
                break;
            case 'withdraw-callback':
                $this->withdrawCallback();
                break;

            case 'sync-amount-pendding':
                $this->syncAmountPendding();
                break;
            default:
                $this->error("Không tìm thấy --type=$type");
        }
    }

    public function syncAmountPendding()
    {
        $obTransactions = Transaction::select(\DB::raw('SUM(amount_after_fee) as amount_pendding,user_id'))->where('status_id', 6)->where('for_control_yyyymmdd', date('Ymd'))->groupBy('user_id')->get();
        foreach ($obTransactions as $obTransaction) {
            $obUser = User::find($obTransaction->user_id);
            $obUser->user_balance_n1 = $obTransaction->amount_pendding;
            $obUser->save();
            $this->info("User ID: {$obUser->id} updated: " . $obUser->user_balance_n1);
        }

        $obTransactions = Transaction::select(\DB::raw('SUM(amount_after_fee) as amount_pendding,user_id'))->where('status_id', 6)->where('for_control_yyyymmdd', date('Ymd', strtotime('+1 day')))->groupBy('user_id')->get();
        foreach ($obTransactions as $obTransaction) {
            $obUser = User::find($obTransaction->user_id);
            $obUser->user_balance_n2 = $obTransaction->amount_pendding;
            $obUser->save();
            $this->info("User ID: {$obUser->id} updated: " . $obUser->user_balance_n2);
        }

        $obTransactions = Transaction::select(\DB::raw('SUM(amount_after_fee) as amount_pendding,user_id'))->where('status_id', 6)->where('for_control_yyyymmdd', date('Ymd', strtotime('+2 day')))->groupBy('user_id')->get();
        foreach ($obTransactions as $obTransaction) {
            $obUser = User::find($obTransaction->user_id);
            $obUser->user_balance_n3 = $obTransaction->amount_pendding;
            $obUser->save();
            $this->info("User ID: {$obUser->id} updated: " . $obUser->user_balance_n3);
        }
    }

    public function collectionCallback()
    {
        $id = $this->option('id');
        $userWithdraw = Transaction::where('ref_code', $id)->first();
        if ($userWithdraw) {
            $userWithdraw->callback_total_retry = 0;
            $userWithdraw->save();
            $id = $userWithdraw->id;
        }

        dispatch(new TransactionCallbackResultJob([
            'id' => $id,
        ]))->onQueue('callback');
        dump($id);
    }

    public function withdrawCallback()
    {
        $id = $this->option('id');
        $userWithdraw = UserWithdraw::where('trans_code', $id)->first();
        if ($userWithdraw) {
            $userWithdraw->callback_total_retry = 0;
            $userWithdraw->save();
            $id = $userWithdraw->id;
        }

        dispatch(new PayoutCallbackResultJob([
            'id' => $id,
        ]))->onQueue('callback');
        dump($id);
    }

    public function syncGatewayAccountBalance()
    {

        // $gatewayAccountService = new GatewayAccountService();
        // $resultCreateRequest = $gatewayAccountService->updateBalance([
        //     "id" => 12
        // ]);

        // dd($resultCreateRequest);

        $objGatewayAccounts = GatewayAccount::where('status_id', 2)->get();
        foreach ($objGatewayAccounts as $objGatewayAccount) {
            $this->info("SYNC Balance for Gateway Account ID: {$objGatewayAccount->id}");
            dispatch(new UpdateBalanceGatewayAccountJob([
                'id' => $objGatewayAccount->id,
            ]))->onQueue('request');
        }
        sleep(8);
        $sql = "
            SELECT FORMAT(((SELECT SUM(balance+pending_balance) FROM gateway_accounts WHERE gateway_id IN (1,2,3) AND id !=2)+ (SELECT SUM(amount) FROM user_debits WHERE type_id!=1 AND deleted_at is null)) - ((SELECT SUM(balance) FROM users INNER JOIN user_balances ON users.id = user_balances.user_id WHERE group_id = 2) + (SELECT SUM(received_amount) FROM transactions WHERE status_id = 6 
            )),0) as rev
            ";
        $results = \Illuminate\Support\Facades\DB::select($sql, []);
        $intAmount = current($results[0] ?? "");
        \App\Jobs\TelegramNotificationJob::dispatch([
            'subject' => "REBATO REPORT",
            'message' => "Lợi nhuận RIBATO: " . $intAmount . "đ",
            'bot_token' => "5662588556:AAF9_L4fo2HS2z7rr6i9z2MIHd4oukjdniU",
            'chat_id' => "-1001830429242"
        ])->onQueue('notification');
    }

    public function syncReportRevenue()
    {



    }

    public function checkTransaction()
    {

        $objPaymentHotAccounts = PaymenthotAccount::where('is_check_manual', 1)->get();
        $appMessageService = new AppMessageService();
        $transactionService = new TransactionService();
        $paymenthotWeb = new PaymenthotWeb();
        foreach ($objPaymentHotAccounts as $id => $objPaymentHotAccount) {
            $strToken = $objPaymentHotAccount->access_token;
            if (empty($strToken)) {
                $resultLogin = $paymenthotWeb->setUsername($objPaymentHotAccount->username)
                    ->setPassword($objPaymentHotAccount->password_hash)
                    ->login();
                if ($resultLogin['success']) {
                    $strToken = $resultLogin['data']["accessToken"] ?? "";
                    $objPaymentHotAccount->access_token = $strToken;
                    $objPaymentHotAccount->save();
                }
            }

            $merchantTranfer = $paymenthotWeb->setAuthorization($strToken)->merchantTranferHistory();
            if (empty($merchantTranfer['success'])) {
                \Log::info("Expriced Token");
                $objPaymentHotAccount->access_token = "";
                $objPaymentHotAccount->save();
                continue;
            }

            foreach ($merchantTranfer['data']["content"] as $key => $arrMerchantTranfer) {
                $intAmount = $arrMerchantTranfer["orgAmount"] ?? "0";
                if (empty($intAmount)) {
                    continue;
                }

                $objUserToken = \App\Models\UserToken::where("user_id", $objPaymentHotAccount->user_id)->first();
                if (empty($objUserToken)) {
                    continue;
                }
                $strAccountName = "";
                $strBankAccountNumber = "";
                $strTradeNo = $arrMerchantTranfer["auditNumber"];
                $intReceivedYear = substr($arrMerchantTranfer["createdAt"], 0, 4);
                $intReceivedMonth = substr($arrMerchantTranfer["createdAt"], 4, 2);
                $intReceivedDay = substr($arrMerchantTranfer["createdAt"], 6, 2);
                $intReceivedHour = substr($arrMerchantTranfer["createdAt"], 8, 2);
                $intReceivedMunite = substr($arrMerchantTranfer["createdAt"], 10, 2);
                $intReceivedSecond = substr($arrMerchantTranfer["createdAt"], 12, 2);

                $strBody = $arrMerchantTranfer["content"] ?? "";
                $strReceivedDate = $intReceivedYear . "-" . $intReceivedMonth . "-" . $intReceivedDay . " " . $intReceivedHour . ":" . $intReceivedMunite . ":" . $intReceivedSecond;
                $arrParams = [
                    'device' => "",
                    'sender' => 'paymenthot',
                    'receiver' => "",
                    'content' => $strBody,
                    'content_origin' => $strBody,
                    'type_id' => 3,
                ];

                $ressultAdd = $appMessageService->add($arrParams);
                if ($ressultAdd["error_code"] != 0) {
                    \Log::info("PaymenthotWeb FW MESSAGE ressultAdd" . json_encode($ressultAdd));
                    $this->info("PaymenthotWeb FW MESSAGE ressultAdd" . json_encode($ressultAdd));
                    continue;
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
                    $this->info("PaymenthotWeb MESSAGE resultDetectCodeTransaction" . json_encode($resultDetectCodeTransaction));
                    continue;
                }

                $intTotalBalance = 0;
                $resultCreatePayment = $transactionService->createPayment([
                    "user_id" => $objUserToken->user_id,
                    'ref_code' => $strTradeNo,
                    'user_token_id' => $objUserToken->id,
                    'amount' => $intAmount,
                    "bank_account_name" => $strAccountName,
                    "bank_account_number" => $strBankAccountNumber,
                ]);

                if ($resultCreatePayment["error_code"] != 0) {
                    \Log::info("PaymenthotWeb MESSAGE resultCreatePayment" . json_encode($resultCreatePayment));
                    $this->info("PaymenthotWeb MESSAGE resultCreatePayment" . json_encode($resultCreatePayment));
                    continue;
                }
                /**
                 * Gọi qua app transaction đẩy nội dung qua xử lý
                 */

                $strCode = $resultCreatePayment["data"]["code"];
                $resultUpdateTransaction = $transactionService->updateResultTransaction(["received_date" => $strReceivedDate, "content" => $strBody, "code" => $strCode, "amount" => $intAmount, "total_balance" => $intTotalBalance]);
                if ($resultUpdateTransaction["error_code"] != 0) {
                    \Log::info("PaymenthotWeb MESSAGE resultUpdateTransaction" . json_encode($resultUpdateTransaction));
                }

                $strMsg = "PaymenthotWeb\nThời gian : $strReceivedDate\nLoại : NẠP\nSố tiền : " . number_format($intAmount) . "\nSố dư : unknown\nNội dung : $strBody";
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

            }


        }

        // exit;
        // $paymenthotWeb = new PaymenthotWeb();
        // $resultLogin   = $paymenthotWeb->setUsername('m85user1')
        //     ->setPassword('NThjZGUxNmE0N2JiMzAyZWQ4NmQyNjYyODIwZTk4NjcwNGQ5N2U1NTQ1ZThkMTA0OTQzODY5YTEyYWFmZjFjMg==')
        //     ->login();



        // $merchantTranfer = $paymenthotWeb->setAuthorization()->merchantTranfer();
        // dd($merchantTranfer);
    }


    public function detectCodeTransaction($arrParams)
    {

        $strBody = $arrParams["content"] ?? "";
        $intAmount = $arrParams["amount"] ?? 0;
        $strReceivedDate = $arrParams["received_date "] ?? 0;

        /**
         * Gọi qua app transaction đẩy nội dung qua xử lý
         */
        $transactionService = new TransactionService();
        $resultFormatContent = $transactionService->formatContentToTransactionCode(["content" => $strBody]);
        if ($resultFormatContent["error_code"] != 0) {
            \Log::info("detectCodeTransaction resultFormatContent" . json_encode($resultFormatContent));
            return $resultFormatContent;
        }

        if (empty($intAmount)) {
            return $transactionService->setStatusCode(404)->setMessage("")->setData($arrParams)->setErrors([
                [__("Vui lòng nhập số tiền.")]
            ])->result();
        }

        /**
         * Gọi qua app transaction đẩy nội dung qua xử lý
         */
        $strCode = $resultFormatContent["data"]["code"];
        $intTotalBalance = $resultFormatContent["data"]["total_balance"];

        $resultUpdateTransaction = $transactionService->updateResultTransaction(["received_date" => $strReceivedDate, "content" => $strBody, "code" => $strCode, "amount" => $intAmount, "total_balance" => $intTotalBalance]);

        if ($resultUpdateTransaction["error_code"] != 0) {
            \Log::info("detectCodeTransaction resultUpdateTransaction" . json_encode($resultUpdateTransaction));
            return $resultUpdateTransaction;
        }
        return $resultUpdateTransaction;

    }

    public function migrateProfit()
    {

        $objTransactions = Transaction::get();
        foreach ($objTransactions as $objTransaction) {
            $intAmount = $objTransaction->amount;
            $intUserId = $objTransaction->user_id;
            $objUser = User::where('id', $intUserId)->first();
            if (!$objUser) {
                continue;
            }
            /**
             * Lấy phí IN 
             */
            $intUserFee = 0;
            $arrUserFee = [];
            $objUserFee = UserFee::where('user_id', $intUserId)->whereIn('type_id', [1, 3])->first();
            if ($objUserFee) {

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

                $arrUserFee = [
                    "type_id" => $objUserFee->type_id,
                    "fee" => $objUserFee->fee,
                    "min_fee" => $objUserFee->min_fee,
                ];
            }


            $intGatewayFee = 0;
            $arrGatewayFee = [];
            $objGatewayFee = GatewayFee::where('gateway_id', $objTransaction->gateway_id)->whereIn('type_id', [1, 3])->first();
            if ($objGatewayFee) {

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

                $arrGatewayFee = [
                    "type_id" => $objGatewayFee->type_id,
                    "fee" => $objGatewayFee->fee,
                    "min_fee" => $objGatewayFee->min_fee,
                ];
            }

            $intUserReferalFee = 0;
            $arrUserReferalFee = [];
            $objUserReferalFee = UserReferalFee::where('user_id', $intUserId)->whereIn('type_id', [1, 3])->first();
            if ($objUserReferalFee) {

                if ($objUserReferalFee->type_id == 1) {
                    //1: phí cố định in
                    $intUserReferalFee += $objUserReferalFee->fee;

                } elseif ($objUserReferalFee->type_id == 3) {
                    // 3: phí % in
                    if (!empty($objUserReferalFee->fee)) {
                        if (strtotime($objTransaction->created_at) < strtotime('2025-10-01 00:00:00')) {
                            $objUserReferalFee->fee = 0.1;
                        }
                    }

                    $intUserReferalFee += round($intAmount * $objUserReferalFee->fee / 100, 3);
                }

                if ($intUserReferalFee < $objUserReferalFee->min_fee) {
                    $intUserReferalFee = $objUserReferalFee->min_fee;
                }

                $arrUserReferalFee = [
                    "type_id" => $objUserReferalFee->type_id,
                    "fee" => $objUserReferalFee->fee,
                    "min_fee" => $objUserReferalFee->min_fee,
                ];
            }

            $intUserFee = $objTransaction->fee;
            $objTransaction->referal_fee_json = json_encode($arrUserReferalFee);
            $objTransaction->referal_fee = $intUserReferalFee;
            $objTransaction->gateway_fee_json = json_encode($arrGatewayFee);
            $objTransaction->gateway_fee = $intGatewayFee;
            $objTransaction->profit = $intUserFee - ($intGatewayFee);
            $objTransaction->save();
            $this->info($objTransaction->profit);

        }


        $objUserWidthDraws = UserWithdraw::get();
        foreach ($objUserWidthDraws as $objUserWidthDraw) {
            $intAmount = $objUserWidthDraw->amount;
            $intUserId = $objUserWidthDraw->user_id;
            $objUser = User::where('id', $intUserId)->first();
            if (!$objUser) {
                continue;
            }
            /**
             * Lấy phí IN 
             */
            $intUserFee = 0;
            $arrUserFee = [];

            $intUserFeeEstimate = 0;
            $arrUserFeeEstimate = [];
            $objUserFee = UserFee::where('user_id', $intUserId)->whereIn('type_id', [2, 4])->first();
            if ($objUserFee) {
                $arrUserFee = [
                    "type_id" => $objUserFee->type_id,
                    "fee" => $objUserFee->fee,
                    "min_fee" => $objUserFee->min_fee
                ];
                $arrUserFeeEstimate = [
                    "type_id" => $objUserFee->type_id,
                    "fee" => $objUserFee->fee_estimate,
                    "min_fee" => $objUserFee->min_fee
                ];
                /**
                 * Liểm tra loại để tính phí
                 * 1: phí cố định in
                 * 2: phí cố định out
                 * 3: phí % in
                 * 4: phí % out
                 */
                $intUserBusiessFeeType = $objUserFee->type_id;
                if ($intUserBusiessFeeType == 2) {
                    //1: phí cố định in
                    $intUserFee += $objUserFee->fee;
                    $intUserFeeEstimate += $objUserFee->fee_estimate;
                } elseif ($intUserBusiessFeeType == 4) {
                    // 3: phí % in
                    $intUserFee += round($intAmount * $objUserFee->fee / 100, 3);
                    $intUserFeeEstimate += round($intAmount * $objUserFee->fee_estimate / 100, 3);
                }

                if ($intUserFee < $objUserFee->min_fee) {
                    $intUserFee = $objUserFee->min_fee;
                }
                if ($intUserFeeEstimate < $objUserFee->min_fee) {
                    $intUserFeeEstimate = $objUserFee->min_fee;
                }
            }

            $intAmountAfterFee = $intUserFee + $intAmount;
            $intAmountEstimateAfterFee = $intUserFeeEstimate + $intAmount;


            $intGatewayId = $objUserWidthDraw->gateway_id;

            $intGatewayFee = 0;
            $arrGatewayFee = [];
            $objGatewayFee = GatewayFee::where('gateway_id', $intGatewayId)->whereIn('type_id', [2, 4])->first();
            if ($objGatewayFee) {
                $arrGatewayFee = [
                    "type_id" => $objGatewayFee->type_id,
                    "fee" => $objGatewayFee->fee,
                    "min_fee" => $objGatewayFee->min_fee,
                ];
                if ($objGatewayFee->type_id == 2) {
                    //1: phí cố định in
                    $intGatewayFee += $objGatewayFee->fee;

                } elseif ($objGatewayFee->type_id == 4) {
                    // 3: phí % in
                    $intGatewayFee += round($intAmount * $objGatewayFee->fee / 100, 3);
                }

                if ($intGatewayFee < $objGatewayFee->min_fee) {
                    $intGatewayFee = $objGatewayFee->min_fee;
                }
            }

            $intUserReferalFee = 0;
            $arrUserReferalFee = [];
            $objUserReferalFee = UserReferalFee::where('user_id', $intUserId)->whereIn('type_id', [2, 4])->first();
            if ($objUserReferalFee) {

                if ($objUserReferalFee->type_id == 2) {
                    //1: phí cố định in
                    $intUserReferalFee += $objUserReferalFee->fee;

                } elseif ($objUserReferalFee->type_id == 4) {
                    // 3: phí % in
                    if (!empty($objUserReferalFee->fee)) {
                        if (strtotime($objUserWidthDraw->created_at) < strtotime('2025-10-01 00:00:00')) {
                            $objUserReferalFee->fee = 0.1;
                        }
                    }
                    $intUserReferalFee += round($intAmount * $objUserReferalFee->fee / 100, 3);
                }

                if ($intUserReferalFee < $objUserReferalFee->min_fee) {
                    $intUserReferalFee = $objUserReferalFee->min_fee;
                }

                $arrUserReferalFee = [
                    "type_id" => $objUserReferalFee->type_id,
                    "fee" => $objUserReferalFee->fee,
                    "min_fee" => $objUserReferalFee->min_fee,
                ];
            }


            $intUserFee = $objUserWidthDraw->fee;
            $objUserWidthDraw->fee_estimate = $intUserFeeEstimate;
            $objUserWidthDraw->amount_estimate_after_fee = $intAmountEstimateAfterFee;
            $objUserWidthDraw->user_fee_estimate_json = json_encode($arrUserFeeEstimate);
            $objUserWidthDraw->referal_fee_json = json_encode($arrUserReferalFee);
            $objUserWidthDraw->gateway_fee = $intGatewayFee;
            $objUserWidthDraw->gateway_fee_json = json_encode($arrGatewayFee);
            $objUserWidthDraw->referal_fee = $intUserReferalFee;
            $objUserWidthDraw->profit = $intUserFee - ($intGatewayFee);
            $objUserWidthDraw->profit_estimate = $intUserFeeEstimate - ($intGatewayFee);
            $objUserWidthDraw->save();
            $this->info($objUserWidthDraw->profit);
        }

    }


    public function backupDatabase()
    {
        $strAppName = env('APP_NAME');
        $strHost = env('DB_HOST');
        $strDbName = env('DB_DATABASE');
        $strDbUser = env('DB_USERNAME');
        $strDbPassword = env('DB_PASSWORD');
        $strPathBackup = base_path("../backup");
        if (!file_exists($strPathBackup)) {
            mkdir($strPathBackup, 0755, true);
        }

        $strFileName = date('Y_m_d_H_i') . "_" . $strDbName . "_" . env('APP_WEBSITE') . ".sql";
        $strFilePath = "$strPathBackup/" . $strFileName;

        $strcolumnStatistic = "";
        if (env('APP_ENV_SOURCE') == "docker") {
            $strcolumnStatistic = "--column-statistics=0";
        }

        $this->info("mysqldump $strcolumnStatistic -h {$strHost} -u {$strDbUser} -p{$strDbPassword} {$strDbName} > {$strFilePath}");
        shell_exec("mysqldump $strcolumnStatistic -h {$strHost} -u {$strDbUser} -p{$strDbPassword} {$strDbName} > {$strFilePath}");

        shell_exec("zip -j {$strFilePath}.zip $strFilePath");
        shell_exec("rm $strFilePath");
        $strUrl = (new S3ClloudBizStorage())->putPrivate("database", "{$strFilePath}.zip", "$strAppName/" . date("Y") . "/" . date("m") . "/$strFileName");
        dump($strUrl);
        if (!empty($strUrl)) {
            shell_exec("rm {$strFilePath}.zip");
        }

        \App\Jobs\TelegramNotificationJob::dispatch([
            'subject' => "BACKUP DB",
            'message' => "Backup thành công URL Tải $strUrl",
            'bot_token' => "5662588556:AAF9_L4fo2HS2z7rr6i9z2MIHd4oukjdniU",
            'chat_id' => "-1001830429242"
        ])->onQueue('notification');
    }
}