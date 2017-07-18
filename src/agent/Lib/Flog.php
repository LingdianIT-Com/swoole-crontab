<?php
/**
 * 记录运行日志
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 上午10:33
 */

namespace Lib;


class Flog
{
    public static function log($value)
    {
        if (DEBUG) {
            $dir = dirname(__DIR__)."/logs/";
            if (!file_exists($dir)) {
                mkdir($dir);
            }
            $file = "/data/www/swoole-crontab/src/agent/logs/agent.log";
            file_put_contents($file, $value."\r\n",FILE_APPEND | LOCK_EX);
        }
    }

}