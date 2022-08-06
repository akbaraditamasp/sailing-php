<?php
namespace App;

use \Medoo\Medoo;

class Db
{
    public static function init()
    {
        return new Medoo([
            // [required]
            'type' => 'mysql',
            'host' => $_ENV["DB_HOST"],
            'database' => $_ENV["DB_NAME"],
            'username' => $_ENV["DB_USER"],
            'password' => $_ENV["DB_PASS"],
        ]);
    }
}
