<?php

class ConfigLoader
{
    /**
     *   @return array{dsn:string,username:string,password:string}
     */
    public static function configLoad(): ?array
    {
        $configPath = __DIR__ . "/../../config/config.json";
        if (!file_exists($configPath)) {
            throw new RuntimeException("Конфиг файл не найден!");
        }

        $jsonConfig = file_get_contents($configPath);

        return json_decode($jsonConfig, true);
    }
}
