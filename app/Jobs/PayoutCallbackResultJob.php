<?php

namespace App\Jobs;

use App\Services\TransactionService;
use App\Services\UserWithdrawService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PayoutCallbackResultJob implements ShouldQueue
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
            return \Log::error("PayoutCallbackResultJob: không tìm thấy dữ liệu");
        }

        if (empty($this->arrData["id"])) {
            return \Log::error("PayoutCallbackResultJob: Thiếu thông tin");
        }

        $intId               = $this->arrData["id"];
        $userWithdrawService = new UserWithdrawService();
        $callbackResult      = $userWithdrawService->callbackResult([
            "user_withdraw_id" => $intId
        ]);

        if ($callbackResult["error_code"] != 0) {
            \Log::error("PayoutCallbackResultJob: " . json_encode($callbackResult));
        }

        if ($callbackResult["error_code"] == 809) {
            /**
             * Callback lại
             */
            sleep(30);
            dispatch(new PayoutCallbackResultJob([
                'id' => $intId,
            ]))->onQueue('callback');

        }

    }

    public function failed($exception)
    {
        return \Log::error("PayoutCallbackResultJob: " . $exception->getMessage());
    }
}
