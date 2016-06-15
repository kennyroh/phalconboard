<?php
/**
 * Created by pentabot.com
 * User: Roh Kyoung-Min
 * Date: 2016-05-27
 * Time: 오후 9:28
 */

$router = new Phalcon\Mvc\Router();
$router->removeExtraSlashes(true);
$router->add(
    "/",
    array(
        'controller' => 'index',
        'action'     => 'index'
    )
);
$router->notFound([
    'controller' => 'error',
    'action' => 'show404'
]);
$router->add('/login', [
    'controller' => 'session',
    'action'    => 'login'
]);
$router->add('/logout', [
    'controller' => 'session',
    'action'    => 'logout'
]);

return $router;