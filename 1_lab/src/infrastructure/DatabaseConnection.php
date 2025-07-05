<?php

declare(strict_types=1);

namespace App\infrastructure;

use App\infrastructure\ConfigLoader;

class DatabaseConnection
{
    public static function connectToDatabase(): \PDO
    {
        $dbConfig = ConfigLoader::configLoad();
        $dsn = $dbConfig['dsn'];
        $userName = $dbConfig['userName'];
        $password = $dbConfig['password'];
        return new \PDO($dsn, $userName, $password);
    }
}
