<?php

namespace App\Console\Commands;

use App\helpers\AliyunOss;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\File;
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
        
        $local = $this->localBackup($app);
        if ($local){
            $aliUpload = $this->aliyunBackup($app);
            $awsUpload = $this->awsBackup($app);
            dump($aliUpload, $awsUpload);
        }
        
    }

    public function localBackup($app)
    {
        try{
            $this->localDir = Storage::disk('local')->path($app['dir'] . $this->date) . '/';
            $user = $app['user'];
            $host = $app['host'];
            $password = $app['password'];
            $database = $app['database'];
            $table = $app['table'];
            $tables = implode(' ', $table);
            if (!is_dir($this->localDir)) {
                mkdir($this->localDir, 0777, true);
            }
            $this->file =  $app['name'] . Carbon::now()->format('-Y-m-d-H-i-s') . '.sql';
            $this->nodatafile = $app['name'] . '-no-data' . Carbon::now()->format('-Y-m-d-H-i-s') . '.sql';
            $this->filePath = $this->localDir . $app['name'] . Carbon::now()->format('-Y-m-d-H-i-s') . '.sql';
            $this->nodatafilePath = $this->localDir . $app['name'] . '-no-data' . Carbon::now()->format('-Y-m-d-H-i-s') . '.sql';
            // if (empty($database) || empty($tables) )
            // dd("mysqldump -F -u$user -h$host -p$password $database > $file");
            $fp = popen("mysqldump --column-statistics=0 --set-gtid-purged=off --single-transaction --quick -u$user -h$host -p$password -B $database --tables $tables> {$this->filePath} ", "r");
            $fp = popen("mysqldump --column-statistics=0 --set-gtid-purged=off -d -u$user -h$host -p$password -B $database > {$this->nodatafilePath}", "r");

            $rs = '';
            while (!feof($fp)) {
                $rs .= fread($fp, 1024);
            }
            pclose($fp);
            if (!file_exists($this->filePath) || !file_exists($this->nodatafilePath)){
                dump("backup {$app['name']} mysql fail ---" . $rs);
                throw new Exception("backup {$app['name']} mysql fail ---". $rs);
            }
            
            return true;
        } catch (Exception $e) {
            Log::error($e);
            dump($e);
        }
        return false;
    }

    public function aliyunBackup($app)
    {
        try{
            // $this->localDir = Storage::disk('local')->path($app['dir'] . $this->date) . '/';
            // $buketDir = $app['dir'] . $this->date;
            $ossClient = AliyunOss::getClient();
            // $uploadDir = AliyunOss::uploadDir($ossClient, $buketDir, $localDir);
            $this->cl_file = $app['dir'] . $this->date . '/' . $this->file;
            $this->cl_nodatafile = $app['dir'] . $this->date . '/' . $this->nodatafile;
            $uploadFile = AliyunOss::uploadFile($ossClient, $this->cl_file, $this->filePath);
            $uploadNodatafile = AliyunOss::uploadFile($ossClient, $this->cl_nodatafile, $this->nodatafilePath);
            return [$uploadFile, $uploadNodatafile];
        } catch (Exception $e) {
            Log::error($e);     
            dump($e);
        }
        return false;
    }

    public function awsBackup($app)
    {
        try{
            // $uploadFile = Storage::disk('s3')->put($this->cl_file, file_get_contents($this->filePath));
            $uploadFile = Storage::disk('s3')->putFileAs($app['dir'] . $this->date, new File($this->filePath), $this->file);
            // $uploadNodatafile = Storage::disk('s3')->put($this->cl_nodatafile, file_get_contents($this->nodatafilePath));
            $uploadNodatafile = Storage::disk('s3')->putFileAs($app['dir'] . $this->date, new File($this->nodatafilePath), $this->nodatafile);
            return [$uploadFile, $uploadNodatafile];
        } catch (Exception $e) {
            Log::error($e);
            dump($e); 
        }
        return false;
    }
}
