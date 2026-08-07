<?php

namespace App\Jobs;

use App\Services\WithdrawPaymenthotLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class WithdrawPaymenthotV2Job implements ShouldQueue
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
            return \Log::error("WithdrawPaymenthotV2Job: không tìm thấy dữ liệu");
        }

        if (empty($this->arrData["id"])) {
            return \Log::error("WithdrawPaymenthotV2Job: Thiếu thông tin");
        }

        $intId                  = $this->arrData["id"];
        $withdrawGpayLogService = new WithdrawPaymenthotLogService();
        $resultCreateRequest    = $withdrawGpayLogService->createRequestV2([
            "user_withdraw_id" => $intId
        ]);

        if ($resultCreateRequest["error_code"] != 0) {
            \Log::error("WithdrawPaymenthotV2Job: " . json_encode($resultCreateRequest));
            return;
        }
    }

    public function failed($exception)
    {
        return \Log::error("WithdrawPaymenthotV2Job: " . $exception->getMessage());
    }
}
