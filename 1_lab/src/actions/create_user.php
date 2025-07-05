<?php

// try {
//     if (
//         empty($_POST["first_name"])
//         || empty($_POST["last_name"])
//         || empty($_POST["gender"])
//         || empty($_POST["birth_date"])
//         || empty($_POST["email"])
//     ) {
//         throw new RuntimeException("Обязательные поля должны быть заполнены!");
//     }

//     $params = $_POST;

//     $uploadDir = "uploads/";
//     $destination = $uploadDir . basename($_FILES["avatar"]["name"]);
//     $params["avatar_path"] = $destination;
//     if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], $destination)) {
//         echo ($destination);
//         throw new RuntimeException("Ошибка сохранении аватара!");
//     }

//     $userTable = $this->getUserTable();
//     $con = $userTable->getPDO();
//     $user = User::createUserFromParams(null, $params);

//     try {
//         $id = $userTable->saveUserToDatabase($con, $user);

//         $redirectUrl = "?action=profile&user_id=$id";
//         header("Location: " . $redirectUrl, true, 303);
//     } catch (PDOException) {
//         throw new RuntimeException("Пользователь с таким email или номером телефона уже сущестует");
//     }
// } catch (RuntimeException $e) {
//     $message = $e->getMessage();
//     $redirectUrl = "action=?msg=" . $message;
//     header("Location: " . $redirectUrl, true, 303);
// }