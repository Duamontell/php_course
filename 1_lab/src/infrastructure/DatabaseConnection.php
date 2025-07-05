<?php

require_once __DIR__. "/ConfigLoader.php";

class DatabaseConnection
{
    public static function connectToDatabase(): PDO
    {
        // try {
        $dbConfig = ConfigLoader::configLoad();
        $dsn = $dbConfig['dsn'];
        $userName = $dbConfig['userName'];
        $password = $dbConfig['password'];
        return new PDO($dsn, $userName, $password);
        // } catch (PDOException $e) {
        //     return null;
        // }
    }
}
