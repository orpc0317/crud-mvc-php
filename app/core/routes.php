
<?php

use App\Core\Router;
use App\Controllers\UserController;
use App\Controllers\ExportController;

$router = new Router();

// Ruta principal
$router->get('/', [UserController::class, 'handleRequest']);

// Usuarios
$router->get('/usuarios/ajax', [UserController::class, 'ajaxList']);
$router->post('/usuarios/crear', [UserController::class, 'create']);
$router->post('/usuarios/actualizar', [UserController::class, 'update']);
$router->get('/usuarios/eliminar/:id', [UserController::class, 'delete']);

// Exportación
$router->get('/exportar', [ExportController::class, 'exportCsv']);

return $router;