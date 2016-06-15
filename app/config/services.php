<?php

use Pentabot\Auth\Auth;
use Pentabot\Pentabot\LibHelper;
use Phalcon\Crypt;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Security;
use Phalcon\Session\Adapter\Files as SessionAdapter;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
//        '.volt' => function ($view, $di) use ($config) {
//
//            $volt = new VoltEngine($view, $di);
//
//            $volt->setOptions(array(
//                'compiledPath' => $config->application->cacheDir,
//                'compiledSeparator' => '_'
//            ));
//
//            return $volt;
//        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
// $di->set('db', function () use ($config) {
//     return new DbAdapter($config->toArray());
// });

$di->set('db', function () use ($config) {
    return new DbAdapter($config['database']->toArray());
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
    session_set_cookie_params(60 * 60 * 24 * 30);
    $session = new SessionAdapter();
    $session->start();
//var_dump( session_get_cookie_params() );
    return $session;
});

//in services.php
$di->set('config', function () use ($config) {
    return $config;
}, true);

//$di->set(
//    'lib_helper',
//    function () {
//        return new LibHelper();
//    }
//);

//$di->set('view', function () use ($config) {
/*
$di->setShared('trans', function() use($di, $config) {
    $session = $di->getShared('session');
    $messages = null;
    // Get language code
    if($session->has("lg")) {
        $language = $session->get("lg");
    } else {
        // Ask browser what is the best language
        $language = LibHelper::getBestLanguage();
    }
    // Check if we have a translation file for that lang
    //$config->application->viewsDir
    if (file_exists($config['application']->languageDir . $language . ".php")) {
        require $config['application']->languageDir . $language . ".php";
    } else {
        // Fallback to some default
        require $config['application']->languageDir . "en.php";
    }

    // Return a translation object
    return new \Phalcon\Translate\Adapter\NativeArray(
        array(
            "content" => $messages
        )
    );
});
*/

$di->setShared('trans', function() use($di, $config) {
    $session = $di->getShared('session');
    $messages = null;
    // Get language code
    if($session->has("lc")) {
        $locale = $session->get("lc");
    } else {
        // Ask browser what is the best language
        $locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    }
    $text = new Phalcon\Translate\Adapter\Gettext(array(
        'locale' => $locale,
//        'file' => 'messages',
        'domain' => 'message',
        'directory' => $config['application']->localeDir
    ));
    return $text;
});

/*
$di->setShared('field_trans', function() use($di, $config) {
    $session = $di->getShared('session');
    $messages = null;
    // Get language code
    if($session->has("lg")) {
        $language = $session->get("lg");
    } else {
        // Ask browser what is the best language
        $language = LibHelper::getBestLanguage();
    }
    // Check if we have a translation file for that lang
    if (file_exists($config['application']->languageDir . "field_" . $language . ".php")) {
        require $config['application']->languageDir . "field_" . $language . ".php";
    } else {
        // Fallback to some default
        require $config['application']->languageDir . "field_" . "en.php";
    }
    // Return a translation object
    return new \Phalcon\Translate\Adapter\NativeArray(
        array(
            "content" => $messages
        )
    );
});
*/

$di->set('security', function () {

    $security = new Security();

    // Set the password hashing factor to 12 rounds
    $security->setWorkFactor(12);

    return $security;
}, true);

/**
 * Custom authentication component
 */
$di->set('auth', function () {
    return new Auth();
});

// Registering a dispatcher
$di->set('dispatcher', function () use ($di) {
    $evManager = $di->getShared('eventsManager');

    $evManager->attach(
        "dispatch:beforeException",
        function($event, $dispatcher, $exception)
        {
            switch ($exception->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward(
                        array(
                            'controller' => 'error',
                            'action'     => 'show404',
                        )
                    );
                    return false;
            }
        }
    );
    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($evManager);
    $dispatcher->setDefaultNamespace('Pentabot\Controllers');
    return $dispatcher;
});

/**
 * Loading routes from the routes.php file
 */
$di->setShared('router', function () {
    return require __DIR__ . '/routes.php';
});

$di->set('cookies', function () {
    $cookies = new Phalcon\Http\Response\Cookies();
    $cookies->useEncryption(true);
    return $cookies;
});

$di->set('crypt', function () {
    $crypt = new Crypt();
    $crypt->setKey('uGcDwuP3Wb9m'); // Use your own key!
    return $crypt;
});