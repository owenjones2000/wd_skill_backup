<?php

namespace App\Console;

use App\Console\Commands\BackupClearCommand;
use App\Console\Commands\BackupCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        // $schedule->command('inspire')->hourly();
        
        $schedule->command(BackupCommand::class,['solitairearena'])->dailyAt('9:00')->runInBackground();
        $schedule->command(BackupCommand::class,['bingowinner'])->dailyAt('9:05')->runInBackground();
        $schedule->command(BackupCommand::class,['bingogo'])->dailyAt('9:10')->runInBackground();
        $schedule->command(BackupCommand::class,['bingosmash'])->dailyAt('9:20')->runInBackground();
        $schedule->command(BackupCommand::class, ['bingoforcash'])->dailyAt('9:30')->runInBackground();
        $schedule->command(BackupClearCommand::class)->dailyAt('8:00')->runInBackground();
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
