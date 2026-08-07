<?php

namespace App\Services;

use App\Jobs\UpdateBalanceGatewayAccountJob;
use App\Models\BankYoobilMapping;
use App\Models\GatewayAccount;
use App\Models\Transaction;
use App\Models\Trash;
use App\Models\User;
use App\Models\UserBalance;
use App\Models\UserTransaction;
use App\Models\UserWithdraw;
use App\Models\UserYoobilConfig;
use App\Models\WithdrawYoobilLog;
use App\Utilities\General;
use App\Utilities\Yoobil;
use Illuminate\Support\Facades\Validator;

class ToolService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new WithdrawYoobilLog())->getFillable();
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Đang bảo trì'
        ],
        2 => [
            'name' => 'Hoạt động'
        ]
    ];

    public function pushTransaction($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "transaction_id" => "required",
            ],
            [

                "transaction_id.required" => __("Vui lòng nhập transaction_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intTransactionId = (int) $arrParams['transaction_id'];
        $objTransaction   = Transaction::where("id", $intTransactionId)->first();
        if (!$objTransaction) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Giao dịch không tồn tại.")]
            ])->result();
        }

        $objUserBalance = UserBalance::where("user_id", $objTransaction->user_id)->first();
        if (!$objUserBalance) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        $intAmountPending = Transaction::where('user_id', $objTransaction->user_id)
            ->where('status_id', 6)
            ->where('created_at', ">", date('Y-m-d H:i:s', time() - (60 * 60 * 24 * 4)))
            ->sum('amount_after_fee');


        $strMessage = "============== TRANSACTION ================\n" .
            "transaction_id: " . $objTransaction->id . "\n" .
            "amount: " . number_format($objTransaction->amount) . "đ\n" .
            "content: " . $objTransaction->content . "đ\n" .
            "status_id: " . $objTransaction->status_id . "\n" .
            "created_at: " . $objTransaction->created_at . "\n" .
            "user_id: " . $objTransaction->user_id . "\n" .
            "balance: " . number_format($objUserBalance->balance) . "đ\n" .
            "amount_pending: " . number_format($intAmountPending) . "đ\n";

        \App\Jobs\TelegramNotificationJob::dispatch([
            'message' => $strMessage,
            'type' => "notification-partner",
            'bot_token' => '8467082251:AAGjrE0jWRIq65KBfrlfirS-LNKeVT7vmM4',
            'chat_id' => "-4604931747"
        ])->onQueue('notification');


        return $this->setStatusCode(0)->setMessage(__("Tạo giao dịch thành công."))->setData([])->result();
    }

    public function pushWithdrawl($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "user_withdraw_id" => "required",
            ],
            [

                "user_withdraw_id.required" => __("Vui lòng nhập user_withdraw_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserWithdrawId = (int) $arrParams['user_withdraw_id'];
        $objUserWithdraw   = UserWithdraw::where("id", $intUserWithdrawId)->first();
        if (!$objUserWithdraw) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Giao dịch không tồn tại.")]
            ])->result();
        }

        $objUserBalance = UserBalance::where("user_id", $objUserWithdraw->user_id)->first();
        if (!$objUserBalance) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        $strMessage = "============== WITHDRAW================\n" .
            "user_withdraw_id: " . $objUserWithdraw->id . "\n" .
            "amount: " . number_format($objUserWithdraw->amount) . "đ\n" .
            "created_at: " . $objUserWithdraw->created_at . "\n" .
            "user_id: " . $objUserWithdraw->user_id . "\n" .
            "balance: " . number_format($objUserBalance->balance) . "đ\n";

        \App\Jobs\TelegramNotificationJob::dispatch([
            'message' => $strMessage,
            'type' => "notification-partner",
            'bot_token' => '8467082251:AAGjrE0jWRIq65KBfrlfirS-LNKeVT7vmM4',
            'chat_id' => "-4604931747"
        ])->onQueue('notification');



        return $this->setStatusCode(0)->setMessage(__("Tạo giao dịch thành công."))->setData([])->result();
    }


}