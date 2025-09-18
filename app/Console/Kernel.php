<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected function scheduleTimezone(): string
    {
        return 'Asia/Jakarta';
    }

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            \Log::info('[Scheduler] start doResetNonaktif at ' . now()->toDateTimeString());
            // atau echo supaya Heroku Scheduler output kelihatan langsung
            echo "[Scheduler] start doResetNonaktif at " . now()->toDateTimeString() . PHP_EOL;

            $deleted = app(\App\Http\Controllers\SubmissionController::class)->doResetNonaktif();

            \Log::info("[Scheduler] doResetNonaktif deleted: " . ($deleted ?? 'n/a'));
            echo "[Scheduler] doResetNonaktif deleted: " . ($deleted ?? 'n/a') . PHP_EOL;
        })->daily(); // atau dailyAt('00:00') saat sudah yakin
    }

    
    // protected function schedule(Schedule $schedule)
    // {
    //     $schedule->call(function () {
    //         \Log::info('Tes scheduler jalan: ' . now());
    //         app(\App\Http\Controllers\SubmissionController::class)->doResetNonaktif();
    //     })->dailyAt('00:00');
    // }



    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
