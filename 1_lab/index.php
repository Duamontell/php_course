<?php

require_once __DIR__ . "/src/controller/UserController.php";
require_once __DIR__ . "/src/infrastructure/DatabaseConnection.php";

$pdo = DatabaseConnection::connectToDatabase();
$userController = new UserController($pdo);
// $userController->index();
