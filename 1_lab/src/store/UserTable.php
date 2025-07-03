<?php

require_once __DIR__ . "/User.php";

class UserTable
{
    public function connectDatabase(): ?PDO
    {
        try {
            $dbConfig = self::getConnectionParams();
            $dsn = $dbConfig['dsn'];
            $userName = $dbConfig['userName'];
            $password = $dbConfig['password'];

            return new PDO($dsn, $userName, $password);
        } catch (PDOException $e) {
            echo ("Connection failed: " . $e->getMessage());
            return null;
        }
    }


    /**
     *   @return array{dsn:string,username:string,password:string}
     */
    private function getConnectionParams(): array
    {
        // Проверка на файл
        $configPath = __DIR__ . "/../../config/config.json";
        if (!file_exists($configPath)) {
            throw new RuntimeException("Конфиг файл не найден!");
        }

        $jsonConfig = file_get_contents($configPath);

        return json_decode($jsonConfig, true);
    }

    function saveUserToDatabase(PDO $pdo, User $user): int
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
            throw new RuntimeException("Ошибка в сохранении пользователя");
        }
        return $lastId;
    }

    function findUserInDatabase(PDO $pdo, int $userId): ?User
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
}
