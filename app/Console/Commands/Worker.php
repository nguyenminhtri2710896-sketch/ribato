<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Worker extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worker {action=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'worker {action=default}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $arrWorkerList = [];

    public function __construct()
    {
        parent::__construct();
        $this->arrWorkerList = [
            // "tools-compress-user-image" => base_path() . '/artisan tools --type=compress-user-image',

        ];
    }

    // tools --type=check-image-upload

    /**topup-v2 --type=card-calculate
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        switch ($this->argument('action')) {
            case 'stop':
                $this->killAll();
                break;
            case 'restart':
                $this->restart();
                break;
            default:
                $this->all();
        }
        exit();
    }

    public function restart()
    {
        $this->killAll();
        $this->all();
    }

    public function all()
    {
        foreach ($this->arrWorkerList as $key => $strWorker) {
            $PID = [];
            exec("ps -auxwww | grep -v grep | grep '" . $strWorker . "' | awk '{ print $2 }'", $PID);
            if (empty($PID)) {
                shell_exec("php " . $strWorker . " >> " . base_path() . "/../logs/$key.log 2>&1 & echo $!");
                $this->info(date('Y-m-d H:i:s', time()) . " run success " . $strWorker);
            }

            if (!empty($PID)) {
                foreach ($PID as $num => $id) {
                    if ($num == 0) {
                        continue;
                    }
                    shell_exec("kill -9 " . $id);
                    $this->info(date('Y-m-d H:i:s', time()) . " Kill Process ID:" . $id . " " . $strWorker);
                }
            }
        }
        exit;
    }

    public function killAll()
    {
        foreach ($this->arrWorkerList as $key => $strWorker) {
            $PID = [];
            exec("ps -auxwww | grep -v grep | grep '" . $strWorker . "' | awk '{ print $2 }'", $PID);
            if (!empty($PID)) {
                foreach ($PID as $id) {
                    shell_exec("kill -9 " . $id);
                    $this->info(date('Y-m-d H:i:s', time()) . " Kill Process ID:" . $id . " " . $strWorker);
                }
            }
        }
    }
}
