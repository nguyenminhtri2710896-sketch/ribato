<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserWithdrawService;
use App\Services\WithdrawYoobilLogService;
use App\Utilities\General;

class WithdrawYoobilLogController extends BaseController
{

    private $withdrawYoobilLogService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(WithdrawYoobilLogService $withdrawYoobilLogService)
    {
        $this->withdrawYoobilLogService = $withdrawYoobilLogService;
    }

    public function callback()
    {
        $arrParams = request()->all();
        \Log::info("DEBUG WithdrawYoobilLogController callback: " . json_encode($arrParams));
        return response()->json($this->withdrawYoobilLogService->updateCallback($arrParams));
    }

}