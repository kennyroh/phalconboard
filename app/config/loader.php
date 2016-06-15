<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
//        $config->application->libraryDir . 'Pentabot',
    )
)->register();

$loader->registerNamespaces(
    array(
        'Pentabot\Controllers' => $config->application->appDir . "controllers/",
        'Pentabot\Models' => $config->application->appDir . "models/",
        'Pentabot\Forms' => $config->application->appDir . "forms/",
        'Pentabot\Pentabot' => $config->application->libraryDir . 'Pentabot/',
        'Pentabot\Auth' => $config->application->libraryDir . 'Auth/',
    )
)->register();
//echo $config->application->appDir . "controllers/";