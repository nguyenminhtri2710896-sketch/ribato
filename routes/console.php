<?php

use App\Console\Commands\Transaction;
use Illuminate\Console\Scheduling\Schedule;

// use Illuminate\Foundation\Inspiring;
// use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// <?php

// use App\Console\Commands\ContentGenerateKeyword;
// use App\Console\Commands\FadoQuotation;
// use App\Console\Commands\GenerateKeyword;
// Schedule::command(Transaction::class, ['--type=for-control-by-day'])->withoutOverlapping()->runInBackground()->cron('8 2 * * *');
// Schedule::command(Transaction::class, ['--type=for-control-by-gpay'])->withoutOverlapping()->runInBackground()->cron('30 15 * * *');

// 8 2 * * * php /data/www/vnpay.biz/public_html/artisan transaction --type=for-control-by-day
// 30 15 * * * php /data/www/vnpay.biz/public_html/artisan transaction --type=for-control-by-gpay
// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');
