<?php

use App\Db;
use App\Router;

require_once __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

if (!count($_POST)) {
    $post = json_decode(file_get_contents('php://input'), true);
    $_POST = $post ? $post : [];
}

$router = new Router();

$router->addApp("db", Db::init());

$router->setRoute("GET", "/auth/login", "\\Controller\\Auth::login");
$router->setRoute("DELETE", "/auth/logout", "\\Middleware\\Auth::valid", "\\Controller\\Auth::logout");

$router->run();
