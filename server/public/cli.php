<?php
ini_set('display_errors', 0);
date_default_timezone_set('Asia/Shanghai');
//use \Phalcon\Di\FactoryDefault\Cli;
//$di = new Cli();

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
try {

    define('APP_PATH', realpath(__DIR__ . '/../') . '/');

    //config
    $config = require __DIR__ . '/../config/config.php';
    /**
     * Include constants.
     */
    require __DIR__ . '/../config/const.php';

    require __DIR__ . '/../cli/services.php';

    //loader
    require __DIR__ . '/../config/loader.php';

    $loader = new \Phalcon\Loader();
    $loader->registerDirs(array(__DIR__ . '/../cli/tasks'));
    $loader->register();

    // 创建console应用
    $console = new Phalcon\Cli\Console();

    $di->set('console', $console, true);

    $console->setDI($di);

    /**
     * 处理console应用参数.
     */
    $arguments = array();
    foreach ($argv as $k => $arg) {
        if ($k == 1) {
            $arguments['task'] = $arg;
        } elseif ($k == 2) {
            $arguments['action'] = $arg;
        } elseif ($k >= 3) {
            $arguments['time'] = $arg;
        }
    }
    // 定义全局的参数， 设定当前任务及动作
    define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
    define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));
    define('CURRENT_TIME', (isset($argv[3]) ? $argv[3] : null));
    // 处理参数
    $console->handle($arguments);

} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}
