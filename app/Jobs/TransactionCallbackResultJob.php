<?php

namespace App\Jobs;

use App\Services\TransactionService;
use App\Utilities\Telegram;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TransactionCallbackResultJob implements ShouldQueue
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
            return \Log::error("TransactionCallbackResultJob: không tìm thấy dữ liệu");
        }

        if (empty($this->arrData["id"])) {
            return \Log::error("TransactionCallbackResultJob: Thiếu thông tin");
        }

        $intId              = $this->arrData["id"];
        $transactionService = new TransactionService();
        $resultTransaction  = $transactionService->callbackResultTransaction([
            "transaction_id" => $intId
        ]);

        if ($resultTransaction["error_code"] != 0) {
            \Log::error("TransactionCallbackResultJob: " . json_encode($resultTransaction));
        }

        if ($resultTransaction["error_code"] == 809) {
            /**
             * Callback lại
             */
            sleep(30);
            dispatch(new TransactionCallbackResultJob([
                'id' => $intId,
            ]))->onQueue('callback');

        }

    }

    public function failed($exception)
    {
        return \Log::error("TransactionCallbackResultJob: " . $exception->getMessage());
    }
}
