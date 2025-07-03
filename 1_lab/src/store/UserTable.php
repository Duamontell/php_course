<?php

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

    function saveUserToDatabase(PDO $pdo, array $userParams): int
    {
        $sql = <<<SQL
            INSERT INTO `user`
            (`first_name`, `last_name`, `middle_name`, `gender`, `birth_date`, `email`, `phone`, `avatar_path`)
            VALUES (:first_name, :last_name, :middle_name, :gender, :birth_date, :email, :phone, :avatar_path) 
        SQL;

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":first_name" => $userParams["first_name"],
            ":last_name" => $userParams["last_name"],
            ":middle_name" => $userParams["middle_name"],
            ":gender" => $userParams["gender"],
            ":birth_date" => $userParams["birth_date"],
            ":email" => $userParams["email"],
            ":phone" => $userParams["phone"],
            ":avatar_path" => $userParams["avatar_path"],
        ]);
        (int)$lastId = $pdo->lastInsertId();
        if ($lastId == false) {
            throw new RuntimeException("Ошибка в сохранении пользователя");
        }
        return $lastId;
    }

    function findUserInDatabase(PDO $pdo, int $userId): ?array
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

        $result = $stmt->fetch();
        if ($result === false) {
            return null;
        }

        return $result;
    }
}
