<?php
// Carga automática de clases con Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Importación de clases con namespace
use App\Core\Router;
use App\Controllers\UserController;
use App\Controllers\ExportController;

$router = new Router();

// Rutas GET
$router->get('/usuarios/ajax', [UserController::class, 'ajaxList']);
$router->get('/exportar', [ExportController::class, 'exportCsv']);

// Rutas POST (ejemplo)
$router->post('/usuarios/crear', [UserController::class, 'create']);

// Ejecutar el router
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

// Instanciación y ejecución
$controller = new UserController();
$controller->ajaxList(); // o el método que quieras probar