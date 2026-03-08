<?php

namespace App\controllers;

class HomeController
{
    /**
     * Responsável por carregar a view de home.
     * @return void
     */
    public function index()
    {
        require_once __DIR__ . '/../views/home/index.php';
    }
}
