<?php

declare(strict_types=1);

namespace App\Entity;

class UserRole
{
    public const USER = 0;
    public const ADMIN = 1;

    public static function isValid(int $role): bool
    {
        return $role === self::USER || $role === self::ADMIN;
    }
}
