<?php

namespace App\Jobs;

use App\Models\UserWithdraw;
use App\Services\UserWithdrawService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Chỉ dùng cho môi trường sandbox: giả lập kết quả rút tiền ngẫu nhiên
 * (thành công / thất bại) để có dữ liệu test IPN payout.
 */
class SandboxWithdrawResultJob implements ShouldQueue
{

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    private $arrData = [];
    public function __construct($arrData = [])
    {
        $this->arrData = $arrData;
    }

    public $timeout = 200;

    public function handle()
    {
        if (!app()->environment('sandbox')) {
            return;
        }

        if (empty($this->arrData["id"])) {
            return \Log::error("SandboxWithdrawResultJob: Thiếu thông tin");
        }

        $objUserWithdraw = UserWithdraw::where('id', $this->arrData["id"])->first();
        if (!$objUserWithdraw || $objUserWithdraw->status_id != 1) {
            return;
        }

        $isSuccess = rand(0, 1) == 1;

        $result = (new UserWithdrawService())->changeStatus([
            'id'        => $objUserWithdraw->id,
            'status_id' => $isSuccess ? 2 : 3,
            'note'      => $isSuccess ? 'Sandbox: giao dịch thành công' : 'Sandbox: giao dịch thất bại',
        ]);

        if ($result["error_code"] != 0) {
            \Log::error("SandboxWithdrawResultJob: " . json_encode($result));
        }
    }

    public function failed($exception)
    {
        return \Log::error("SandboxWithdrawResultJob: " . $exception->getMessage());
    }
}
