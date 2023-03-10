<?php

namespace App\Console\Commands;

use App\helpers\AliyunOss;
use Carbon\Carbon;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use OSS\Core\OssException;

use function PHPSTORM_META\type;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {function} {param1?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test';

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
        $p = $this->argument('function');
        $name = 'test' . $p;
        call_user_func([$this, $name]);
    }

    public function test1()
    {
        // $fp = popen('df -lh| grep -E "^(/)"', 'r');
        // $fp = shell_exec('top -b -n 1');dd($fp);
        $fp = popen('top -b -n 1|grep -E "(Cpu|KiB Mem)"', 'r');
        $rs = '';
        while (!feof($fp)) {
            $rs .= fread($fp, 1024);
        }
        $sys_info = explode("\n", $rs);
        $cpu_info = explode(",", $sys_info[0]);
        $cpu_us = trim(trim($cpu_info[0], '%Cpu(s): '), 'us'); //百分比
        $cpu_sy = trim($cpu_info[1], 'sy');
        $cpu_id = trim($cpu_info[3], 'sy');
        $mem_info = explode(",", $sys_info[1]); //
        $mem_total = trim(trim($mem_info[0], 'KiB Mem : '), ' total');
        $mem_free = trim(trim($mem_info[1], 'free'));
        $mem_used = trim(trim($mem_info[2], 'used'));
        // $mem_usage = round(100 * intval($mem_used) /     intval($mem_total), 2); //百分比
        // pclose($fp);
        // $sysInfo = explode("\n", $rs);
        dump($sys_info);
        // $logsize = filesize(storage_path('logs/laravel-'.date('Y-m-d').'.log'));
        // dump($logsize);
        dump($cpu_info);
        dump($mem_info);
        dump((float)$cpu_us);
        dump((float)$cpu_sy);
        dump((float)$cpu_id);
        dump((float)$mem_total);
        dump((float)$mem_free);
        dump((float)$mem_used);
    }

    public function test2()
    {
        $fp = popen('df -l | grep -E "^(/)"', "r");

        $rs = '';
        while (!feof($fp)) {
            $rs .= fread($fp, 1024);
        }
        pclose($fp);

        $rs = preg_replace('/\s{2,}/', ' ', $rs);

        $hd = explode(" ", $rs);
        $hd_size = trim($hd[1], 'G');
        $hd_used = trim($hd[2], 'G');
        $hd_avail = trim($hd[3], 'G');
        // $hd_usage = trim($hd[4], '%'); //挂载点 百分比
        dump($hd[1]);
        dump($hd[2]);
        dump($hd[3]);
        // dump($hd_used);
        // dump($hd_avail);
    }

    public function test3()
    {
        $fp = popen('mysqldump -F -u root -h 127.0.0.1 -pfd2f909a7c34a235 attributes > /home/backup/mysql/attr.sql', "r");

        $rs = '';
        while (!feof($fp)) {
            $rs .= fread($fp, 1024);
        }
        pclose($fp);

        dump($rs);
    }

    public function test4()
    {
        $ossClient = AliyunOss::getClient();
        // $exist = AliyunOss::doesObjectExist($ossClient, 'solitairearena');
        $file = AliyunOss::listObjects($ossClient);
        // $uploaddir = AliyunOss::uploadDir($ossClient, 'test/db/2021-09-17', '/mnt/d/www/skills-backup/storage/app/test/db/2021-09-17');
        dump($file);
        // dump($uploaddir);
    }

    public function test5()
    {
        // $res = Storage::disk('s3')->allDirectories();
        // $res = Storage::disk('s3')->directories();
        $res = Storage::disk('s3')->allFiles();
        // $res = Storage::disk('s3')->put('test/db/2021-09-18/test-2021-09-18-03-23-07.sql', '/mnt/d/www/skills-backup/storage/app/test/db/2021-09-18/test-2021-09-18-03-23-07.sql');
        dd($res);
    }

    public function test6()
    {
        $app = config('backup')['bingoforcash'];
        $user = $app['user'];
        $host = $app['host'];
        $password = $app['password'];
        $database = $app['database'];
        $table = $app['table'];
        $localDir = Storage::disk('local')->path($app['dir'] . Carbon::now()->format('Y-m-d')) . '/';

        foreach ($table as $key => $value) {
            $this->filePath = $localDir . $value . Carbon::now()->format('-Y-m-d-H-i-s') . '.sql';
            $fp = popen("mysqldump --column-statistics=0 --set-gtid-purged=off --single-transaction --quick -u$user -h$host -p$password -B $database --tables $value> {$this->filePath} ", "r");
        }


        $rs = '';
        while (!feof($fp)) {
            $rs .= fread($fp, 1024);
        }
        pclose($fp);
    }

    public function test7()
    {
        $storage = new StorageClient([
            'keyFilePath' => '/mnt/d/www/skills-backup/wide-hulling-326807-d739d2bbfee1.json',
            'projectId' => 'wide-hulling-326807'
        ]);

        $bucket  = $storage->bucket('wdys-skills');
        $object = $bucket->upload(
            fopen('/mnt/d/www/skills-backup/storage/app/test/db/2021-09-26/test-no-data-2021-09-26-07-25-40.sql', 'r')
        );
        dd($object);
    }

    public function test8()
    {
        $app = config('backup')['solitairearena'];
        $importConfig = config('backup')['import'];
        $user = $importConfig['user'];
        $host = $importConfig['host'];
        $password = $importConfig['password'];
        $localDir = Storage::disk('local')->path($app['dir']);
        dump($localDir);
        $cloudDir = $app['dir'] . Carbon::now()->subDays()->format('Y-m-d') . '/';
        $files = Storage::disk('s3')->allFiles($cloudDir);
        $dataTables = [];
        $nodataTables = [];
        foreach ($files as $key => $value) {
            if (stripos($value, 'no-data') === false) {
                $dataTables[] = $value;
            } else {
                $nodataTables[] = $value;
            }
        }
        sort($dataTables);
        sort($nodataTables);
        $dataTable = array_pop($dataTables);
        $nodataTable = array_pop($nodataTables);
        dump($dataTable, $nodataTable);
        // $resource = Storage::disk('s3')->readStream($nodataTable);
        // $downloadNodata =  Storage::disk('local')->put($app['dir'] .  Carbon::now()->format('Y-m-d').'-no-data.sql', Storage::disk('s3')->readStream($nodataTable));
        $compressdfilePath = $app['dir'] .  Carbon::now()->format('Y-m-d') . '.tar.gz';
        $compressdfileName =  Carbon::now()->format('Y-m-d') . '.tar.gz';
        // $downloaddata =  Storage::disk('local')->put($compressdfilePath, Storage::disk('s3')->readStream($dataTable));
        $localNodataFileName = Storage::disk('local')->path($app['dir'] .  Carbon::now()->format('Y-m-d') . '-no-data.sql');
        exec("cd $localDir && tar -zxvf $compressdfileName", $output, $code);
        if ($code == 0) {
            $localdataFilePath = Storage::disk('local')->path($app['dir'] .  $output[0]);
        }
        dd($localdataFilePath);

        // exec("mysql -u$user -h$host -p$password < {$localNodataFileName}", $output, $code);
        // exec("mysql -u$user -h$host -p$password < {$localdataFilePath}", $output, $code);
        dump($output, $code);
    }
}
