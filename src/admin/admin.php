<?php
date_default_timezone_set("Asia/Chongqing");
define('APPSPATH', __DIR__);
define('WEBPATH', dirname(__DIR__).'/public');
define("FRAMEWORK_PATH",dirname(__DIR__)."/framework/");
function getRunPath()
{
    $path = Phar::running(false);
    if (empty($path)) return __DIR__;
    else return dirname($path)."/../admin";
}


require_once FRAMEWORK_PATH.'/libs/lib_config.php';

$AppSvr = new Swoole\Protocol\AppServer();
$AppSvr->loadSetting(__DIR__."/swoole.ini");
$AppSvr->setAppPath(__DIR__);
$AppSvr->setLogger(new \Swoole\Log\FileLog(getRunPath() . '/logs/admin.log')); //Logger


$server = new \Swoole\Network\Server('0.0.0.0', 80);
$server->setProtocol($AppSvr);
$server->daemonize();
$server->setProcessName("AdminWebServer");


//重定向PHP错误日志到logs目录
ini_set('error_log', getRunPath() . '/logs/php_errors.log');

$env = getenv("DEPLOY_ENV");
if ($env == "product")
{
    define('DEBUG', 'off');
    define('WEBROOT', 'http://crontab.oa.com');
}elseif ($env == "test"){
    define('DEBUG', 'on');
    define('WEBROOT', 'http://crontab.oa.com');
} else {
    $env = 'dev';
    define('DEBUG', 'on');
    define('WEBROOT', 'http://cron.lingdianit.com');
}
define('ENV_NAME', $env);

Swoole::$php->config->setPath(APPSPATH . '/configs');
Swoole::$php->config->setPath(APPSPATH . '/configs/' . ENV_NAME);
$server->run(array('worker_num' => 3,'max_request' => 5000));

