<?php

namespace App\Console\Commands;

use App\helpers\AliyunOss;
use App\helpers\Functions;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
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
    protected $signature = 'skill-backup {app?} {action?}';

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
        Log::info('backup start');
        if ($appname) {
            if (isset($this->backupConfig[$appname])) {
                if ($action == 'aliyun') {
                    $aliUpload = $this->aliyunBackup($this->backupConfig[$appname]);
                } elseif ($action == 'aws') {
                    $awsUpload = $this->awsBackup($this->backupConfig[$appname]);
                } elseif ($action == 'google') {
                    $googleUpload = $this->googleBackup($this->backupConfig[$appname]);
                } elseif ($action == 'backup') {
                } elseif ($action == 'upload') {
                    $awsUpload = $this->awsBackup($this->backupConfig[$appname]);
                    $googleUpload = $this->googleBackup($this->backupConfig[$appname]);
                    $aliUpload = $this->aliyunBackup($this->backupConfig[$appname]);
                } elseif ($action == 'backup') {
                    $this->localBackup($this->backupConfig[$appname]);
                } else {
                    $this->mysqlBackup($this->backupConfig[$appname]);
                }
            }
        }
        Log::info('backup end');
    }



    public function mysqlBackup($app)
    {

        $local = $this->localBackup($app);
        if ($local) {
            // Artisan::call('skill-upload', ['app' => $app['name'], 'action'=>'aliyun']);
            // Artisan::call('skill-upload', ['app' => $app['name'], 'action'=>'aws']);
            // Artisan::call('skill-upload', ['app' => $app['name'], 'action'=>'google']);

            $awsUpload = $this->awsBackup($app);
            $googleUpload = $this->googleBackup($app);
            $aliUpload = $this->aliyunBackup($app);
        }
    }

    public function localBackup($app)
    {
        try {
            $this->localDir = Storage::disk('local')->path($app['dir'] . $this->date) . '/';
            $user = $app['user'];
            $host = $app['host'];
            $password = $app['password'];
            $database = $app['database'];
            $table = $app['table'];
            $tables = implode(' ', $table);
            if (!is_dir($this->localDir)) {
                mkdir($this->localDir, 0777, true);
            } else {
                Functions::deleteDirsFile($this->localDir);
            }
            $this->fileName =  $app['name'] . Carbon::now()->format('-Y-m-d-H-i-s');
            $this->file =  $app['name'] . Carbon::now()->format('-Y-m-d-H-i-s') . '.sql';
            $this->nodatafile = $app['name'] . '-no-data' . Carbon::now()->format('-Y-m-d-H-i-s') . '.sql';
            $this->filePath = $this->localDir . $app['name'] . Carbon::now()->format('-Y-m-d-H-i-s') . '.sql';
            $this->nodatafilePath = $this->localDir . $app['name'] . '-no-data' . Carbon::now()->format('-Y-m-d-H-i-s') . '.sql';
            // if (empty($database) || empty($tables) )
            // dd("mysqldump -F -u$user -h$host -p$password $database > $file");
            if (App::environment('local')) {
                if ($app['sub_table']) {
                    foreach ($table as $key => $value) {
                        $filePath = $this->localDir . $value . Carbon::now()->format('-Y-m-d-H-i-s') . '.sql';
                        $fp = popen("mysqldump  --set-gtid-purged=off --single-transaction --quick -u$user -h$host -p$password -B $database --tables $value> {$filePath} ", "r");
                    }
                } else {
                    $fp = popen("mysqldump   --set-gtid-purged=off --single-transaction --quick -u$user -h$host -p$password -B $database --tables $tables> {$this->filePath} ", "r");
                    $fp = popen("mysqldump   --set-gtid-purged=off -d -u$user -h$host -p$password -B $database > {$this->nodatafilePath}", "r");
                    $fp = popen("cd  {$this->localDir} && tar -czvf {$this->fileName}.tar.gz {$this->file} 2>&1 && rm {$this->filePath}", "r");
                }
            } else {
                if ($app['sub_table']) {
                    foreach ($table as $key => $value) {
                        $filePath = $this->localDir . $value . Carbon::now()->format('-Y-m-d-H-i-s') . '.sql';
                        $fp = popen("mysqldump  --column-statistics=0 --set-gtid-purged=off --single-transaction --quick -u$user -h$host -p$password -B $database --tables $value> {$filePath} ", "r");
                    }
                } else {
                    $fp = popen("mysqldump  --column-statistics=0 --set-gtid-purged=off --single-transaction --quick -u$user -h$host -p$password -B $database --tables $tables> {$this->filePath} ", "r");
                    $fp = popen("mysqldump  --column-statistics=0 --set-gtid-purged=off -d -u$user -h$host -p$password -B $database > {$this->nodatafilePath}", "r");
                    $fp = popen("cd  {$this->localDir} && tar -czvf {$this->fileName}.tar.gz {$this->file} 2>&1 && rm {$this->filePath}", "r");
                }
            }


            $rs = '';
            while (!feof($fp)) {
                $rs .= fread($fp, 1024);
            }
            pclose($fp);
            // if (!file_exists($this->filePath) || !file_exists($this->nodatafilePath)) {
            //     dump("backup {$app['name']} mysql fail ---" . $rs);
            //     throw new Exception("backup {$app['name']} mysql fail ---" . $rs);
            // }

            return true;
        } catch (Exception $e) {
            Log::error($e);
            dump($e);
        }
        return false;
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

                            $uploadFile = AliyunOss::uploadFile($ossClient, $app['dir'] . $this->date . '/' . pathinfo($sonDir)['basename'], $sonDir);
                            dump($uploadFile);
                            if ($uploadFile) {
                                $size = Functions::formatSize(filesize($sonDir));
                                $message =  $this->successMessage($app, 'Aliyun', $size, $app['dir'] . $this->date . '/' . pathinfo($sonDir)['basename']);
                                $this->sendTextMessage($message);
                            }
                            Log::info($uploadFile);
                        }
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            Log::error($e);
            dump($e->getMessage());
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
                            if ($uploadFile) {
                                $size = Functions::formatSize(filesize($sonDir));
                                $message =  $this->successMessage($app, 'AWS', $size, $uploadFile);
                                $this->sendTextMessage($message);
                            }
                            Log::info($uploadFile);
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($e);
            dump($e->getMessage());
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
                            if ($uploadFile) {
                                $size = Functions::formatSize(filesize($sonDir));
                                $message =  $this->successMessage($app, 'Google', $size, $uploadFile);
                                $this->sendTextMessage($message);
                            }
                            Log::info($uploadFile);
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($e);
            dump($e->getMessage());
        }
        return false;
    }

    protected  function successMessage($app, $cloud, $size, $path)
    {
        $str = <<<heredoc
        电竞备份 \n
        {$app['name']} 上传 $cloud 完成 \n
        路径 $path \n
        大小 $size \n
        heredoc;
        return $str;
    }
 
    protected  function sendTextMessage($content = '', $at_all = false)
    {

        $data = [
            'msgtype' => 'text',
            'text'    => [
                'content' => $content
            ],
        ];
        if ($at_all) {
            $data['text']['mentioned_list'] = ['@all'];
        }
        $client = new Client();
        $client->request("POST", "https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=4f6840d0-7a7d-4d3f-ab4d-d9d085941909", [
            "json" => $data,
        ]);
    }
}
