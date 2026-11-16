<?php

use App\Controllers\AuthController;
use PHPUnit\Framework\TestCase;

class AuthControllerTest extends TestCase
{
    public function testResetAuthSessionClearsAuthRelatedSessionData(): void
    {
        $_SESSION = [
            'auth0_user' => ['sub' => 'auth0|123'],
            'auth0_user_id' => 'auth0|123',
            'auth0_user_email' => 'ana@example.com',
            'auth0_user_role' => 'tutor',
            'other_flag' => true,
        ];

        AuthController::resetAuthSession();

        $this->assertArrayNotHasKey('auth0_user', $_SESSION);
        $this->assertArrayNotHasKey('auth0_user_id', $_SESSION);
        $this->assertArrayNotHasKey('auth0_user_email', $_SESSION);
        $this->assertArrayNotHasKey('auth0_user_role', $_SESSION);
        $this->assertArrayHasKey('other_flag', $_SESSION);
        $this->assertTrue($_SESSION['other_flag']);
    }
}
