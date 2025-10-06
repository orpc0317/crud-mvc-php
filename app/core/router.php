<?php

namespace App\Core;

class Router
{
  private $routes = [];

  public function get($path, $callback)
  {
    $this->routes['GET'][$path] = $callback;
  }

  public function post($path, $callback)
  {
    $this->routes['POST'][$path] = $callback;
  }

  public function dispatch($method, $uri)
  {
    // Normalizar URI
    $uri = parse_url($uri, PHP_URL_PATH);

    // Eliminar el prefijo del proyecto y "index.php"
    $base = '/crud-mvc-php/public/index.php';
    if (strpos($uri, $base) === 0) {
      $uri = substr($uri, strlen($base));
    }

    // Asegurar que la URI comience con "/"
    $uri = '/' . ltrim($uri, '/');

    $callback = $this->routes[$method][$uri] ?? null;

    if (!$callback) {
      http_response_code(404);
      echo "Ruta no encontrada: $uri";
      return;
    }

    if (is_callable($callback)) {
      call_user_func($callback);
    } elseif (is_array($callback)) {
      [$controller, $method] = $callback;
      if (class_exists($controller)) {
        $instance = new $controller();
        call_user_func([$instance, $method]);
      } else {
        echo "Controlador no encontrado: $controller";
      }
    }
  }
}
