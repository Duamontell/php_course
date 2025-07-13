<?php

declare(strict_types=1);

namespace App\Entity;

class User
{
    public function __construct(
        private ?int $userId,
        private string $firstName,
        private string $lastName,
        private ?string $middleName,
        private string $gender,
        private \DateTime $birthDate,
        private string $email,
        private ?string $phone,
        private ?string $avatarPath,
        private string $password,
        private int $role
    ) {}

    public static function createUserfromParams(?int $userId, array $params): User
    {
        $middleName = $params["middle_name"] != null ? $params["middle_name"] : null;
        $birthDate = new \DateTime($params["birth_date"]);
        $phone = $params["phone"] != null ? $params["phone"] : null;

        return new User(
            $userId,
            $params["first_name"],
            $params["last_name"],
            $middleName,
            $params["gender"],
            $birthDate,
            $params["email"],
            $phone,
            $params["avatar_path"],
            "",
            1
        );
    }

    public function setFirstName(string $str)
    {
        $this->firstName = $str;
    }

    public function setLastName(string $str)
    {
        $this->lastName = $str;
    }

    public function setMiddleName(string $str)
    {
        $this->middleName = $str;
    }

    public function setGender(string $str)
    {
        $this->gender = $str;
    }

    public function setBirthDate(\DateTime $str)
    {
        $this->birthDate = $str;
    }

    public function setEmail(string $str)
    {
        $this->email = $str;
    }

    public function setPhone(string $str)
    {
        $this->phone = $str;
    }

    public function setAvatarPath(string $str)
    {
        $this->avatarPath = $str;
    }

    public function setPassword(string $str)
    {
        $this->password = $str;
    }

    public function set(string $str)
    {
        $this->avatarPath = $str;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getBirthDate(): \DateTime
    {
        return $this->birthDate;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): int
    {
        return $this->role;
    }
}
