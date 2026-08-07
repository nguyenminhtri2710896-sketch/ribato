<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('transaction --type=for-control-by-day')->withoutOverlapping()->runInBackground()->cron('8 2 * * *');
        $schedule->command('transaction --type=for-control-by-gpay')->withoutOverlapping()->runInBackground()->cron('30 15 * * *');
        $schedule->command('transaction --type=for-control-by-gpayribato')->withoutOverlapping()->runInBackground()->cron('30 15 * * *');
        $schedule->command('tool --type=sync-gateway-account-balance')->withoutOverlapping()->runInBackground()->cron('1 */1 * * *');
        $schedule->command('tool --type=backup-database')->withoutOverlapping()->runInBackground()->cron('1 0 * * *');
        $schedule->command('user:sync-revenue-report')->withoutOverlapping()->runInBackground()->everyTenMinutes();
        $schedule->command('tool --type=sync-amount-pendding')->withoutOverlapping()->runInBackground()->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}


