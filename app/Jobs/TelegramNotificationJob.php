<?php

namespace App\Jobs;

use App\Models\User;
use App\Utilities\Telegram;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TelegramNotificationJob implements ShouldQueue
{

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $arrData = [];
    public function __construct($arrData = [])
    {
        $this->arrData = $arrData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public $timeout = 200;

    public function handle()
    {
        if (empty($this->arrData)) {
            return \Log::error("TelegramNotificationJob: không tìm thấy dữ liệu");
        }

        if (empty($this->arrData["message"])) {
            return \Log::error("TelegramNotificationJob: Thiếu thông tin");
        }
        $strMessage  = $this->arrData["message"];
        $strBotToken = $this->arrData["bot_token"] ?? "6443370442:AAFfryL7MA_xcKbLlPnaAYlMWGBQ9Ua-nO4";
        $strType     = $this->arrData["type"] ?? "";
        $strChatId   = $this->arrData["chat_id"] ?? "";
        $intUserId   = $this->arrData["user_id"] ?? "";
        if ($strType == "notification") {
            $strChatId = "-4075327717";
        }

        if ($strType == "error") {
            $strChatId = "-4043999646";
        }

        if (!empty($intUserId)) {
            $objUser = User::where('id', $intUserId)->first();
            if ($objUser) {
                $strChatId = $objUser->telegram_id_notification;
            }
        }

        if (empty($strChatId)) {
            return;
        }

        $telegram = new Telegram();
        $telegram->setToken($strBotToken)->sendMessage([
            "chat_id" => $strChatId,
            "message" => $strMessage
        ]);
    }
    public function failed($exception)
    {
        return \Log::error("TelegramNotificationJob: " . $exception->getMessage());
    }
}
