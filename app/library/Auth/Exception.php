<?php
/**
 * Created by pentabot.com
 * User: Roh Kyoung-Min
 * Date: 2016-05-25
 * Time: 오후 10:12
 */
namespace Pentabot\Auth;

class Exception extends \Phalcon\Exception
{
    /**
     * Exception constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        \Exception::__construct($message);
        if (IS_DEBUG == true) {
            \PhalconDebug::addMessage('Exception', $message);
        }
    }


}