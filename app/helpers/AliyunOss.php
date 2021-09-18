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

    public function __construct()
    {
        self::init();
    }
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

    /**
     *
     * @param OssClient $ossClient OssClient instance
     * @param string $bucket bucket name
     * @return null
     */
    public static function doesObjectExist($ossClient, $object)
    {
        try {
            $exist = $ossClient->doesObjectExist(self::$bucket, $object);
        } catch (OssException $e) {
            Log::error($e->getMessage() . "\n");
            return;
        }
        return $exist;
    }

    /**
     *
     * @param OssClient $ossClient OssClient instance
     * @param string $bucket bucket name
     * @return null
     */
    public static function listObjects($ossClient)
    {
        $options = array(
        );
        try {
            $listObjectInfo = $ossClient->listObjects(self::$bucket, $options);
        } catch (OssException $e) {

            return;
        }
        $objectList = $listObjectInfo->getObjectList(); // object list
        // $prefixList = $listObjectInfo->getPrefixList(); // directory list
        return $listObjectInfo;
    }

    /**
     *
     * @param OssClient $ossClient OssClient
     * @param string $bucket bucket name
     *
     */
    public static function uploadDir($ossClient, $prefix , $localDirectory)
    {
        try {
            $res = $ossClient->uploadDir(self::$bucket, $prefix, $localDirectory);
        } catch (OssException $e) {
            Log::error($e->getMessage() . "\n");
            return false;
        }
        return $res;
    }

    /**
     *
     * @param OssClient $ossClient OssClient instance
     * @param string $bucket bucket name
     * @return null
     */
    public static function uploadFile($ossClient, $object, $filePath)
    {
        $options = array();

        try {
            $res = $ossClient->uploadFile(self::$bucket, $object, $filePath, $options);
        } catch (OssException $e) {
            Log::error($e->getMessage() . "\n");
            return false;
        }
        return $res;
    }
}
