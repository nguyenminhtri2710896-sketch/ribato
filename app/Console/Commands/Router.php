<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;


class Router extends Command
{

    use PrependsOutput,
        PrependsTimestamp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'router {--type=default} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'router {--type=default}';

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
            case 'render-url':
                $this->renderUrl();
                break;
            default:
                $this->error("Không tìm thấy --type=$type");
        }
    }

    public function renderUrl()
    {
        $routeCollection = \Illuminate\Support\Facades\Route::getRoutes();
        $varUrlJs = "";
        foreach ($routeCollection as $arrRouter) {
            if (empty($arrRouter->action["as"])) {
                continue;
            }
            if (empty($arrRouter->action["middleware"])) {
                continue;
            }

            $arrMiddleware = $arrRouter->action["middleware"];
            if (!in_array("api", $arrMiddleware)) {
                continue;
            }
            $strUrlRouter = Str::studly(str_replace(".", "_", $arrRouter->action["as"]));
            $strUrlRouter = "var strUrl" . $strUrlRouter . " = '" . route($arrRouter->action["as"]) . "';\n";
            $varUrlJs .= $strUrlRouter;
            $directoryPath = base_path('static/api/v1/js/');

            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }
            file_put_contents($directoryPath . env('APP_ENV') . '-url.js', $varUrlJs);
        }
    }
}
