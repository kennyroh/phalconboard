<?php
namespace Pentabot\Pentabot;

use Phalcon\Mvc\User\Component;

/**
 * Created by PhpStorm.
 * User: z
 * Date: 2016-05-22
 * Time: 오후 1:34
 */
class LibHelper extends Component
{
    public static function getBestLanguage()
    {
        $locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $language = substr($locale, 0, 2);
        return $language;
    }
}