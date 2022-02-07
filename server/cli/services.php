<?php

use \Phalcon\Db\Adapter\Pdo\Mysql as MysqlPdo;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Flash\Direct as Flash;
use \Phalcon\Di\FactoryDefault\Cli as Clis;
use Phalcon\Crypt;
use Phalcon\Http\Response\Cookies;
use Phalcon\Cache\Backend\Redis as RedisCache;
use Phalcon\Cache\Frontend\Data as FrontData;
use Library\Ico;
use Phalcon\Db\Profiler as ProfilerDb;
use Phalcon\Logger;
use Phalcon\Events\Manager;
use Phalcon\Logger\Adapter\File as FileLogger;
$di = new Clis();
/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include __DIR__. "/../config/config.php";
});
$config =  include __DIR__. "/../config/config.php" ;

include __DIR__ . '/../vendor/autoload.php';
/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});
$di->set(
    'dbMaster',
    function () {
        $config = $this->getConfig();
        return new MysqlPdo(
            [
                'host'     =>  $config->dbMaster->host,
                'username' =>  $config->dbMaster->username,
                'password' =>  $config->dbMaster->password,
                'dbname'   =>  $config->dbMaster->dbname,
                'charset'  => $config->database->charset
            ]
        );
    }
);

$di->set(
    'dbSlave',
    function () {
        $config = $this->getConfig();
        return new MysqlPdo(
            [
                'host'     =>  $config->dbSlave->host,
                'username' =>  $config->dbSlave->username,
                'password' =>  $config->dbSlave->password,
                'dbname'   =>  $config->dbSlave->dbname,
                'charset'  => $config->database->charset
            ]
        );
    }
);

$di->set(
    'dbLog',
    function () {
        $config = $this->getConfig();

        $eventsManager = new Manager();
        $y = date("Y",time());
        $m = date("m",time());
        $d = date("d",time());
        $path = dirname(dirname(__FILE__))."/cache/cron_log/db/dbLog/$y/$m/";
        if (!is_dir($path))
        {
            @mkdir($path,0777,true);
            @chmod($path,0777);
        }
        $logger = new FileLogger($path.$d.".log");

        $connection = new MysqlPdo(
            [
                'host'     =>  $config->dbLog->host,
                'username' =>  $config->dbLog->username,
                'password' =>  $config->dbLog->password,
                'dbname'   =>  $config->dbLog->dbname,
                'charset' => $config->dbLog->charset
            ]
        );
        $eventsManager->attach(
            'db:beforeQuery',
            function ($event, $connection) use ($logger) {
                $logger->log(
                    $connection->getSQLStatement(),
                    Logger::INFO
                );
            }
        );
        $connection->setEventsManager($eventsManager);
        return $connection;
    }
);

$di->set(
    'dbDy',
    function () {
        $config = $this->getConfig();

        $eventsManager = new Manager();
        $y = date("Y",time());
        $m = date("m",time());
        $d = date("d",time());
        $path = dirname(dirname(__FILE__))."/cache/cron_log/db/dbDy/$y/$m/";
        if (!is_dir($path))
        {
            @mkdir($path,0777,true);
            @chmod($path,0777);
        }
        $logger = new FileLogger($path.$d.".log");

        $connection = new MysqlPdo(
            [
                'host'     =>  $config->dbDy->host,
                'username' =>  $config->dbDy->username,
                'password' =>  $config->dbDy->password,
                'dbname'   =>  $config->dbDy->dbname,
                'charset'  =>  $config->dbDy->charset,
                'port'     =>  $config->dbDy->port
            ]
        );
        $eventsManager->attach(
            'db:beforeQuery',
            function ($event, $connection) use ($logger) {
                $logger->log(
                    $connection->getSQLStatement(),
                    Logger::INFO
                );
            }
        );
        $connection->setEventsManager($eventsManager);
        return $connection;
    }
);

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    return new Flash([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});
/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});
$di->set(
    'crypt',
    function (){
        $crypt = new Crypt();
        // Set a global encryption key
        $crypt->setKey(
            "T4\xb1\x8d\xa9\x98\x05\\\x8c\xbe\x1d\x07&[\x99\x18\xa4~Lc1\xbeW\xb3|T4\xb1\x8d\xa9\x98\x054t7w!z%C*F-Jk\x98\x05\\\x5c"
        );
        $crypt->setCipher('aes-256-ctr');
        return $crypt;
    },
    true
);
$di->set(
    'cookies',
    function () {
        $cookies = new Cookies();

        return $cookies;
    }
);
$di->set(
    'mongo',
    function () {
        $mongo_user = $this->getConfig()->mongodb->user;
        $mongo_pwd = $this->getConfig()->mongodb->password;
        $mongo_auth = $this->getConfig()->mongodb->auth;
        $mongo_host =  $this->getConfig()->mongodb->host;
        $mongo_port =  $this->getConfig()->mongodb->port;
        $mongo_dbname = $this->getConfig()->mongodb->db;
        if ($mongo_auth) {
            $uri = 'mongodb://' . $mongo_user . ':' . $mongo_pwd . '@' . $mongo_host . ':' . $mongo_port . '/' . $mongo_dbname;
        } else {
            $uri = 'mongodb://' . $mongo_host . ':' . $mongo_port . '/' . $mongo_dbname;
        }
        $mongo = new \MongoDB\Driver\Manager(
            $uri
        );

        return $mongo;
    },
    true
);
$di->set(
    'cache',
    function () {

        $host =  $this->getConfig()->redis->host;
        $port =  $this->getConfig()->redis->port;
        $prefix = $this->getConfig()->redis->prefix;
        $lifetime = $this->getConfig()->redis->lifetime;
        $frontCache = new FrontData([
            'lifetime' => $lifetime
        ]);
        $redis = new RedisCache($frontCache,[
            'host' => $host,
            'port' => $port,
            'prefix' => $prefix
        ]);

        return $redis;
    },
    true
);
$di->set(
    'redis',
    function () {

        $host =  $this->getConfig()->redis->host;
        $port =  $this->getConfig()->redis->port;
        $redis = new Redis();
        $redis->connect($host,$port);
        return $redis;
    },
    true
);

$di->set(
    'debug',
    function (){
        $file_name = date('YmdH',time()).'.log';
        $path = dirname(dirname(__FILE__)).'/cache/cron_log/debug/';
        if (!is_dir($path))
        {
            @mkdir($path,0777,true);
            chmod($path,0777);
        }
        $logger = new FileLogger($path.'/'.$file_name);
        return $logger;
    }
);