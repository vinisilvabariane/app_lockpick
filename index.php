<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\config\Router;

$router = new Router();
$router->dispatch(
    isset($_SERVER['REQUEST_URI'])
    ? $_SERVER['REQUEST_URI']
    : '/',
    isset($_SERVER['SCRIPT_NAME'])
    ? $_SERVER['SCRIPT_NAME']
        : '/index.php');
