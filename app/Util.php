<?php
namespace App;

use \Medoo\Medoo;

class Util
{
    public static function get($key, $default_value = null)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default_value;
    }

    public static function post($key, $default_value = null)
    {
        return isset($_POST[$key]) ? $_POST[$key] : $default_value;
    }

    public static function env($key, $default_value = null)
    {
        return isset($_ENV[$key]) ? $_ENV[$key] : $default_value;
    }

    public static function header($key, $default_value = null)
    {
        $headers = $default_value;
        $upper_key = "HTTP_" . strtoupper($key);

        if (isset($_SERVER[$key])) {
            $headers = trim($_SERVER[$key]);
        } else if (isset($_SERVER[$upper_key])) {
            $headers = trim($_SERVER[$upper_key]);
        } else if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders[$key])) {
                $headers = trim($requestHeaders[$key]);
            }
        }

        return $headers;
    }

    public static function random($length = 10)
    {
        $characters = '0123456789ABCDEF';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function unique(Medoo $db, $table, $field, $suffix = "")
    {
        $rand = Util::random();
        while (($db->get($table, [$field], [$field => $rand . "-" . $suffix]))) {
            $rand = Util::random();
        }

        return $rand . "-" . $suffix;
    }
}
