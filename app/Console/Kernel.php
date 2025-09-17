<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected function scheduleTimezone()
    {
        return 'Asia/Jakarta';
    }

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            \Log::info('Tes scheduler jalan: ' . now());
            app(\App\Http\Controllers\SubmissionController::class)->doResetNonaktif();
        })->daily(); 
    }



    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
