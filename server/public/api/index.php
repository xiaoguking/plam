<?php
use Phalcon\Di\FactoryDefault;
/**
 * api
 */
header("access-control-allow-origin: *");
ini_set('max_execution_time', 1000);//秒为单位，自己根据需要定义
header('Access-Control-Allow-Headers:Token');
if ( strtolower($_SERVER['REQUEST_METHOD']) == 'options'){
    exit();
}
date_default_timezone_set('Asia/Shanghai');
ini_set('display_errors', 0);
ini_set('session.cookie_httponly', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
//error_reporting(E_ALL & ~E_NOTICE);
define('APP_PATH', substr(realpath('..'), 0, -7) . '/');
define('API',1);
try {
    /**
     * Read the configuration.
     */
    $di = new FactoryDefault();
    $config = require __DIR__ . '/../../config/config.php';

    define('BASE_URI', $config->application->baseUri);
    /**
     * Auto-loader configuration.
     */
    require __DIR__ . '/../../config/loader.php';

    /**
     * Include services.
     */
    require __DIR__ . '/../../config/services.php';

    /**
     * Handle the request.
     */
    $config = $di->getConfig();

    /**
     * Include Const
     */
    require __DIR__ . '/../../config/const.php';

    require __DIR__ . '/../../config/admin_router.php';
    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    $request = \Phalcon\DI::getDefault()->get('request');

    $content = $application->handle()->getContent();

    echo $content;
} catch (Exception $e) {
    print_r($e->getMessage());
}
