<?php
//clearstatcache();
if( 0 === strpos($_SERVER["SERVER_ADDR"], '127.0.0.1') ) {
    define('IS_DEBUG', true);
    error_reporting(E_ALL);
    (new Phalcon\Debug)->listen();
}else{
    define('IS_DEBUG', false);
}
try {

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../app/config/config.php";

    /**
     * Read auto-loader
     */
    include __DIR__ . "/../app/config/loader.php";

    /**
     * Include composer autoloader
     */
    require __DIR__ . "/../vendor/autoload.php";

    /**
     * Read services
     */
    include __DIR__ . "/../app/config/services.php";

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);
    if (IS_DEBUG == true) {
        $di['app'] = $application; //  Important
        (new Snowair\Debugbar\ServiceProvider())->start();
    }
    echo $application->handle()->getContent();

} catch (\Exception $e) {
    echo $e->getMessage();
}
