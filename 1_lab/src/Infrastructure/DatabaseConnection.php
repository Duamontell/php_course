<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Infrastructure\ConfigLoader;

// Почитать про инфрастуктуру!
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
