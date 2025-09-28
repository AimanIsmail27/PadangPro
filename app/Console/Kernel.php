<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // Register your command class here
        \App\Console\Commands\CancelExpiredRentals::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Runs the rental expiration check every minute
        $schedule->command('rental:cancel-expired')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
