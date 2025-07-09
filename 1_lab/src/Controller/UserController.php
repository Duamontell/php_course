<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\UserTable;
use App\Model\Entity\User;
use App\Service\ImageService;

class UserController
{
	// Обращаться напрямую к полю класса
	private UserTable $userTable;
	private ImageService $imageService;

	public function __construct(\PDO $pdo)
	{
		$this->userTable = new UserTable($pdo);
		$this->imageService = new ImageService();
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
				$this->showUser();
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
				$this->showAdminPanel();
				break;
			case "error":
				$this->showErrorPage();
				break;
			default:
				$this->showErrorPage();
				break;
		}
	}

	private function showUser()
	{
		if (empty($_GET["user_id"])) {
			$this->redirectWithError("404: Запрашиваемая страница не найдена!");
		}

		$userId = (int) $_GET["user_id"];
		if (is_null($user = $this->userTable->findUserInDatabase($this->userTable->getPDO(), $userId))) {
			$this->redirectWithError("Такого пользователя не существует!");
		}

		$dateFromDB = new \DateTime($user->getBirthDate());
		$dateFormated = $dateFromDB->format("Y-m-d");
		require_once __DIR__ . "/../View/show_user.php";
	}

	private function showAdminPanel()
	{
		$users = $this->userTable->grabAllUsers($this->userTable->getPDO());
		require_once __DIR__ . "/../View/admin_panel.php";
	}

	private function showErrorPage()
	{
		$message = $_GET['msg'] ?? '"404: Запрашиваемая страница не найдена!"';
		require_once __DIR__ . "/../View/error.php";
	}

	private function registrationUser()
	{
		try {
			$params = $_POST;
			if (!$this->checkRequiredFields($params)) {
				throw new \RuntimeException("Обязательные поля должны быть заполнены!");
			}

			if (is_uploaded_file($_FILES["avatar"]["tmp_name"])) {
				$params["avatar_path"] = $this->imageService->saveUserAvatar($_FILES["avatar"]);
			} else {
				$params["avatar_path"] = null;
			}

			$user = User::createUserFromParams(null, $params);
			$id = $this->userTable->saveUserToDatabase($user);

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

			if (is_null($user = $this->userTable->findUserInDatabase($userId))) {
				$this->redirectWithError("Такого пользователя не существует!");
			}

			if (is_uploaded_file($_FILES["avatar"]["tmp_name"])) {
				// Удалить старую аватарку
				$params["avatar_path"] = $this->imageService->saveUserAvatar($_FILES["avatar"]);
			} else {
				$params["avatar_path"] = $user->getAvatarPath();
			}

			$updatedUser = User::createUserFromParams($userId, $params);

			$this->userTable->updateUserInDatabase($this->userTable->getPDO(), $updatedUser);

			$redirectUrl = "?action=profile&user_id=$userId";
			header("Location: " . $redirectUrl, true, 303);
			die();
		} catch (\PDOException $e) {
			$this->redirectWithError("Пользователь с таким email или номером телефона уже сущестует");
		} catch (\RuntimeException $e) {
			$this->redirectWithError($e->getMessage());
		}
	}

	private function deleteUser(int $userId)
	{
		if (is_null($this->userTable->findUserInDatabase($userId))) {
			$this->redirectWithError("Такого пользователя не существует!");
		}

		try {
			// Удалять аватар пользователя
			$this->userTable->deleteUserFromDatabase($userId);
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
			$ar["phone"] = null;
		};

		return !empty($ar["first_name"]) && strlen($ar["first_name"]) <= 50
			&& !empty($ar["last_name"]) && strlen($ar["last_name"]) <= 50
			&& strlen($ar["middle_name"]) <= 50
			&& !empty($ar["gender"])
			&& !empty($ar["birth_date"])
			&& !empty($ar["email"]) && strlen($ar["email"]) <= 75;
	}

	public static function redirectWithError(string $message)
	{
		$redirectUrl = "?action=error&msg=" . $message;
		header("Location: " . $redirectUrl, true, 303);
		die();
	}
}
