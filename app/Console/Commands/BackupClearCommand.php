<?php

namespace App\Console\Commands;

use App\helpers\Functions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skill-backup-clear {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->argument('date')?Carbon::parse($this->argument('date'))->format('Y-m-d') : Carbon::now()->subMonths()->format('Y-m-d');
        Log::info('clear start'. $date);
        dump($date);

        $this->clearBackup($date);
        Log::info('clear end' . $date);
    }

    public function clearBackup($date)
    {
        $backupConfig =  config('backup');
        
        foreach ($backupConfig as $key => $app) {
            $localDir = Storage::disk('local')->path($app['dir'] . $date) . '/';
            Functions::deleteDir($localDir);
        }


    }
}
