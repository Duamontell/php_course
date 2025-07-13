<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class PasswordHasher implements PasswordHasherInterface
{
    private const SALT = '_salt';

    use CheckPasswordLengthTrait;

    public function hash(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    // public function hash(string $plainPassword): string
    // {
    //     if ($this->isPasswordTooLong($plainPassword))
    //     {
    //         throw new InvalidPasswordException();
    //     }

    //     return $this->encodePassword($plainPassword);
    // }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        if ('' === $plainPassword || $this->isPasswordTooLong($plainPassword)) {
            return false;
        }
        return password_verify($plainPassword, $hashedPassword);
    }

    // public function verify(string $hashedPassword, string $plainPassword): bool
    // {
    //     if ('' === $plainPassword || $this->isPasswordTooLong($plainPassword))
    //     {
    //         return false;
    //     }

    //     return $this->encodePassword($plainPassword) === $hashedPassword;
    // }

    public function needsRehash(string $hashedPassword): bool
    {
        return password_needs_rehash($hashedPassword, PASSWORD_DEFAULT);
    }

    // public function needsRehash(string $hashedPassword): bool
    // {
    //     return false;
    // }

    private function encodePassword(string $password): string
    {
        return md5($password . self::SALT);
    }
}
