<?php
/**
 * Created by PhpStorm.
 * User: app
 * Date: 2021-09-14
 * Time: 18:37
 */

namespace App\helpers;


use Illuminate\Support\Facades\Log;
use OSS\Core\OssException;
use OSS\OssClient;

/**
 * 阿里云oss操作类
 * Class AliyunOss
 * @package App\helpers
 */
class AliyunOss
{
    private static $access_key_id = null;
    private static $access_secret = null;
    private static $endpoint = null;
    private static $bucket = null;

    /**
     * 初始化配置
     */
    private static function init()
    {
        self::$access_key_id = self::$access_key_id ?? env('ALIYUN_OSS_ACCESSKEYID');
        self::$access_secret = self::$access_secret ?? env('ALIYUN_OSS_ACCESSSECRET');
        self::$endpoint = self::$endpoint ?? env('ALIYUN_OSS_ENDPOINT');
        self::$bucket = self::$bucket ?? env('ALIYUN_OSS_BUCKET');
    }

    /**
     * 创建阿里云oss客户端对象
     * @param null $endpoint
     * @return OssClient|null
     */
    public static function getClient($endpoint = null)
    {
        try {
            self::init();
            $client = new OssClient(self::$access_key_id, self::$access_secret, ($endpoint ?? self::$endpoint), false);
            return $client;
        } catch (OssException $e) {
            Log::error($e->getMessage());
            Log::error($e);
        }
        return null;
    }

    /**
     * 获取阿里云oss的桶
     * @return mixed
     */
    public static function getBucket()
    {
        self::init();
        return self::$bucket;
    }
}
