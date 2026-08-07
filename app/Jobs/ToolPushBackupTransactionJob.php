<?php

namespace App\Jobs;

use App\Models\User;
use App\Utilities\Telegram;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ToolPushBackupTransactionJob implements ShouldQueue
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
            return \Log::error("ToolPushBackupTransactionJob: không tìm thấy dữ liệu");
        }
       
        $toolService = new \App\Services\ToolService();
        $toolService->pushTransaction($this->arrData);
    }
    public function failed($exception)
    {
        return \Log::error("ToolPushBackupTransactionJob: " . $exception->getMessage());
    }
}
