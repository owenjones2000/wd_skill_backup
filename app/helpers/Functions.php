<?php

/**
 * Created by PhpStorm.
 * User: app
 * Date: 2021-09-14
 * Time: 18:37
 */

namespace App\helpers;


class Functions
{

    /**
     * 删除目录下的所有目录和文件
     * @param string $path 待删除的目录
     * @note  $path路径结尾要有斜杠/(例如:[$path='./static/image/'])
     */
    public static function deleteDirsFile($path)
    {

        if (is_dir($path)) {
            //扫描一个目录内的所有目录和文件并返回数组
            $dirs = scandir($path);

            foreach ($dirs as $dir) {
                //排除目录中的当前目录(.)和上一级目录(..)
                if ($dir != '.' && $dir != '..') {
                    //如果是目录则递归子目录，继续操作
                    $sonDir = $path  . $dir;
                    if (is_dir($sonDir)) {
                        //递归删除
                        static::deleteDirsFile($sonDir);

                        //目录内的子目录和文件删除后删除空目录
                        @rmdir($sonDir);
                    } else {

                        //如果是文件直接删除
                        @unlink($sonDir);
                    }
                }
            }
        }
    }
    /**
     * 删除当前目录及其目录下的所有目录和文件
     * @param string $path 待删除的目录
     * @note  $path路径结尾要有斜杠/(例如:[$path='./static/image/'])
     */
    public static function deleteDir($path)
    {

        if (is_dir($path)) {
            //扫描一个目录内的所有目录和文件并返回数组
            $dirs = scandir($path);

            foreach ($dirs as $dir) {
                //排除目录中的当前目录(.)和上一级目录(..)
                if ($dir != '.' && $dir != '..') {
                    //如果是目录则递归子目录，继续操作
                    $sonDir = $path  . $dir;
                    if (is_dir($sonDir)) {
                        //递归删除
                        static::deleteDirsFile($sonDir);

                        //目录内的子目录和文件删除后删除空目录
                        @rmdir($sonDir);
                    } else {

                        //如果是文件直接删除
                        @unlink($sonDir);
                    }
                }
            }
            @rmdir($path);
        }
    }
}
