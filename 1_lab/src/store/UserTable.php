<?php

function saveUserToDatabase(PDO $pdo, array $userParams): int
{
    $sql = <<<SQL
        INSERT INTO `user`
        (`first_name`, `last_name`, `middle_name`, `gender`, `birth_date`, `email`, `phone`, `avatar_path`)
        VALUES (:first_name, :last_name, :middle_name, :gender, :birth_date, :email, :phone, :avatar_path) 
        SQL;

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(":first_name", $userParams["first_name"]);
    $stmt->bindValue(":last_name", $userParams["last_name"]);
    $stmt->bindValue(":middle_name", $userParams["middle_name"]);
    $stmt->bindValue(":gender", $userParams["gender"]);
    $stmt->bindValue(":birth_date", $userParams["birth_date"]);
    $stmt->bindValue(":email", $userParams["email"]);
    $stmt->bindValue(":phone", $userParams["phone"]);
    $stmt->bindValue(":avatar_path", $userParams["avatar_path"]);


    $stmt->execute();
    (int)$lastId = $pdo->lastInsertId();
    if ($lastId == false) {
        throw new RuntimeException("Ошибка в сохранении пользователя");
    }
    return $lastId;

    // try {
    //     $stmt->execute();
    //     (int)$lastId = $pdo->lastInsertId();
    //     if ($lastId == false) {
    //         throw new RuntimeException("Ошибка в сохранении пользователя");
    //     }
    //     return $lastId;
    // } catch (PDOException $e) {
    //     if ($stmt->errorCode() == 23000) {
    //         $redirectUrl = "../view/error.php";
    //         header('Location: ' . $redirectUrl, true, 303);
    //         throw new RuntimeException("Пользователь с таким email или номером телефона уже сущестует");
    //     }
    // }

    return 0;
}


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
        $jsonConfig = file_get_contents(__DIR__ . '/../../config/config.json');

        return json_decode($jsonConfig, true);
    }
}

// echo ("sdsd");
// $userTable = new UserTable();
// $con = $userTable->connectDatabase();
// $array = [
//     "first_name" => "Maksim",
//     "last_name" => "Ivanov",
//     "middle_name" => "Viktorovich",
//     "gender" => "man",
//     "birth_date" => "15-03-2005",
//     "email" => "maks@gmail.com",
//     "phone" => "+79000000",
//     "avatar_path" => "/path/to/avatar.png",
// ];
// $row = saveUserToDatabase($con, $array);
// echo ($row);
