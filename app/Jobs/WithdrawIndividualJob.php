<?php

namespace App\Jobs;

use App\Services\UserWithdrawService;
use App\Services\WithdrawYoobilLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class WithdrawIndividualJob implements ShouldQueue
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
            return \Log::error("WithdrawIndividualJob: không tìm thấy dữ liệu");
        }

        if (empty($this->arrData["id"])) {
            return \Log::error("WithdrawIndividualJob: Thiếu thông tin");
        }

        $intId = $this->arrData["id"];
        $userWithdrawServide = new UserWithdrawService();
        $resultCreateRequest = $userWithdrawServide->withdrawIndividual([
            "user_withdraw_id" => $intId
        ]);

        if ($resultCreateRequest["error_code"] != 0) {
            \Log::error("WithdrawIndividualJob: " . json_encode($resultCreateRequest));
            return;
        }
    }


    public function failed($exception)
    {
        return \Log::error("WithdrawIndividualJob: " . $exception->getMessage());
    }
}
