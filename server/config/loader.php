<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->modelsDir.'/es',
        $config->application->modelsDir.'/mongo',
        $config->application->pluginsDir,
        $config->application->libraryDir,
        $config->application->cronDir,
        $config->application->libraryDir.'/page',
    ]
)->registerNamespaces(
    array(
        'Controllers\Admin' => $config->application->controllersDir.'/admin',
        'Controllers\Web' => $config->application->controllersDir.'/web',
        'Controllers\Api' => $config->application->controllersDir.'/api',
        'Controllers\Live' => $config->application->controllersDir.'/live',
        'Controllers\Im' => $config->application->controllersDir.'/im',
        'Controllers\WWW' => $config->application->controllersDir.'/www',
        'Library' => $config->application->libraryDir,
        'Cron' => $config->application->cronDir,
        'Model\Mysql' =>  $config->application->modelsDir.'/mysql',
    )
)->register();
