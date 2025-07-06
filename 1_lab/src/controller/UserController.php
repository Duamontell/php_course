<?php

declare(strict_types=1);

namespace App\controller;

use App\store\UserTable;
use App\store\User;
use RuntimeException;

class UserController
{
    private UserTable $userTable;

    public function __construct(\PDO $pdo)
    {
        $this->userTable = new UserTable($pdo);
    }

    public function index()
    {
        if (!isset($_GET["action"])) {
            $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
            if ($url == "/") {
                header("Location: ?action=registration");
                die();
            }
            header("Location: ?action=error");
            die();
        }

        $action = $_GET["action"];
        switch ($action) {
            case "/":
            case "registration":
                require_once  __DIR__ . "/../view/register_user.php";
                break;
            case "register":
                $this->registrationUser();
                break;
            case "profile":
                require_once __DIR__ . "/../view/show_user.php";
                break;
            case "error":
                require_once __DIR__ . "/../view/error.php";
                break;
            default:
                require_once __DIR__ . "/../view/error.php";
                break;
        }
    }


    public function registrationUser()
    {
        try {
            if (!$this->checkRequiredFields($_POST)) {
                throw new \RuntimeException("Обязательные поля должны быть заполнены!");
            }
            if (empty($_POST["middle_name"])) {
                $_POST["middle_name"] = null;
            }
            if (empty($_POST["phone"])) {
                $_POST["phone"] = null;
            }

            $params = $_POST;
            $params["avatar_path"] = null;

            $avatarPath = null;
            $avatarExtension = null;
            if (is_uploaded_file($_FILES["avatar"]["tmp_name"])) {
                $avatarPath = "avatar";
                $avatarExtension = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
                if (!$this->checkExtensions($avatarExtension)) {
                    throw new RuntimeException("Недопустимый формат аватара!");
                }
            }

            $userTable = $this->getUserTable();
            $con = $userTable->getPDO();
            $user = User::createUserFromParams(null, $params);
            try {

                $id = $userTable->saveUserToDatabase($con, $user);
                if ($avatarPath != null) {
                    $avatarPath = "avatar{$id}" . "." . $avatarExtension;
                    $uploadDir = "uploads/" . $avatarPath;
                    if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], $uploadDir)) {
                        throw new \RuntimeException("Ошибка сохранении аватара!");
                    }
                    $userTable->updateAvatarPath($con, $id, $avatarPath);
                }

                $redirectUrl = "?action=profile&user_id=$id";
                header("Location: " . $redirectUrl, true, 303);
            } catch (\PDOException) {
                throw new \RuntimeException("Пользователь с таким email или номером телефона уже сущестует");
            }
        } catch (\RuntimeException $e) {
            $message = $e->getMessage();
            $redirectUrl = "?action=error&msg=" . $message;
            header("Location: " . $redirectUrl, true, 303);
        }
    }

    private static function checkRequiredFields(array $ar): bool
    {
        return !empty($ar["first_name"])
            && !empty($ar["last_name"])
            && !empty($ar["gender"])
            && !empty($ar["birth_date"])
            && !empty($ar["email"]);
    }

    public static function checkExtensions(string $ext): bool
    {
        return $ext == "png" || $ext == "jpeg" || $ext == "gif";
    }

    public function getUserTable(): UserTable
    {
        return $this->userTable;
    }
}
