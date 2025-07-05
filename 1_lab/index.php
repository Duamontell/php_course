<?php

require_once __DIR__ . "/vendor/autoload.php";

use App\infrastructure\DatabaseConnection;
use App\controller\UserController;

$pdo = DatabaseConnection::connectToDatabase();
$userController = new UserController($pdo);
$userController->index();
