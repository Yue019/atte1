<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    protected $commands = [
    \App\Console\Commands\SwitchWork::class, // コマンドを登録
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 毎日深夜24時（00:00）にSwitchWorkコマンドを実行
        $schedule->command('work:switch')->dailyAt('00:00');
    }
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');

    }
}
