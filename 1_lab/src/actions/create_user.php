<?php

require_once __DIR__ . '/../store/UserTable.php';

try {
    if (
        empty($_POST["first_name"])
        || empty($_POST["last_name"])
        || empty($_POST["gender"])
        || empty($_POST["birth_date"])
        || empty($_POST["email"])
    ) {
        throw new RuntimeException("Обязательные поля должны быть заполнены!");
    }
    $params = $_POST;
    $userTable = new UserTable();
    $con = $userTable->connectDatabase();
    try {
        $id = $userTable->saveUserToDatabase($con, $params);
        $redirectUrl = "../view/show_user.php?user_id=$id";
        header("Location: " . $redirectUrl, true, 303);
    } catch (PDOException) {
        throw new RuntimeException("Пользователь с таким email или номером телефона уже сущестует");
    }
} catch (RuntimeException $e) {
    $message = $e->getMessage();
    $redirectUrl = "../view/error.php?msg=" . $message;
    header("Location: " . $redirectUrl, true, 303);
}
