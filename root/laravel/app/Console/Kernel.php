<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\cronController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
/*
        $schedule->call( function(){
            echo 'setNewTideFiles：' . date('Y-m-d H:i:s').' ';
            $year = date('Y') + 1;
            cronController::setNewTideFiles($year);
        })
            ->monthly();

        $schedule->call( function(){
            echo 'saveTideData：' . date('Y-m-d H:i:s').' ';
            $year = date('Y') + 1;
            cronController::saveTideData($year);
        })
//            ->everyMinute();
            ->everyTenMinutes();
*/
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
