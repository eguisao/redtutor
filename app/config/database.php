<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    public static function getConnection()
    {
        $host = env('DB_HOST', 'localhost');
        $dbName = env('DB_NAME', 'redtutor');
        $user = env('DB_USER', 'root');
        $pass = env('DB_PASS', '');
        $charset = env('DB_CHARSET', 'utf8mb4');

        $dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException('No se pudo conectar a la base de datos: ' . $e->getMessage());
        }
    }
}
