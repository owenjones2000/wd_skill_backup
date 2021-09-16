<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

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

}
