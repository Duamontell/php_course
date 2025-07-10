<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository
{
    private EntityManagerInterface $entityManager;

    // public function __construct(EntityManagerInterface $entityManager)
    // {
    //     $this->entityManager = $entityManager;
    // }

    public function store(User $user): int
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getUserId();
    }

    public function findById(int $id): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function delete(int $id): void
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function __construct(
        private \PDO $pdo
    ) {}

    function saveUserToDatabase(User $user): int 
    {
        $sql = <<<SQL
            INSERT INTO `user`
            (`first_name`, `last_name`, `middle_name`, `gender`, `birth_date`, `email`, `phone`, `avatar_path`)
            VALUES (:first_name, :last_name, :middle_name, :gender, :birth_date, :email, :phone, :avatar_path)
        SQL;

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":first_name" => $user->getFirstName(),
            ":last_name" => $user->getLastName(),
            ":middle_name" => $user->getMiddleName(),
            ":gender" => $user->getGender(),
            ":birth_date" => $user->getBirthDate(),
            ":email" => $user->getEmail(),
            ":phone" => $user->getPhone(),
            ":avatar_path" => $user->getAvatarPath(),
        ]);
        $lastId = $this->pdo->lastInsertId();
        if ($lastId == false) {
            throw new \RuntimeException("Ошибка в сохранении пользователя");
        }
        return (int)$lastId;
    }

    public function updateUserInDatabase(User $user)
    {
        $sql = <<<SQL
            UPDATE `user`
            SET `first_name`  = :first_name,
                `last_name`   = :last_name,
                `middle_name` = :middle_name,
                `gender`      = :gender,
                `birth_date`  = :birth_date,
                `email`       = :email,
                `phone`       = :phone,
                `avatar_path` = :avatar_path
            WHERE `user_id` = :user_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':first_name'  => $user->getFirstName(),
            ':last_name'   => $user->getLastName(),
            ':middle_name' => $user->getMiddleName(),
            ':gender'      => $user->getGender(),
            ':birth_date'  => $user->getBirthDate(),
            ':email'       => $user->getEmail(),
            ':phone'       => $user->getPhone(),
            ':avatar_path' => $user->getAvatarPath(),
            ':user_id'     => $user->getUserId()
        ]);
    }

    function findUserInDatabase(int $userId): ?User
    {
        $sql = <<<SQL
            SELECT `first_name`, `last_name`, `middle_name`, `gender`, `birth_date`, `email`, `phone`, `avatar_path`
            FROM `user`
            WHERE `user_id` = :user_id;
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":user_id" => $userId
        ]);

        $resultQuery = $stmt->fetch();
        if ($resultQuery === false) {
            return null;
        }

        $user = User::createUserFromParams($userId, $resultQuery);

        return $user;
    }

    /**
     * @return Users[]
     */
    public function grabAllUsers(): ?array
    {
        $sql = <<<SQL
            SELECT *
            FROM user
        SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $resultQuery = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($resultQuery === false) {
            return null;
        }

        $users = [];
        foreach ($resultQuery as $row) {
            $users[] = User::createUserfromParams($row["user_id"], $row);
        }


        return $users;
    }

    public function deleteUserFromDatabase(int $userId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM user WHERE user_id = :userId");
        $stmt->execute([":userId" => $userId]);
    }

    // public function updateAvatarPathInDatabase(int $userId, string $path)
    // {
    //     $stmt = $this->pdo->prepare("UPDATE user SET avatar_path = :path WHERE user_id = :userId");
    //     $stmt->execute([':path' => $path, ':userId' => $userId]);
    // }
}
