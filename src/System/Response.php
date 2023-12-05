<?php
namespace App\System;
class Response
{
    public static function redirect($url)
    {
        header(sprintf('Location: %s', $url));
    }

    public static function setHeaders($name, $value)
    {
        header(sprintf('%s: %s', $name, $value));
    }
}
