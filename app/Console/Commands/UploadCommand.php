<?php

namespace App\Console\Commands;

use App\helpers\AliyunOss;
use App\helpers\Functions;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skill-upload {app?} {action?}';

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
        $action = $this->argument('action');
        $this->backupConfig =  config('backup');
        $this->date =  Carbon::now()->format('Y-m-d');
        dump($this->date, $appname, $action);
        Log::info('upload start');
        if ($appname) {
            if (isset($this->backupConfig[$appname])) {
                if ($action == 'aliyun') {
                    $aliUpload = $this->aliyunBackup($this->backupConfig[$appname]);
                }elseif($action == 'aws'){
                    $awsUpload = $this->awsBackup($this->backupConfig[$appname]);
                } elseif ($action == 'google') {
                    $googleUpload = $this->googleBackup($this->backupConfig[$appname]);
                }
            }
        }
        Log::info('upload end');
    }


    // public function aliyunBackup($app)
    // {
    //     try{
    //         $ossClient = AliyunOss::getClient();
    //         $this->cl_file = $app['dir'] . $this->date . '/' . $this->file;
    //         $this->cl_nodatafile = $app['dir'] . $this->date . '/' . $this->nodatafile;
    //         $uploadFile = AliyunOss::uploadFile($ossClient, $this->cl_file, $this->filePath);
    //         $uploadNodatafile = AliyunOss::uploadFile($ossClient, $this->cl_nodatafile, $this->nodatafilePath);
    //         return [$uploadFile, $uploadNodatafile];
    //     } catch (Exception $e) {
    //         Log::error($e);     
    //         dump($e);
    //     }
    //     return false;
    // }
    // public function aliyunBackup($app)
    // {
    //     try {
    //         $this->localDir = Storage::disk('local')->path($app['dir'] . $this->date) . '/';
    //         $buketDir = $app['dir'] . $this->date;
    //         $uploadDir = false;
    //         if (is_dir($this->localDir)) {
    //             $ossClient = AliyunOss::getClient();
    //             $uploadDir = AliyunOss::uploadDir($ossClient, $buketDir, $this->localDir);
    //         }
    //         dump($uploadDir);
    //         return $uploadDir;
    //     } catch (Exception $e) {
    //         Log::error($e);
    //         dump($e);
    //     }
    //     return false;
    // }
    public function aliyunBackup($app)
    {
        try {
            $this->localDir = Storage::disk('local')->path($app['dir'] . $this->date) . '/';
            $buketDir = $app['dir'] . $this->date;
            $ossClient = AliyunOss::getClient();
            if (is_dir($this->localDir)) {
                $dirs = scandir($this->localDir);
                foreach ($dirs as $dir) {
                    if ($dir != '.' && $dir != '..') {
                        $sonDir = $this->localDir . $dir;
                        if (!is_dir($sonDir)) {

                            $uploadFile = AliyunOss::uploadFile($ossClient, $app['dir'] . $this->date.'/'. pathinfo($sonDir)['basename'], $sonDir);
                            dump($uploadFile);
                            Log::info($uploadFile);
                        }
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            Log::error($e);
            dump($e);
        }
        return false;
    }

    // public function awsBackup($app)
    // {
    //     try{
    //         // $uploadFile = Storage::disk('s3')->put($this->cl_file, file_get_contents($this->filePath));
    //         $uploadFile = Storage::disk('s3')->putFileAs($app['dir'] . $this->date, new File($this->filePath), $this->file);
    //         // $uploadNodatafile = Storage::disk('s3')->put($this->cl_nodatafile, file_get_contents($this->nodatafilePath));
    //         $uploadNodatafile = Storage::disk('s3')->putFileAs($app['dir'] . $this->date, new File($this->nodatafilePath), $this->nodatafile);
    //         return [$uploadFile, $uploadNodatafile];
    //     } catch (Exception $e) {
    //         Log::error($e);
    //         dump($e); 
    //     }
    //     return false;
    // }
    public function awsBackup($app)
    {
        try {
            $this->localDir = Storage::disk('local')->path($app['dir'] . $this->date) . '/';
            $buketDir = $app['dir'] . $this->date;

            if (is_dir($this->localDir)) {
                $dirs = scandir($this->localDir);
                foreach ($dirs as $dir) {
                    if ($dir != '.' && $dir != '..') {
                        $sonDir = $this->localDir . $dir;
                        if (!is_dir($sonDir)) {
                            $uploadFile = Storage::disk('s3')->putFileAs($app['dir'] . $this->date, new File($sonDir), pathinfo($sonDir)['basename']);
                            dump($uploadFile);
                            Log::info($uploadFile);
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($e);
            dump($e);
        }
        return false;
    }

    public function googleBackup($app)
    {
        try {
            $this->localDir = Storage::disk('local')->path($app['dir'] . $this->date) . '/';
            $buketDir = $app['dir'] . $this->date;
            if (is_dir($this->localDir)) {
                $dirs = scandir($this->localDir);
                foreach ($dirs as $dir) {
                    if ($dir != '.' && $dir != '..') {
                        $sonDir = $this->localDir . $dir;
                        if (!is_dir($sonDir)) {
                            $uploadFile = Storage::disk('gcs')->putFileAs($app['dir'] . $this->date, new File($sonDir), pathinfo($sonDir)['basename']);
                            dump($uploadFile);
                            Log::info($uploadFile);
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($e);
            dump($e);
        }
        return false;
    }
}
