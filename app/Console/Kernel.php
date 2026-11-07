<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // Schedule the SendBirthdayMessagesJob to run daily at midnight
        // $schedule->job(new SendBirthdayMessagesJob)->everyMinute();

        // Dispatch message processing every minute
        // $schedule->call(function () {
        //     DispatchMessagesJob::dispatch(100); // Process batch of 100 messages every minute
        // })->everyThirtyMinutes();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
