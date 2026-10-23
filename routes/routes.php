<?php

require_once __DIR__ . '/../app/config/bootstrap.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/redtutor';

if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

if ($uri === '') {
    $uri = '/';
}

switch ($uri) {
    case '/':
        App\Controllers\HomeController::index();
        break;
    case '/login':
        App\Controllers\AuthController::login();
        break;
    case '/callback':
        App\Controllers\AuthController::callback();
        break;
    case '/logout':
        App\Controllers\AuthController::logout();
        break;
    case '/dashboard':
        App\Controllers\DashboardController::index();
        break;
    case '/materias':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            App\Controllers\MateriaController::handleAction();
        } else {
            App\Controllers\MateriaController::index();
        }
        break;
    case '/usuarios':
        App\Controllers\UsuarioController::index();
        break;
    default:
        http_response_code(404);
        echo '404 - Página no encontrada';
        break;
}
