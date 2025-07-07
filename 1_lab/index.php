<?php

declare(strict_types=1);

require_once __DIR__ . "/vendor/autoload.php";

use App\Infrastructure\DatabaseConnection;
use App\Controller\UserController;

$pdo = DatabaseConnection::connectToDatabase();
$userController = new UserController($pdo);
$userController->index();
