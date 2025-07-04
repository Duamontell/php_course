<?php

require_once __DIR__ . "/../store/UserTable.php";
require_once __DIR__ . "/../../index.php";

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

    $uploadDir = "../../public/";
    $destination = $uploadDir . basename($_FILES["avatar"]["name"]);
    $params["avatar_path"] = $destination;
    if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], $destination)) {
        throw new RuntimeException("Ошибка сохранении аватара!");
    }

    $userTable = $userController->getUserTable();
    $con = $userTable->getPDO();
    $user = User::createUserFromParams($null, $params);

    try {
        $id = $userTable->saveUserToDatabase($con, $user);

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
