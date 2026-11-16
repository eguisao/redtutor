<?php

use App\Config\Database;
use App\Models\UserModel;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    protected function setUp(): void
    {
        $this->prepareDatabase();
    }

    protected function tearDown(): void
    {
        $this->dropDatabase();
    }

    public function testSyncAuth0UserCreatesAndReturnsExistingUser(): void
    {
        $first = UserModel::syncAuth0User(['email' => 'ana@example.com']);

        $this->assertSame('ana@example.com', $first['correo']);
        $this->assertArrayHasKey('id_usuario', $first);
        $this->assertSame('ana@example.com', UserModel::findByEmail('ANA@EXAMPLE.com')['correo']);

        $second = UserModel::syncAuth0User(['email' => 'ana@example.com']);
        $this->assertSame($first['id_usuario'], $second['id_usuario']);
        $this->assertSame('ana@example.com', $second['correo']);
    }

    public function testSyncAuth0UserAddsMissingSchemaColumnsAndRejectsEmptyEmail(): void
    {
        $pdo = Database::getConnection();
        $columns = $pdo->query('SHOW COLUMNS FROM usuarios')->fetchAll();
        $columnNames = array_map(static function (array $column) {
            return $column['Field'];
        }, $columns);

        if (in_array('correo', $columnNames, true)) {
            $pdo->exec('ALTER TABLE usuarios DROP COLUMN correo');
        }

        if (in_array('fecha_creado', $columnNames, true)) {
            $pdo->exec('ALTER TABLE usuarios DROP COLUMN fecha_creado');
        }

        $pdo->exec('ALTER TABLE usuarios ADD COLUMN nombre_usuario VARCHAR(120) NULL');

        $persisted = UserModel::syncAuth0User(['email' => 'otro@example.com']);
        $this->assertSame('otro@example.com', $persisted['correo']);

        $columns = $pdo->query('SHOW COLUMNS FROM usuarios')->fetchAll();
        $columnNames = array_map(static function (array $column) {
            return $column['Field'];
        }, $columns);
        $this->assertContains('correo', $columnNames);
        $this->assertContains('fecha_creado', $columnNames);
        $this->assertNotContains('nombre_usuario', $columnNames);

        $this->expectException(\InvalidArgumentException::class);
        UserModel::syncAuth0User(['email' => '   ']);
    }

    private function prepareDatabase(): void
    {
        $this->setTestEnvironment();

        $conn = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
        $conn->exec('DROP DATABASE IF EXISTS redtutor_test');
        $conn->exec('CREATE DATABASE redtutor_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        $pdo = Database::getConnection();
        $pdo->exec('CREATE TABLE usuarios (
            id_usuario INT NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (id_usuario)
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
