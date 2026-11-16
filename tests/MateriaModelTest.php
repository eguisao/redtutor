<?php

use App\Config\Database;
use App\Models\MateriaModel;
use PHPUnit\Framework\TestCase;

class MateriaModelTest extends TestCase
{
    protected function setUp(): void
    {
        $this->prepareDatabase();
    }

    protected function tearDown(): void
    {
        $this->dropDatabase();
    }

    public function testCreateUpdateToggleDeleteAndListMaterias(): void
    {
        $created = MateriaModel::create([
            'codigo' => ' MAT-101 ',
            'nombre' => ' Matemáticas Básicas ',
            'descripcion' => ' Fundamentos ',
            'area' => ' Ciencias Exactas ',
            'activa' => 1,
        ]);

        $this->assertTrue($created);

        $materias = MateriaModel::getAll();
        $this->assertCount(1, $materias);
        $this->assertSame('MAT-101', $materias[0]['codigo']);
        $this->assertSame('Matemáticas Básicas', $materias[0]['nombre']);
        $this->assertSame('1', (string) $materias[0]['activa']);

        $id = (int) $materias[0]['id_materia'];

        $updated = MateriaModel::update($id, [
            'codigo' => 'MAT-202',
            'nombre' => 'Física General',
            'descripcion' => 'Movimiento y energía',
            'area' => 'Ciencias',
        ]);

        $this->assertTrue($updated);

        $afterUpdate = MateriaModel::getAll();
        $this->assertSame('MAT-202', $afterUpdate[0]['codigo']);
        $this->assertSame('Física General', $afterUpdate[0]['nombre']);

        $toggled = MateriaModel::toggleStatus($id, 0);
        $this->assertTrue($toggled);

        $row = Database::getConnection()->query("SELECT activa FROM materias WHERE id_materia = {$id}")->fetch();
        $this->assertSame('0', (string) $row['activa']);

        $deleted = MateriaModel::delete($id);
        $this->assertTrue($deleted);
        $this->assertEmpty(MateriaModel::getAll());
    }

    private function prepareDatabase(): void
    {
        $this->setTestEnvironment();

        $conn = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
        $conn->exec('DROP DATABASE IF EXISTS redtutor_test');
        $conn->exec('CREATE DATABASE redtutor_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        $pdo = Database::getConnection();
        $pdo->exec('CREATE TABLE materias (
            id_materia INT NOT NULL AUTO_INCREMENT,
            codigo VARCHAR(20) NOT NULL,
            nombre VARCHAR(120) NOT NULL,
            descripcion VARCHAR(255) DEFAULT NULL,
            area VARCHAR(80) DEFAULT NULL,
            activa TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id_materia)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    }

    private function dropDatabase(): void
    {
        $conn = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
        $conn->exec('DROP DATABASE IF EXISTS redtutor_test');
    }

    private function setTestEnvironment(): void
    {
        $_ENV['DB_HOST'] = 'localhost';
        $_ENV['DB_NAME'] = 'redtutor_test';
        $_ENV['DB_USER'] = 'root';
        $_ENV['DB_PASS'] = '';
        $_ENV['DB_CHARSET'] = 'utf8mb4';

        putenv('DB_HOST=localhost');
        putenv('DB_NAME=redtutor_test');
        putenv('DB_USER=root');
        putenv('DB_PASS=');
        putenv('DB_CHARSET=utf8mb4');
    }
}
