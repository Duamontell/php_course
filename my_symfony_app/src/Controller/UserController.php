<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\UserService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
    ) {}

    public function showRegistrationForm(): Response
    {
        return $this->render('register_user.html.twig', ['action' => 'register']);
    }

    public function registrationUser(Request $request): Response
    {
        $userInfo = $request->request->all();
        $avatarFile = $request->files->get('avatar');

        try {
            $id = $this->userService->registerUser($userInfo, $avatarFile);

            return $this->redirectToRoute('show_user', ['userId' => $id], Response::HTTP_SEE_OTHER);
        } catch (UniqueConstraintViolationException) {
            return $this->redirectToRoute('error_page', ['message' => 'Пользователь с таким email или номером телефона уже существует'], Response::HTTP_SEE_OTHER);
        } catch (\RuntimeException $e) {
            return $this->redirectToRoute('error_page', ['message' => $e->getMessage()], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('registration_page');
    }

    public function showUser(int $userId): Response
    {
        if (is_null($user = $this->userService->getUser($userId))) {
            return $this->redirectToRoute('error_page', ['message' => "Такого пользователя не существует!"], Response::HTTP_SEE_OTHER);
        }

        $birthDate = $user->getBirthDate();
        return $this->render('show_user.html.twig', [
            'userId'    => $userId,
            'user' => $user,
            'birthDate' => $birthDate->format('Y-m-d'),
        ]);
    }

    public function updateUserInfo(int $userId, Request $request): Response
    {
        try {
            $newUserInfo = $request->request->all();
            $avatarFile = $request->files->get('avatar');
            $this->userService->updateUserInfo($userId, $newUserInfo, $avatarFile);
        } catch (UniqueConstraintViolationException $e) {
            return $this->redirectToRoute('error_page', ['message' => 'Пользователь с таким email или номером телефона уже существует'], Response::HTTP_SEE_OTHER);
        } catch (\RuntimeException $e) {
            return $this->redirectToRoute('error_page', ['message' => $e->getMessage()], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('show_user', ['userId' => $userId]);
    }

    public function showAdminPanel(): Response
    {
        $users = $this->userService->getAllUsers();
        return $this->render('admin_panel.html.twig', ['users' => $users]);
    }

    public function deleteUser(int $userId): Response
    {
        try {
            $this->userService->deleteUser($userId);
        } catch (\RuntimeException $e) {
            return $this->redirectToRoute('error_page', ['message' => $e->getMessage()], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('registration_page', [], Response::HTTP_SEE_OTHER);
    }

    public function showError(Request $request): Response
    {
        $message = $request->query->get('message');
        return $this->render('error.html.twig', ['message' => $message]);
    }
}
