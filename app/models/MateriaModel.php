<?php

namespace App\Models;

use App\Config\Database;

class MateriaModel
{
    public static function getAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT * FROM materias ORDER BY nombre ASC');
        return $stmt->fetchAll();
    }

    public static function create(array $data): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            INSERT INTO materias (codigo, nombre, descripcion, area, activa)
            VALUES (:codigo, :nombre, :descripcion, :area, :activa)
        ');

        return $stmt->execute([
            ':codigo' => trim((string)($data['codigo'] ?? '')),
            ':nombre' => trim((string)($data['nombre'] ?? '')),
            ':descripcion' => trim((string)($data['descripcion'] ?? '')),
            ':area' => trim((string)($data['area'] ?? '')),
            ':activa' => isset($data['activa']) ? (int)$data['activa'] : 1,
        ]);
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            UPDATE materias
            SET codigo = :codigo,
                nombre = :nombre,
                descripcion = :descripcion,
                area = :area
            WHERE id_materia = :id
        ');

        return $stmt->execute([
            ':codigo' => trim((string)($data['codigo'] ?? '')),
            ':nombre' => trim((string)($data['nombre'] ?? '')),
            ':descripcion' => trim((string)($data['descripcion'] ?? '')),
            ':area' => trim((string)($data['area'] ?? '')),
            ':id' => $id,
        ]);
    }

    public static function toggleStatus(int $id, int $activa): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE materias SET activa = :activa WHERE id_materia = :id');

        return $stmt->execute([
            ':activa' => $activa ? 1 : 0,
            ':id' => $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM materias WHERE id_materia = :id');

        return $stmt->execute([
            ':id' => $id,
        ]);
    }
}
