<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-18
 * Time: 下午2:30
 */
define('SERVICE', true);
define('WEBPATH', __DIR__);
define('SWOOLE_SERVER', true);
date_default_timezone_set("Asia/Shanghai");
function getRunPath()
{
    $path = Phar::running(false);
    if (empty($path)) return __DIR__;
    else return dirname($path)."/../crontab_log";
}

const LOAD_SIZE = 8192;//最多载入任务数量
const TASKS_SIZE = 1024;//同时运行任务最大数量
const ROBOT_MAX = 128;//同时挂载worker数量
const WORKER_NUM = 4;//worker进程数量
const TASK_NUM = 4;//task进程数量

define("CENTER_HOST",'127.0.0.1');
define("CENTRE_PORT",8901);
$env = getenv("DEPLOY_ENV");
if (empty($env)) {
    $env = "dev";
}
define('ENV_NAME', $env);


define("FRAMEWORK_PATH",dirname(__DIR__)."/framework/");
require_once FRAMEWORK_PATH.'/libs/lib_config.php';

Swoole::$php->config->setPath(__DIR__ . '/configs/' . ENV_NAME);//共有配置
Swoole::$php->config->setPath(__DIR__ . '/configs');//共有配置
Swoole\Loader::addNameSpace('App', __DIR__ . '/App');
Swoole\Loader::addNameSpace('Lib', __DIR__ . '/Lib');


Swoole\Network\Server::setPidFile(getRunPath() . '/logs/center.pid');

Swoole\Network\Server::start(function ($opt)
{
    $logger = new Swoole\Log\FileLog(['file' => getRunPath() . '/logs/center.log']);
    $AppSvr = new Lib\CenterServer;
    $AppSvr->setLogger($logger);

    $setting = array(
        'worker_num' => WORKER_NUM,
        'task_worker_num'=>TASK_NUM,
        'max_request' => 1000,
        'dispatch_mode' => 3,
        'log_file' => getRunPath() . '/logs/swoole.log',
        'open_length_check' => 1,
        'package_max_length' => $AppSvr->packet_maxlen,
        'package_length_type' => 'N',
        'package_body_offset' => \Swoole\Protocol\SOAServer::HEADER_SIZE,
        'package_length_offset' => 0,
    );
    //重定向PHP错误日志到logs目录
    ini_set('error_log', getRunPath() . '/logs/php_errors.log');

    \Lib\LoadTasks::init();//载入任务表
    \Lib\Donkeyid::init();//初始化donkeyid对象
    \Lib\Tasks::init();//创建task表
    \Lib\Robot::init();//创建任务处理服务表
    Swoole::$php->db->close();
    $host = CENTER_HOST;
    $port = CENTRE_PORT;
    if (isset($opt['host'])){
        $host = $opt['host'];
    }
    if (isset($opt['port'])){
        $port = $opt['port'];
    }
    
    $server = Swoole\Network\Server::autoCreate($host, $port);
    $AppSvr::$_server = $server;
    $server->setProtocol($AppSvr);
    $server->setProcessName("CenterServer");
    $server->on("PipeMessage",array($AppSvr, 'onPipeMessage'));
    $server->run($setting);
});

