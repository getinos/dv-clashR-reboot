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
        // Run the battleground tick once every second.
        $schedule->command('battleground:tick')->everySecond();

        // Process auctions ended by their configured ends_at.
        $schedule->command('auction:process-end')->everySecond();
    }
}
