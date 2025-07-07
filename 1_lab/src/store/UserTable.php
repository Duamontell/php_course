<?php

declare(strict_types=1);

namespace App\store;

use App\store\User;

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
		(int)$lastId = $pdo->lastInsertId();
		if ($lastId == false) {
			throw new \RuntimeException("Ошибка в сохранении пользователя");
		}
		return (int)$lastId;
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

	public function updateAvatarPath(\PDO $pdo, int $id, string $path)
	{
		$stmt = $pdo->prepare("UPDATE user SET avatar_path = :path WHERE user_id = :id");
		$stmt->execute([':path' => $path, ':id' => $id]);
	}

	public function getPDO()
	{
		return $this->pdo;
	}
}
