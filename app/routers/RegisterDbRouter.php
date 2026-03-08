<?php

namespace App\routers;

use App\controllers\RegisterDbController;

class RegisterDbRouter
{
    public function index()
    {
        $controller = new RegisterDbController();
        $action = $_GET['action'] ?? 'index';
        switch ($action) {
            case 'save-config':
                $controller->saveConfig();
                break;
            default:
                $controller->index();
        }
    }
}
