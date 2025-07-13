<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;
use App\Service\ImageService;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private ImageService   $imageService,
    ) {}

    public function registerUser(array $userInfo, ?UploadedFile $avatarFile): int
    {
        if (!$this->checkRequiredFields($userInfo)) {
            throw new \RuntimeException('Обязательные поля должны быть заполнены!');
        }

        if ($avatarFile) {
            $fileArray = [
                'tmp_name' => $avatarFile->getPathname(),
                'name'     => $avatarFile->getClientOriginalName(),
                'type'     => $avatarFile->getClientMimeType(),
                'error'    => $avatarFile->getError(),
                'size'     => $avatarFile->getSize(),
            ];
            $userInfo['avatar_path'] = $this->imageService->saveImage($fileArray);
        } else {
            $userInfo['avatar_path'] = null;
        }

        $user = User::createUserFromParams(null, $userInfo);
        return $this->userRepository->store($user);
    }

    public function updateUserInfo(int $userId, array $newUserInfo, ?UploadedFile $avatarFile)
    {
        if (!$this->checkRequiredFields($newUserInfo)) {
            throw new \RuntimeException('Обязательные поля не заполнены или превышают лимит символов!');
        }
        if (!$user = $this->userRepository->findById($userId)) {
            throw new \RuntimeException("Такого пользователя не существует!");
        }

        if ($avatarFile) {
            if ($user->getAvatarPath() != null) {
                if (!$this->imageService->deleteImage($user->getAvatarPath())) {
                    throw new \RuntimeException("Ошибка удаления аватара!");
                }
            }
            $fileArray = [
                'tmp_name' => $avatarFile->getPathname(),
                'name'     => $avatarFile->getClientOriginalName(),
                'type'     => $avatarFile->getClientMimeType(),
                'error'    => $avatarFile->getError(),
                'size'     => $avatarFile->getSize(),
            ];
            $newUserInfo['avatar_path'] = $this->imageService->saveImage($fileArray);
        } else {
            $newUserInfo['avatar_path'] = $user->getAvatarPath();
        }

        $user->setFirstName($newUserInfo['first_name']);
        $user->setLastName($newUserInfo['last_name']);
        $user->setMiddleName($newUserInfo['middle_name'] ?? null);
        $user->setGender($newUserInfo['gender']);
        $user->setBirthDate(new \DateTime($newUserInfo['birth_date']));
        $user->setEmail($newUserInfo['email']);
        $user->setPhone($newUserInfo['phone'] ?? null);
        $user->setAvatarPath($newUserInfo['avatar_path']);

        $this->userRepository->store($user);
    }

    public function deleteUser(int $userId)
    {
        if (!$user = $this->userRepository->findById($userId)) {
            throw new \RuntimeException("message' => 'Пользователь не найден");
        } else {
            $this->userRepository->delete($userId);
            if (!$this->imageService->deleteImage($user->getAvatarPath())) {
                throw new \RuntimeException("Ошибка удаления аватара!");
            }
        }
    }

    public function getUser(int $userId): ?User
    {
        $user = $this->userRepository->findById($userId);
        if ($user !== null) {
            return new User(
                $user->getUserId(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getMiddleName(),
                $user->getGender(),
                $user->getBirthDate(),
                $user->getEmail(),
                $user->getPhone(),
                $user->getAvatarPath(),
                // $user->getPassword(),
                "1",
                // $user->getRole()
                0
            );
        } else {
            return null;
        }
    }

    public function getAllUsers(): ?array
    {
        return $this->userRepository->findAll();
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
}
