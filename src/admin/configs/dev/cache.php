<?php
//$cache['session'] = array(
//    'type' => 'redis'
//);
$cache['session'] = array(
    'type' => 'FileCache',
    'cache_dir' => '/dev/shm'
);
return $cache;

