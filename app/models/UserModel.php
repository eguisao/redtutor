<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class UserModel
{
    public static function syncAuth0User(array $auth0User): array
    {
        $email = strtolower(trim((string) ($auth0User['email'] ?? '')));

        if ($email === '') {
            throw new \InvalidArgumentException('Faltan datos esenciales del usuario autenticado en Auth0.');
        }

        $pdo = Database::getConnection();
        self::ensureSimplifiedSchema($pdo);

        $existing = self::findByEmail($email);
        if ($existing) {
            return $existing;
        }

        $byEmail = $pdo->prepare('SELECT * FROM usuarios WHERE correo = :correo LIMIT 1');
        $byEmail->execute([':correo' => $email]);
        $existingByEmail = $byEmail->fetch();

        if ($existingByEmail) {
            throw new \RuntimeException('Este email ya existe. No se puede crear un nuevo usuario con el mismo correo.');
        }

        $insert = $pdo->prepare('INSERT INTO usuarios (correo, fecha_creado) VALUES (:correo, NOW())');
        $insert->execute([':correo' => $email]);

        $id = (int) $pdo->lastInsertId();

        return [
            'id_usuario' => $id,
            'correo' => $email,
            'fecha_creado' => date('Y-m-d H:i:s'),
        ];
    }

    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE correo = :correo LIMIT 1');
        $stmt->execute([':correo' => strtolower(trim($email))]);

        $user = $stmt->fetch();
        return $user ?: null;
    }

    private static function ensureSimplifiedSchema(PDO $pdo): void
    {
        $columns = $pdo->query('SHOW COLUMNS FROM usuarios')->fetchAll();
        $columnNames = [];

        foreach ($columns as $column) {
            $columnNames[] = $column['Field'];
        }

        if (!in_array('correo', $columnNames, true)) {
            $pdo->exec('ALTER TABLE usuarios ADD COLUMN correo VARCHAR(180) NOT NULL AFTER id_usuario');
        }

        if (!in_array('fecha_creado', $columnNames, true)) {
            $pdo->exec('ALTER TABLE usuarios ADD COLUMN fecha_creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER correo');
        }

        foreach (['nombre_usuario', 'auth0_id', 'id_tipo_usuario'] as $column) {
            if (in_array($column, $columnNames, true)) {
                $pdo->exec('ALTER TABLE usuarios DROP COLUMN ' . $column);
            }
        }

        $indexes = $pdo->query('SHOW INDEX FROM usuarios')->fetchAll();
        $hasUniqueCorreo = false;

        foreach ($indexes as $index) {
            if (($index['Key_name'] ?? '') === 'uq_correo') {
                $hasUniqueCorreo = true;
                break;
            }
        }

        if (!$hasUniqueCorreo) {
            $pdo->exec('CREATE UNIQUE INDEX uq_correo ON usuarios(correo)');
        }
    }
}
