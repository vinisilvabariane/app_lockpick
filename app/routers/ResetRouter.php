<?php

namespace App\routers;

use App\controllers\ResetController;

class ResetRouter
{
    public function index()
    {
        $controller = new ResetController();
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            default:
                $controller->index();
        }
    }
}
