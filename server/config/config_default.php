<?php
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/');
return new \Phalcon\Config([
    //++++++主库
    'dbMaster' => [
        'adapter'     => 'Mysql',
        'host'        => '192.168.0.8',
        'username'    => 'root',
        'password'    => '123456',
        'dbname'      => 'game_admin',
        'charset'     => 'utf8',
        'port'        => '3306'
    ],
    //++++++从库
    'dbSlave' => [
        'adapter'     => 'Mysql',
        'host'        => '192.168.0.8',
        'username'    => 'root',
        'password'    => '123456',
        'dbname'      => 'game_admin',
        'charset'     => 'utf8',
        'port'        => '3306'
    ],
    //++++++++主库 databases 和dbMaster 保持一致
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => '192.168.0.8',
        'username'    => 'root',
        'password'    => '123456',
        'dbname'      => 'game_admin',
        'charset'     => 'utf8',
        'port'        => '3306'
    ],
    //+++++++++日志库
    'dbLog' => [
        'adapter'     => 'Mysql',
        'host'        => '192.168.0.8',
        'username'    => 'root',
        'password'    => '123456',
        'dbname'      => 'db_log',
        'charset'     => 'utf8',
        'port'        => '3306'
    ],
    //++++++++++动态库
    'dbDy' => [
        'adapter'     => 'Mysql',
        'host'        => '192.168.0.8',
        'username'    => 'root',
        'password'    => '123456',
        'dbname'      => 'db_test',
        'charset'     => 'utf8',
        'port'        => '3306'
    ],
    'application' => [
        'appDir'         => APP_PATH . 'app/',
        'controllersDir' => APP_PATH . 'app/controllers/',
        'modelsDir'      => APP_PATH . 'app/models/',
        'viewsDir'       => APP_PATH . 'app/views/',
        'pluginsDir'     => APP_PATH . 'plugins/',
        'libraryDir'     => APP_PATH . 'library/',
        'cronDir'        => APP_PATH . 'cron/',
        'cacheDir'       => APP_PATH . 'cache/',
        'baseUri'        => '/',
        'uploadUri'      => APP_PATH . 'public/upload/',
    ],

    //mongodb
    'mongodb' => [
        'user' => 'root',
        'password' => 'root',
        'auth' => true,
        'host' => '127.0.0.1',
        'port' => '27017',
        'db' => 'cms'
    ],
    //redis
    'redis' => [
        'host' => '127.0.0.1',
        'port' => '6379',
        'prefix' => '_game_',
        'lifetime' => 2*3600
    ]

]);
