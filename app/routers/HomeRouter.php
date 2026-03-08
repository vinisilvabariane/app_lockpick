<?php

namespace App\routers;

use App\controllers\HomeController;

class HomeRouter
{
    public function index()
    {
        $controller = new HomeController();
        $action = $_GET['action'] ?? 'index';
        switch ($action) {
            default:
                $controller->index();
        }
    }
}