<?php

namespace App\controllers;

class ResetController
{
    /**
     * Responsavel por carregar a view de reset.
     * @return void
     */
    public function index()
    {
        require_once __DIR__ . '/../views/reset/index.php';
    }
}
