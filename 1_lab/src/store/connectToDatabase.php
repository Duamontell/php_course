<?php 

/**
 *   @return array{dsn:string,username:string,password:string}
 */
function getConnectionParams(): array
{
    $configPath = __DIR__ . "/../../config/config.json";
    if (!file_exists($configPath)) {
        throw new RuntimeException("Конфиг файл не найден!");
    }

    $jsonConfig = file_get_contents($configPath);

    return json_decode($jsonConfig, true);
}

function connectToDatabase(): ?PDO
{
    try {
        $dbConfig = getConnectionParams();
        $dsn = $dbConfig['dsn'];
        $userName = $dbConfig['userName'];
        $password = $dbConfig['password'];
        return new PDO($dsn, $userName, $password);
    } catch (PDOException $e) {
        return null;
    }
}