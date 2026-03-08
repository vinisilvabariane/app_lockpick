<?php

namespace App\controllers;

use App\config\ErrorResponse;
use App\exceptions\RegisterDbException;
use App\models\UserModel;

class UserController
{
    /**
     * Responsável por carregar a view de cadastro de usuário.
     * @return void
     */
    public function index()
    {
        require_once __DIR__ . "/../views/user/index.php";
    }
}
