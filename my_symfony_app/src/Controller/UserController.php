<?php

namespace App\Controller;

use App\Infrastructure\DatabaseConnection;
use App\Infrastructure\ConfigLoader;
use App\Model\UserTable;
use App\Model\Entity\User;
use App\Service\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
	private UserTable $userTable;
	private ImageService $imageService;

	public function __construct(
		DatabaseConnection $dbConnection,
		ConfigLoader $configLoader,
		ImageService $imageService
	) {
		$pdo = DatabaseConnection::connectToDatabase();
		$this->userTable = new UserTable($pdo);
		$this->imageService = $imageService;
	}

	/**
	 * Отобразить форму регистрации
	 */
	public function showRegistrationForm(): Response
	{
		return $this->render('register_user.html.twig', ['action' => 'register']);
	}

	/**
	 * Обработать регистрацию
	 */
	public function registrationUser(Request $request): Response
	{
		$params = $request->request->all();
		try {
			if (!$this->checkRequiredFields($params)) {
				throw new \RuntimeException('Обязательные поля должны быть заполнены!');
			}

			$avatarFile = $request->files->get('avatar');
			if ($avatarFile && $avatarFile->isValid()) {
				$fileArray = [
					'tmp_name' => $avatarFile->getPathname(),
					'name'     => $avatarFile->getClientOriginalName(),
					'type'     => $avatarFile->getClientMimeType(),
					'error'    => $avatarFile->getError(),
					'size'     => $avatarFile->getSize(),
				];
				// $params['avatar_path'] = $this->imageService->saveUserAvatar($fileArray);
				$params['avatar_path'] = $this->imageService->saveUserAvatar($fileArray);
			} else {
				$params['avatar_path'] = null;
			}

			$user = User::createUserFromParams(null, $params);
			$id = $this->userTable->saveUserToDatabase($this->getUserTable()->getPDO(), $user);

			return $this->redirectToRoute('show_user', ['user_id' => $id], Response::HTTP_SEE_OTHER);
		} catch (\PDOException) {
			$this->addFlash('error', 'Пользователь с таким email или номером телефона уже существует');
		} catch (\RuntimeException $e) {
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('registration_page');
	}

	/**
	 * Показать профиль пользователя
	 */
	public function showUser(int $user_id): Response
	{
		$user = $this->userTable->findUserInDatabase($this->getUserTable()->getPDO(), $user_id);
		if (!$user) {
			$this->addFlash('error', 'Такого пользователя не существует!');
			return $this->redirectToRoute('registration_page');
		}

		$birthDate = new \DateTime($user->getBirthDate());
		return $this->render('show_user.html.twig', [
			'user' => $user,
			'birthDate' => $birthDate->format('Y-m-d'),
		]);
	}

	/**
	 * Обновить информацию о пользователе
	 */
	public function updateUserInfo(int $user_id, Request $request): Response
	{
		try {
			$params = $request->request->all();
			if (!$this->checkRequiredFields($params)) {
				throw new \RuntimeException('Обязательные поля не заполнены или превышают лимит!');
			}
			$existingUser = $this->userTable->findUserInDatabase($this->getUserTable()->getPDO(), $user_id);
			if (!$existingUser) {
				$this->addFlash('error', 'Такого пользователя не существует!');
				return $this->redirectToRoute('show_user', ['user_id' => $user_id]);
			}

			$avatarFile = $request->files->get('avatar');
			if ($avatarFile && $avatarFile->isValid()) {
				$params['avatar_path'] = $this->imageService->saveUserAvatar($avatarFile);
			} else {
				$params['avatar_path'] = $existingUser->getAvatarPath();
			}

			$updatedUser = User::createUserFromParams($user_id, $params);
			$this->userTable->updateUserInDatabase($this->getUserTable()->getPDO(), $updatedUser);

			return $this->redirectToRoute('show_user', ['user_id' => $user_id], Response::HTTP_SEE_OTHER);
		} catch (\PDOException) {
			$this->addFlash('error', 'Пользователь с таким email или телефоном уже существует');
		} catch (\RuntimeException $e) {
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('show_user', ['user_id' => $user_id]);
	}

	/**
	 * Админ-панель: список пользователей
	 */
	public function showAdminPanel(): Response
	{
		$users = $this->userTable->grabAllUsers($this->getUserTable()->getPDO());
		return $this->render('admin_panel.html.twig', ['users' => $users]);
	}

	/**
	 * Удалить пользователя
	 */
	public function deleteUser(int $user_id): Response
	{
		$user = $this->userTable->findUserInDatabase($this->getUserTable()->getPDO(), $user_id);
		if (!$user) {
			$this->addFlash('error', 'Пользователь не найден');
		} else {
			try {
				$this->userTable->deleteUserFromDatabase($this->getUserTable()->getPDO(), $user_id);
				$this->addFlash('success', 'Пользователь удалён');
			} catch (\Exception) {
				$this->addFlash('error', 'Ошибка при удалении пользователя');
			}
		}
		return $this->redirectToRoute('registration_page', [], Response::HTTP_SEE_OTHER);
	}

	/**
	 * Проверка обязательных полей
	 */
	private function checkRequiredFields(array $ar): bool
	{
		if (empty($ar['middle_name'])) {
			$ar['middle_name'] = null;
		}
		if (empty($ar['phone'])) {
			$ar['phone'] = null;
		}

		return !empty($ar['first_name']) && mb_strlen($ar['first_name']) <= 50
			&& !empty($ar['last_name']) && mb_strlen($ar['last_name']) <= 50
			&& mb_strlen((string) $ar['middle_name']) <= 50
			&& !empty($ar['gender'])
			&& !empty($ar['birth_date'])
			&& !empty($ar['email']) && mb_strlen($ar['email']) <= 75;
	}

	private function getUserTable(): UserTable
	{
		return $this->userTable;
	}
}
