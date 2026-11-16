<?php

if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenvFile = __DIR__ . '/../../.env';
if (file_exists($dotenvFile)) {
    $lines = file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || strpos($trimmed, '#') === 0) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
        $key = trim($key);
        $value = trim($value);

        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}

function env($key, $default = null)
{
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }

    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    return $default;
}

function base_url($path = '')
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = '/redtutor';

    $base = $scheme . '://' . $host . $basePath;
    $path = ltrim((string) $path, '/');

    if ($path === '') {
        return $base;
    }

    return $base . '/' . $path;
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function require_auth()
{
    if (empty($_SESSION['auth0_user'])) {
        header('Location: ' . base_url('/'));
        exit;
    }
}
