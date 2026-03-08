<?php

namespace App\config;

use App\routers\HomeRouter;
use App\routers\RegisterDbRouter;
use App\routers\ResetRouter;
use App\routers\UserRouter;
use App\routers\ConfigurateRouter;

class Router
{
    public function dispatch($requestUri, $scriptName)
    {
        $basePath = $this->extractBasePath($scriptName);
        $routePath = $this->extractRoutePath($requestUri, $basePath);
        $routes = [
            '/' => [HomeRouter::class, 'index'],
            '/home' => [HomeRouter::class, 'index'],
            '/register' => [RegisterDbRouter::class, 'index'],
            '/reset' => [ResetRouter::class, 'index'],
            '/user' => [UserRouter::class, 'index'],
            '/configurate' => [ConfigurateRouter::class, 'index'],
        ];
        if (!isset($routes[$routePath])) {
            http_response_code(404);
            $_SERVER['APP_BASE_PATH'] = $basePath;
            $_SERVER['APP_CURRENT_ROUTE'] = '/not-found';
            require_once __DIR__ . '/notFound/index.php';
            return;
        }
        $_SERVER['APP_BASE_PATH'] = $basePath;
        $_SERVER['APP_CURRENT_ROUTE'] = $routePath;
        list($controllerClass, $action) = $routes[$routePath];
        $controller = new $controllerClass();
        $controller->$action();
    }

    private function extractBasePath($scriptName)
    {
        $normalizedScript = str_replace('\\', '/', (string)$scriptName);
        $basePath = str_replace('/index.php', '', $normalizedScript);
        $basePath = rtrim($basePath, '/');
        if ($basePath === '' || $basePath === '.') {
            return '';
        }
        return $basePath;
    }

    private function extractRoutePath($requestUri, $basePath)
    {
        $path = parse_url((string)$requestUri, PHP_URL_PATH);
        $path = str_replace('\\', '/', (string)$path);
        if ($basePath !== '' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        if ($path === '' || $path === false || $path === '/index.php') {
            $queryPage = isset($_GET['page']) ? trim((string)$_GET['page']) : '';
            $queryRoute = strtolower($queryPage);
            if (in_array($queryRoute, ['home', 'register', 'reset', 'user', 'configurate'], true)) {
                return '/' . $queryRoute;
            }
            return '/';
        }
        if (strpos($path, '/index.php/') === 0) {
            $path = substr($path, strlen('/index.php'));
        }
        $path = '/' . trim($path, '/');
        if ($path === '/index.php' || $path === '//') {
            return '/';
        }
        return strtolower($path);
    }
}
