<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function loadUserByUsername(string $username)
    {
        $user = $this->repository->findByEmail($username);
        if ($user === null) {
            throw new UserNotFoundException($username);
        }
        return new SecurityUser($user->getUserId(), $user->getEmail(), $user->getPassword(), $user->getRole());
    }

    /**
     * @throws UserNotFoundException, если пользователь не найден
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->repository->findByEmail($identifier);
        if ($user === null) {
            throw new UserNotFoundException($identifier);
        }
        return new SecurityUser($user->getUserId(), $user->getEmail(), $user->getPassword(), $user->getRole());
    }

    /**
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof SecurityUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $currentUser = $this->repository->findByEmail($user->getUserIdentifier());
        if ($currentUser === null) {
            throw new UserNotFoundException($user->getUserIdentifier());
        }
        return new SecurityUser($user->getId(), $currentUser->getEmail(), $currentUser->getPassword(), $currentUser->getRole());
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass(string $class): bool
    {
        return $class === SecurityUser::class || is_subclass_of($class, SecurityUser::class);
    }
}
