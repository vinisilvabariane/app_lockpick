<?php

namespace App\routers;

use App\controllers\UserController;

class UserRouter
{
    public function index()
    {
        $controller = new UserController();
        $action = $_GET['action'] ?? 'index';
        switch ($action) {
            case 'index':
            default:
                $controller->index();
        }
    }
}
