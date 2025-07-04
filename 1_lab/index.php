<?php

require_once __DIR__ . "/src/controller/UserController.php";
require_once __DIR__ . "/src/store/connectToDatabase.php";

$pdo = connectToDatabase();
$userController = new UserController($pdo);
// $userController->index();