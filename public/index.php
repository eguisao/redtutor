<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/redtutor';

if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

if ($uri === '') {
    $uri = '/';
}

$assetPath = __DIR__ . $uri;
if (is_file($assetPath)) {
    return false;
}

require_once __DIR__ . '/../routes/routes.php';
