<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

// Cargar rutas
$router = require __DIR__ . '/../app/core/routes.php';

// Normalizar URI eliminando el prefijo dinámico del proyecto
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$requestUri = str_replace('\\', '/', $_SERVER['REQUEST_URI']);

$basePath = rtrim(dirname($scriptName), '/');
$path = parse_url($requestUri, PHP_URL_PATH);

$normalizedUri = $path;

if ($basePath !== '/' && strpos($path, $basePath) === 0) {
    $normalizedUri = substr($path, strlen($basePath));
}

$normalizedUri = '/' . trim($normalizedUri, '/');

// Ejecutar el router con la URI normalizada
$router->dispatch($_SERVER['REQUEST_METHOD'], $normalizedUri);
