<?php
/**
 * Created by pentabot.com
 * User: Roh Kyoung-Min
 * Date: 2016-05-30
 * Time: 오후 10:20
 */
namespace Pentabot\Controllers;

class ErrorController extends ControllerBase
{

    public function indexAction()
    {
    }

    public function show404Action(){
        $this->response->setStatusCode(404, 'Not Found');
        $this->view->pick('http/404');
    }
}