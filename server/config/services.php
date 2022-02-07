<?php

use Phalcon\Db\Dialect\Mysql;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Router;
use Phalcon\Crypt;
use Phalcon\Http\Response\Cookies;
use Phalcon\Db\Adapter\Pdo\Mysql as MysqlPdo;
use MongoClient;
use Phalcon\Cache\Backend\Redis as Redis_Cache;
use Phalcon\Cache\Frontend\Data as FrontData;
use Library\Ico;
use Phalcon\Db\Profiler as ProfilerDb;
use Phalcon\Logger;
use Phalcon\Events\Manager;
use Phalcon\Logger\Adapter\File as FileLogger;
/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "config/config.php";
});

include APP_PATH . 'vendor/autoload.php';
$di->setShared('dispatcher', function () use ($di) {

    $eventsManager = new Phalcon\Events\Manager;

    $eventsManager->attach('dispatch:beforeException', new ExceptionPlugin);
    $eventsManager->attach('dispatch:beforeExecuteRoute', new ApiServerPlugin);
    $dispatcher = new Dispatcher;
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});
$di->set('router', function () {
    $router = new Router(false);

    //默认
    $router->add('/', array(
        'namespace' => '',
        'controller' => 'index',
        'action' => 'index',
    ));
    $router->add('/index/:controller/:action/:params', array(
        'namespace' => '',
        'controller' => 1,
        'action' => 2,
        'params' => 3,
    ));
    $router->notFound(
        [
            'namespace' => '',
            'controller' => 'index',
            'action' => 'info',
            'params' => array('title' => '出错了!','magess' => 'NOT 404')
        ]
    );
    //外网
    $router->add('/web/:controller/:action/:params', array(
        'namespace' => 'Controllers\Web',
        'controller' => 1,
        'action' => 2,
        'params' => 3,
    ));
    $router->add('/web/:controller', array(
        'namespace' => 'Controllers\Web',
        'controller' => 1,
    ));
    $router->add('/web(/?)', array(
        'namespace' => 'Controllers\Web',
        'controller' => 'index',
        'action' => 'index'
    ));
    //接口
    $router->add('/api/:controller/:action/:params', array(
        'namespace' => 'Controllers\Api',
        'controller' => 1,
        'action' => 2,
        'params' => 3,
    ));
    $router->add('/api/:controller', array(
        'namespace' => 'Controllers\Api',
        'controller' => 1,
    ));
    $router->add('/api(/?)', array(
        'namespace' => 'Controllers\Api',
        'controller' => 'index',
        'action' => 'index'
    ));
    return $router;
}, true);
/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.html' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);
            $path = $config->application->cacheDir.'view/';

            if (!is_dir($path)) {
                @mkdir($path, 0777 , true);
                chmod($path,0777);
            }
            $volt->setOptions([
                'compiledPath' =>$path,
                'compiledSeparator' => '%%'
            ]);
            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

$di->set('profiler', function () {
    return new ProfilerDb();
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $eventsManager = new Manager();
    $y = date("Y",time());
    $m = date("m",time());
    $d = date("d",time());
    $path = dirname(dirname(__FILE__))."/cache/log/db/db/$y/$m/";
    if (!is_dir($path))
    {
        @mkdir($path,0777,true);
        chmod($path,0777);
    }
    $logger = new FileLogger($path.$d.".log");

    $params = [
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);
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
});
$di->set(
    'dbMaster',
    function () {
        $config = $this->getConfig();

        $eventsManager = new Manager();

        $y = date("Y",time());
        $m = date("m",time());
        $d = date("d",time());
        $path = dirname(dirname(__FILE__))."/cache/log/db/dbMaster/$y/$m/";
        if (!is_dir($path))
        {
            @mkdir($path,0777,true);
            chmod($path,0777);
        }
        $logger = new FileLogger($path.$d.".log");

        $connection = new MysqlPdo(
            [
                'host'     =>  $config->dbMaster->host,
                'username' =>  $config->dbMaster->username,
                'password' =>  $config->dbMaster->password,
                'dbname'   =>  $config->dbMaster->dbname,
                'charset' => $config->dbMaster->charset
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
    'dbSlave',
    function () {
        $config = $this->getConfig();
        $eventsManager = new Manager();
        $y = date("Y",time());
        $m = date("m",time());
        $d = date("d",time());
        $path = dirname(dirname(__FILE__))."/cache/log/db/dbSlave/$y/$m/";
        if (!is_dir($path))
        {
            @mkdir($path,0777,true);
            chmod($path,0777);
        }
        $logger = new FileLogger($path.$d.".log");
        $connection = new MysqlPdo(
            [
                'host'     =>  $config->dbSlave->host,
                'username' =>  $config->dbSlave->username,
                'password' =>  $config->dbSlave->password,
                'dbname'   =>  $config->dbSlave->dbname,
                'charset' => $config->dbSlave->charset
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
    'dbLog',
    function () {
        $config = $this->getConfig();

        $eventsManager = new Manager();
        $y = date("Y",time());
        $m = date("m",time());
        $d = date("d",time());
        $path = dirname(dirname(__FILE__))."/cache/log/db/dbLog/$y/$m/";
        if (!is_dir($path))
        {
            @mkdir($path,0777,true);
            chmod($path,0777);
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
        $path = dirname(dirname(__FILE__))."/cache/log/db/dbDy/$y/$m/";
        if (!is_dir($path))
        {
            @mkdir($path,0777,true);
            chmod($path,0777);
        }
        $logger = new FileLogger($path.$d.".log");

        $connection = new MysqlPdo(
            [
                'host'     =>  $config->dbDy->host,
                'username' =>  $config->dbDy->username,
                'password' =>  $config->dbDy->password,
                'dbname'   =>  $config->dbDy->dbname,
                'charset'  => $config->dbDy->charset,
                'port'     => $config->dbDy->port
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
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
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
            'lifetime' => null
        ]);
        $cache = new Redis_Cache($frontCache,[
            'host' => $host,
            'port' => $port,
            'prefix' => $prefix,
            'statsKey' => '_PHCR'
        ]);

        return $cache;
    },
    true
);

Ico::set(
    'debug_log',
    function (){
        $file_name = date('YmdH',time()).'.log';
        $path = dirname(dirname(__FILE__)).'/cache/log/debug/';
        if (!is_dir($path))
        {
            @mkdir($path,0777,true);
            chmod($path,0777);
        }
        $logger = new FileLogger($path.'/'.$file_name);
        return $logger;
    }
);
Ico::set(
    'redis',
    function (){
        $config = json_decode(json_encode($GLOBALS['config']['redis']),true);
        $host = $config['host'];
        $port = $config['port'];
        $redis = new \Redis();
        $redis->connect($host,$port);
        return $redis;
    }
);
