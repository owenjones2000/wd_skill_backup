<?php

namespace App\Console\Commands;

use App\helpers\AliyunOss;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skill-backup {app?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test';
    protected $backupConfig = [];

    protected $backup_dir = '/home/backup/mysql/';

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
     * @return mixed
     */
    public function handle()
    {
        //
        $appname = $this->argument('app');
        $this->backupConfig =  config('backup');
        $this->date =  Carbon::now()->format('Y-m-d');
        dump($this->date);
        if ($appname) {
            if (isset($this->backupConfig[$appname])) {
                $this->mysqlBackup($this->backupConfig[$appname]);
            }
        }
    }



    public function mysqlBackup($app)
    {
        $this->localBackup($app);
        $this->aliyunBackup($app);
    }

    public function localBackup($app)
    {
        $dir = Storage::disk('local')->path($app['dir'] . $this->date) . '/';
        $user = $app['user'];
        $host = $app['host'];
        $password = $app['password'];
        $database = $app['database'];
        $table = $app['table'];
        $tables = implode(' ', $table);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $file = $dir . $app['name'] . '.sql';
        $nodatafile = $dir . $app['name'] . '-no-data.sql';
        // dd("mysqldump -F -u$user -h$host -p$password $database > $file");
        $fp = popen("mysqldump -F -u$user -h$host -p$password -B $database --tables $tables> $file", "r");
        $fp = popen("mysqldump -F -d -u$user -h$host -p$password -B $database > $nodatafile", "r");

        $rs = '';
        while (!feof($fp)) {
            $rs .= fread($fp, 1024);
        }
        pclose($fp);

        dump($rs);
    }

    public function aliyunBackup($app)
    {
        $localDir = Storage::disk('local')->path($app['dir'] . $this->date) . '/';
        $buketDir = $app['dir'] . $this->date;
        $ossClient = AliyunOss::getClient();
        $uploadDir = AliyunOss::uploadDir($ossClient, $buketDir, $localDir);
        if (!$uploadDir){
            Log::info('aliyun back fail');
        }
        dump($uploadDir);
        
    }
}
