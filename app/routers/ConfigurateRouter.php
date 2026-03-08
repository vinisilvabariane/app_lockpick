<?php

namespace App\routers;

use App\controllers\ConfigurateController;

class ConfigurateRouter
{
    public function index()
    {
        $controller = new ConfigurateController();
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'list-tables':
                $controller->listTables();
                break;
            case 'load-configuration':
                $controller->loadConfiguration();
                break;
            case 'save-configuration':
                $controller->saveConfiguration();
                break;
            default:
                $controller->index();
                break;
        }
    }
}
