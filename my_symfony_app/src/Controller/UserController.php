<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\DatabaseConnection;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Service\ImageService;
use RuntimeException;

class UserController extends AbstractController
{
	private UserRepository $userTable;
	private ImageService $imageService;

	public function __construct(
		ImageService $imageService
	) {
		$pdo = DatabaseConnection::connectToDatabase();
		$this->userTable = new UserRepository($pdo);
		$this->imageService = $imageService;
	}

	public function showRegistrationForm(): Response
	{
		return $this->render('register_user.html.twig', ['action' => 'register']);
	}

	public function registrationUser(Request $request): Response
	{
		$params = $request->request->all();
		try {
			if (!$this->checkRequiredFields($params)) {
				throw new \RuntimeException('Обязательные поля должны быть заполнены!');
			}

			$avatarFile = $request->files->get('avatar');
			if ($avatarFile) {
				$fileArray = [
					'tmp_name' => $avatarFile->getPathname(),
					'name'     => $avatarFile->getClientOriginalName(),
					'type'     => $avatarFile->getClientMimeType(),
					'error'    => $avatarFile->getError(),
					'size'     => $avatarFile->getSize(),
				];
				$params['avatar_path'] = $this->imageService->saveImage($fileArray);
			} else {
				$params['avatar_path'] = null;
			}

			$user = User::createUserFromParams(null, $params);
			$id = $this->userTable->saveUserToDatabase($user);

			return $this->redirectToRoute('show_user', ['userId' => $id], Response::HTTP_SEE_OTHER);
		} catch (\PDOException) {
			return $this->redirectToRoute('error_page', ['message' => 'Пользователь с таким email или номером телефона уже существует'], Response::HTTP_SEE_OTHER);
		} catch (\RuntimeException $e) {
			return $this->redirectToRoute('error_page', ['message' => $e->getMessage()], Response::HTTP_SEE_OTHER);
		}

		return $this->redirectToRoute('registration_page');
	}

	public function showUser(int $userId): Response
	{
		if (is_null($user = $this->userTable->findUserInDatabase($userId))) {
			return $this->redirectToRoute('error_page', ['message' => "Такого пользователя не существует!"], Response::HTTP_SEE_OTHER);
		}

		$birthDate = new \DateTime($user->getBirthDate());
		return $this->render('show_user.html.twig', [
			'userId'    => $userId,
			'user' => $user,
			'birthDate' => $birthDate->format('Y-m-d'),
		]);
	}

	public function updateUserInfo(int $userId, Request $request): Response
	{
		try {
			$params = $request->request->all();
			if (!$this->checkRequiredFields($params)) {
				throw new \RuntimeException('Обязательные поля не заполнены или превышают лимит символов!');
			}
			if (!$user = $this->userTable->findUserInDatabase($userId)) {
				return $this->redirectToRoute('error_page', ['message' => "Такого пользователя не существует!"], Response::HTTP_SEE_OTHER);
			}

			$avatarFile = $request->files->get('avatar');
			if ($avatarFile) {
				if ($user->getAvatarPath() != null) {
					if (!$this->imageService->deleteImage($user->getAvatarPath())) {
						throw new RuntimeException("Ошибка удаления аватара!");
					}
				}
				$fileArray = [
					'tmp_name' => $avatarFile->getPathname(),
					'name'     => $avatarFile->getClientOriginalName(),
					'type'     => $avatarFile->getClientMimeType(),
					'error'    => $avatarFile->getError(),
					'size'     => $avatarFile->getSize(),
				];
				$params['avatar_path'] = $this->imageService->saveImage($fileArray);
			} else {
				$params['avatar_path'] = $user->getAvatarPath();
			}

			$updatedUser = User::createUserFromParams($userId, $params);
			$this->userTable->updateUserInDatabase($updatedUser);
		} catch (\PDOException) {
			return $this->redirectToRoute('error_page', ['message' => 'Пользователь с таким email или номером телефона уже существует'], Response::HTTP_SEE_OTHER);
		} catch (\RuntimeException $e) {
			return $this->redirectToRoute('error_page', ['message' => $e->getMessage()], Response::HTTP_SEE_OTHER);
		}

		return $this->redirectToRoute('show_user', ['userId' => $userId]);
	}

	public function showAdminPanel(): Response
	{
		$users = $this->userTable->grabAllUsers();
		return $this->render('admin_panel.html.twig', ['users' => $users]);
	}

	public function deleteUser(int $userId): Response
	{
		if (!$user = $this->userTable->findUserInDatabase($userId)) {
			return $this->redirectToRoute('error_page', ['message' => 'Пользователь не найден'], Response::HTTP_SEE_OTHER);
		} else {
			try {
				$this->userTable->deleteUserFromDatabase($userId);
				if (!$this->imageService->deleteImage($user->getAvatarPath())) {
					throw new RuntimeException("Ошибка удаления аватара!");
				}
			} catch (\PDOException) {
				return $this->redirectToRoute('error_page', ['message' => "Ошибка при удалении пользователя"], Response::HTTP_SEE_OTHER);
			} catch (\RuntimeException $e) {
				return $this->redirectToRoute('error_page', ['message' => $e->getMessage()], Response::HTTP_SEE_OTHER);
			}
		}
		return $this->redirectToRoute('registration_page', [], Response::HTTP_SEE_OTHER);
	}

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

	public function showError(Request $request): Response
	{
		$message = $request->query->get('message');
		return $this->render('error.html.twig', ['message' => $message]);
	}
}
