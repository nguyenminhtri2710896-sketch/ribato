<?php

namespace App\Jobs;

use App\Services\GatewayForwardService;
use App\Services\WithdrawGpayLogService;
use App\Services\WithdrawYoobilLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GatewayForwardJob implements ShouldQueue
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
            return \Log::error("GatewayForwardJob: không tìm thấy dữ liệu");
        }

        $arrParams      = $this->arrData["params"] ?? [];
        $strDest        = $this->arrData["dest"] ?? "";
        $strUrlForwad = $this->arrData["url_forward"] ?? "";
        $gatewayService = new GatewayForwardService();
        /**
         * @var 
         * Tuỳ strDest sẽ xử lý khác nhau
         */
        $resultSend     = $gatewayService->sendRibato([
            "params" => $arrParams,
            "url_forward" => $strUrlForwad
        ]);

        if ($resultSend["error_code"] ?? 999 != 0) {
            \Log::error("GatewayForwardJob: " . json_encode($resultSend));
            return;
        }
    }

    public function failed($exception)
    {
        return \Log::error("GatewayForwardJob: " . $exception->getMessage());
    }
}
