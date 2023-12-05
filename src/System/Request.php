<?php

namespace App\System;

class Request
{
    public static function isGet()
    {
        return self::getServer('REQUEST_METHOD') == "GET";
    }

    public static function isPost()
    {
        return self::getServer('REQUEST_METHOD') == "POST";
    }

    public static function get($key)
    {
        return $_GET[$key] ?? null;
    }

    public static function post($key)
    {
        return $_POST[$key] ?? null;
    }

    public static function getVar($key)
    {
        return $_REQUEST[$key] ?? null;
    }

    public static function getServer($name)
    {
        return $_SERVER[strtoupper($name)] ?? null;
    }

    public static function getHeaders($name)
    {
        return get_headers(self::getServer('host'), true)[$name] ?? null;
    }
    public static function isUrl($url)
    {
        return $_SERVER['REQUEST_URI'] == $url;
    }
    public static function isFile($url)
    {
        return $_SERVER['PHP_SELF'] == '/frontend' . $url;
    }
}
