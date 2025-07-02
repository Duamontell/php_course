<?php

require_once __DIR__ . '/../store/UserTable.php';

// Переделать на is_null
if (
    !isset($_POST["first_name"])
    || !isset($_POST["last_name"])
    || !isset($_POST["gender"])
    || !isset($_POST["birth_date"])
    || !isset($_POST["email"])
) {
    // Переделать на Exception
    $redirectUrl = "../view/error.php";
    header('Location: ' . $redirectUrl, true, 303);
}

$params = $_POST;
$userTable = new UserTable();
$con = $userTable->connectDatabase();

try {
    $id = saveUserToDatabase($con, $params);
} catch (PDOException $e) {
    
    //throw $th;
}
