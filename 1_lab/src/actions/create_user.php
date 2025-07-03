<?php

require_once __DIR__ . '/../store/UserTable.php';

function createUserFromParams(array $params): User
{
    return new User(
        null,
        $params["first_name"],
        $params["last_name"],
        $params["middle_name"],
        $params["gender"],
        $params["birth_date"],
        $params["email"],
        $params["phone"],
        $params["avatar_path"]
    );
}

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

    $userTable = new UserTable();
    $con = $userTable->connectDatabase();
    $user = createUserFromParams($params);

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
