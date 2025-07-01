<?php

class ConnectionProvider {
    public function connectDatabase(): PDO
    {
        $dbConfig = self::getConnectionParams();
        $dsn = $dbConfig['dsn'];
        $userName = $dbConfig['userName'];
        $password = $dbConfig['password'];
        
        return new PDO($dsn, $userName, $password);
    }


    /**
    *   @return array{dsn:string,username:string,password:string}
    */
    private function getConnectionParams(): array
    {
        $jsonConfig = file_get_contents(__DIR__. '/config.json');
    
        return json_decode($jsonConfig, true);
    }
}