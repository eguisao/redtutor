<?php

namespace App\Controllers;

class DashboardController
{
    public static function index()
    {
        require_auth();

        $user = $_SESSION['auth0_user'] ?? [];
        require __DIR__ . '/../views/dashboard.php';
    }
}
