<?php
$db['master'] = array(
    'type' => Swoole\Database::TYPE_MYSQLi,
    'host' => "192.168.2.250",
    'port' => 3306,
    'dbms' => 'mysql',
    'user' => "admin",
    'passwd' => "123456",
    'name' => "crontab_local",
    'charset' => "utf8",
    'setname' => true,
    'persistent' => false, //MySQL长连接
);
return $db;