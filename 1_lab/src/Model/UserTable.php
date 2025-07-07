<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Entity\User;

class UserTable
{
	public function __construct(
		private \PDO $pdo
	) {}

	function saveUserToDatabase(\PDO $pdo, User $user): int
	{
		$sql = <<<SQL
            INSERT INTO `user`
            (`first_name`, `last_name`, `middle_name`, `gender`, `birth_date`, `email`, `phone`, `avatar_path`)
            VALUES (:first_name, :last_name, :middle_name, :gender, :birth_date, :email, :phone, :avatar_path) 
        SQL;

		$stmt = $pdo->prepare($sql);
		$user->getUserId();

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
		$lastId = $pdo->lastInsertId();
		if ($lastId == false) {
			throw new \RuntimeException("Ошибка в сохранении пользователя");
		}
		return (int)$lastId;
	}

	public function updateUserInDatabase(\PDO $pdo, User $user)
	{
		$sql = <<<SQL
        UPDATE `user`
        SET
            `first_name`  = :first_name,
            `last_name`   = :last_name,
            `middle_name` = :middle_name,
            `gender`      = :gender,
            `birth_date`  = :birth_date,
            `email`       = :email,
            `phone`       = :phone
        WHERE `user_id` = :user_id
    SQL;

		$stmt = $pdo->prepare($sql);
		$stmt->execute([
			':first_name'  => $user->getFirstName(),
			':last_name'   => $user->getLastName(),
			':middle_name' => $user->getMiddleName(),
			':gender'      => $user->getGender(),
			':birth_date'  => $user->getBirthDate(),
			':email'       => $user->getEmail(),
			':phone'       => $user->getPhone(),
			':user_id'     => $user->getUserId(),
		]);
	}

	function findUserInDatabase(\PDO $pdo, int $userId): ?User
	{
		$sql = <<<SQL
            SELECT `first_name`, `last_name`, `middle_name`, `gender`, `birth_date`, `email`, `phone`, `avatar_path`
            FROM `user`
            WHERE `user_id` = :user_id;
        SQL;

		$stmt = $pdo->prepare($sql);
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

	public function deleteUserFromDatabase(\PDO $pdo, int $userId)
	{
		$stmt = $pdo->prepare("DELETE FROM user WHERE user_id = :userId");
		$stmt->execute([":userId" => $userId]);
	}

	public function updateAvatarPathInDatabase(\PDO $pdo, int $userId, string $path)
	{
		$stmt = $pdo->prepare("UPDATE user SET avatar_path = :path WHERE user_id = :userId");
		$stmt->execute([':path' => $path, ':userId' => $userId]);
	}

	public function getPDO()
	{
		return $this->pdo;
	}
}
