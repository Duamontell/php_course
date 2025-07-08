<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\UserTable;
use App\Model\Entity\User;
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
			if (empty($_SERVER["QUERY_STRING"])) {
				echo ("sdsd");
				header("Location: ?action=registration_page");
				die();
			}
			$this->redirectWithError("Страница не найдена!");
		}

		$action = $_GET["action"];
		switch ($action) {
			case "/":
			case "registration_page":
				require_once __DIR__ . "/../View/register_user.php";
				break;
			case "register_user":
				$this->registrationUser();
				break;
			case "profile":
				require_once __DIR__ . "/../View/show_user.php";
				break;
			case "update":
				$userId = (int)$_GET["user_id"];
				$this->updateUserInfo($userId);
				break;
			case "delete_user":
				$userId = (int)$_GET["user_id"];
				$this->deleteUser($userId);
				break;
			case "admin_panel":
				require_once __DIR__ . "/../View/admin_panel.php";
				break;
			case "error":
				require_once __DIR__ . "/../View/error.php";
				break;
			default:
				require_once __DIR__ . "/../View/error.php";
				break;
		}
	}

	private function registrationUser()
	{
		try {
			$params = $_POST;
			if (!$this->checkRequiredFields($params)) {
				throw new \RuntimeException("Обязательные поля должны быть заполнены!");
			}

			$params["avatar_path"] = null;

			$avatar = $this->getUserAvatar();

			// Убрать лишние соединения с БД
			$userTable = $this->getUserTable();
			$con = $userTable->getPDO();
			$user = User::createUserFromParams(null, $params);
			$id = $userTable->saveUserToDatabase($con, $user);

			if ($avatar != null) {
				// Переместить в ImageService
				$newAvatarName = $this->generateAvatarFilename($id, $avatar["avatarExtension"]);
				$this->moveUserAvatar($_FILES["avatar"]["tmp_name"], $newAvatarName);
				$userTable->updateAvatarPathInDatabase($con, $id, $newAvatarName);
			}

			$redirectUrl = "?action=profile&user_id=$id";
			header("Location: " . $redirectUrl, true, 303);
			die();
		} catch (\PDOException) {
			$this->redirectWithError("Пользователь с таким email или номером телефона уже существует");
		} catch (\RuntimeException $e) {
			$this->redirectWithError($e->getMessage());
		}
	}

	private function updateUserInfo(int $userId)
	{
		try {
			$params = $_POST;
			if (!$this->checkRequiredFields($params)) {
				throw new \RuntimeException("Обязательные поля должны быть заполнены и/или не превышать лимит символов!");
			}

			$avatar = $this->getUserAvatar();

			$userTable = $this->getUserTable();
			$con = $userTable->getPDO();
			if (is_null($user = $userTable->findUserInDatabase($con, $userId))) {
				$this->redirectWithError("Такого пользователя не существует!");
			}

			$params["avatar_path"] = $user->getAvatarPath();
			$updatedUser = User::createUserFromParams($userId, $params);


			$userTable->updateUserInDatabase($con, $updatedUser);

			if ($avatar != null) {
				$newAvatarName = $this->generateAvatarFilename($userId, $avatar["avatarExtension"]);
				$this->moveUserAvatar($_FILES["avatar"]["tmp_name"], $newAvatarName);
				$userTable->updateAvatarPathInDatabase($con, $userId, $newAvatarName);
			}

			$redirectUrl = "?action=profile&user_id=$userId";
			header("Location: " . $redirectUrl, true, 303);
			die();
		} catch (\PDOException) {
			$this->redirectWithError("Пользователь с таким email или номером телефона уже сущестует");
		} catch (\RuntimeException $e) {
			$this->redirectWithError($e->getMessage());
		}
	}

	private function deleteUser(int $userId)
	{
		$userTable = $this->getUserTable();
		$con = $userTable->getPDO();
		if (is_null($userTable->findUserInDatabase($con, $userId))) {
			$this->redirectWithError("Такого пользователя не существует!");
		}

		try {
			$userTable->deleteUserFromDatabase($con, $userId);
			header("Location: " . "?action=registration_page", true, 303);
			die();
		} catch (\PDOException) {
			$this->redirectWithError("Ошибка удаления пользователя");
		} catch (\RuntimeException $e) {
			$this->redirectWithError($e->getMessage());
		}
	}

	private static function checkRequiredFields(array $ar): bool
	{
		if (empty($ar["middle_name"])) {
			$ar["middle_name"] = null;
		}
		if (empty($ar["phone"]) || $ar["phone"] == "") {
			echo ("DELAEM NULL ");
			$ar["phone"] = null;
		};

		return !empty($ar["first_name"]) && strlen($ar["first_name"]) <= 50
			&& !empty($ar["last_name"]) && strlen($ar["last_name"]) <= 50
			&& strlen($ar["middle_name"]) <= 50
			&& !empty($ar["gender"])
			&& !empty($ar["birth_date"])
			&& !empty($ar["email"]) && strlen($ar["email"]) <= 75;
	}

	private function getUserAvatar(): ?array
	{
		$avatar = [
			"avatarPath" => null,
			"avatarExtension" => null
		];

		if (is_uploaded_file($_FILES["avatar"]["tmp_name"])) {
			$avatar["avatarPath"] = "avatar";
			$avatar["avatarExtension"] = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
			if (!$this->checkFileExtensions($avatar["avatarExtension"])) {
				throw new RuntimeException("Недопустимый формат аватара!");
			}

			return $avatar;
		}

		return null;
	}

	private function generateAvatarFilename(int $userId, string $extension): string
	{
		return "avatar{$userId}" . "." . $extension;
	}

	private function moveUserAvatar(string $path, string $filename)
	{
		$uploadDir = "uploads/" . $filename;
		if (!move_uploaded_file($path, $uploadDir)) {
			throw new \RuntimeException("Ошибка сохранении аватара!");
		}
	}

	private static function checkFileExtensions(string $ext): bool
	{
		return in_array($ext, ["png", "jpeg", "gif"]);
	}

	public static function redirectWithError(string $message)
	{
		$redirectUrl = "?action=error&msg=" . $message;
		header("Location: " . $redirectUrl, true, 303);
		die();
	}

	public function getUserTable(): UserTable
	{
		return $this->userTable;
	}
}
