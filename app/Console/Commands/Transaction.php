<?php

namespace App\Console\Commands;

use App\Services\TransactionService;
use App\Utilities\Telegram;
use Illuminate\Console\Command;


class Transaction extends Command
{

    use PrependsOutput,
        PrependsTimestamp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction {--type=default} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'transaction {--type=default}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->option('type');
        switch ($type) {
            case 'for-control-by-day':
                $this->forControlByDay();
                break;
            case 'for-control-by-gpay':
                $this->forControlByGpay();
                break;
            case 'for-control-by-gpayribato':
                $this->forControlByGpayRibato();
                break;
            case 'for-control-by-payment-hot':
                $this->forControlByPaymentHot();
                break;
            case 'test':
                $this->test();
                break;
            default:
                $this->error("Không tìm thấy --type=$type");
        }
    }

    public function forControlByDay()
    {
        $transactionService = new TransactionService();
        $this->info(date('Ymd', time()));
        $result = $transactionService->forControl(date('Ymd', time()));
        dump($result);
    }

    public function forControlByGpay()
    {
        $transactionService = new TransactionService();
        $this->info(date('Ymd', time()));
        $result = $transactionService->forControlByGpay(date('Ymd', time()));
        dump($result);
    }

    public function forControlByPaymentHot()
    {
        $transactionService = new TransactionService();
        $this->info(date('Ymd', time()));
        $result = $transactionService->forControlByPaymentHot(date('Ymd', time()));
        dump($result);
    }

    public function forControlByGpayRibato()
    {
        $transactionService = new TransactionService();
        $this->info(date('Ymd', time()));
        $result = $transactionService->forControlByGpayRibato(date('Ymd', time()));
        dump($result);
    }
    public function test()
    {
        $transactionService = new TransactionService();
        $this->info(date('Ymd', time()));
        $result = $transactionService->forControlByGpay(20240916);
        dump($result);
    }

}