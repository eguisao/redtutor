<?php

namespace App\Controllers;

class HomeController
{
    public static function index()
    {
        $user = $_SESSION['auth0_user'] ?? null;
        $userEmail = $_SESSION['auth0_user_email'] ?? null;

        if ($user || $userEmail) {
            header('Location: ' . base_url('/dashboard'));
            exit;
        }

        require __DIR__ . '/../views/public/home.php';
    }
}
