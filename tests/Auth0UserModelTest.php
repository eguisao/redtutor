<?php

use App\Models\Auth0UserModel;
use PHPUnit\Framework\TestCase;

class Auth0UserModelTest extends TestCase
{
    public function testFindMetadataValueReadsNestedMetadataWithDifferentAliases(): void
    {
        $source = [
            'user_metadata' => [
                'numero_documento' => 'ABC123',
                'fecha_nacimiento' => '1998-05-12',
            ],
            'app_metadata' => [
                'perfil' => 'Tutor',
            ],
            'metadata' => [
                'role' => 'Estudiante',
            ],
        ];

        $this->assertSame('ABC123', Auth0UserModel::findMetadataValue($source, ['numero_documento', 'numeroDocumento'], ''));
        $this->assertSame('1998-05-12', Auth0UserModel::findMetadataValue($source, ['fecha_nacimiento', 'fechaNacimiento'], ''));
        $this->assertSame('Tutor', Auth0UserModel::findMetadataValue($source, ['tipo_usuario', 'rol', 'perfil'], ''));
        $this->assertSame('Estudiante', Auth0UserModel::findMetadataValue($source, ['role', 'rol'], ''));
    }

    public function testNormalizeUserAddsDefaultsAndMergesMetadata(): void
    {
        $input = [
            'nickname' => 'anita',
            'blocked' => true,
            'user_metadata' => ['numero_documento' => 'XYZ987'],
            'app_metadata' => ['perfil' => 'Docente'],
        ];

        $normalized = Auth0UserModel::normalizeUser($input);

        $this->assertSame('anita', $normalized['name']);
        $this->assertNull($normalized['created_at']);
        $this->assertTrue($normalized['blocked']);
        $this->assertSame(['numero_documento' => 'XYZ987'], $normalized['user_metadata']);
        $this->assertSame(['perfil' => 'Docente'], $normalized['app_metadata']);
        $this->assertSame(['numero_documento' => 'XYZ987', 'perfil' => 'Docente'], $normalized['metadata']);
    }

    public function testGetMetadataValueReadsTopLevelAndNestedValues(): void
    {
        $source = [
            'user_metadata' => ['numero_documento' => 'CC123'],
            'app_metadata' => ['tipo_usuario' => 'Estudiante'],
            'role' => 'Tutor',
        ];

        $this->assertSame('CC123', Auth0UserModel::getMetadataValue($source, 'numero_documento', ''));
        $this->assertSame('Estudiante', Auth0UserModel::getMetadataValue($source, 'tipo_usuario', ''));
        $this->assertSame('Tutor', Auth0UserModel::getMetadataValue($source, 'role', ''));
        $this->assertSame('valor-por-defecto', Auth0UserModel::getMetadataValue($source, 'campo_inexistente', 'valor-por-defecto'));
    }

    public function testPrettyMetadataReturnsJsonForNonEmptyMetadataAndMessageForEmpty(): void
    {
        $empty = Auth0UserModel::prettyMetadata([]);
        $formatted = Auth0UserModel::prettyMetadata([
            'numero_documento' => 'ABC123',
            'perfil' => 'Tutor',
        ]);

        $this->assertSame('Sin metadata', $empty);
        $this->assertStringContainsString('numero_documento', $formatted);
        $this->assertStringContainsString('ABC123', $formatted);
        $this->assertStringContainsString('perfil', $formatted);
    }

    public function testParseJsonMetadataParsesValidJsonAndRejectsInvalidJson(): void
    {
        $parsed = Auth0UserModel::parseJsonMetadata('{"numero_documento":"ABC123","perfil":"Tutor"}');

        $this->assertSame('ABC123', $parsed['numero_documento']);
        $this->assertSame('Tutor', $parsed['perfil']);

        $this->expectException(\InvalidArgumentException::class);
        Auth0UserModel::parseJsonMetadata('not-json');
    }
}
