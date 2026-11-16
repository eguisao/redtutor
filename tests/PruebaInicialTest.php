
<?php

use PHPUnit\Framework\TestCase;

class PruebaInicialTest extends TestCase
{
    public function testOperacionBasica(): void
    {
        $resultado = 2 + 2;

        $this->assertEquals(4, $resultado);
    }
}
