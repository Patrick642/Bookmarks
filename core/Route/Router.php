<?php
namespace core\Route;

use core\Middleware\Middleware;

class Router
{
    private array $routes = [];

    private function registerRoutes()
    {
        $routes_list = include_once ROOT_DIR . '/core/Route/routes.php' ?? [];

        foreach ($routes_list as $r) {
            array_push($this->routes, [
                'method' => strtoupper($r[0]),
                'path' => strtolower(rtrim($r[1], '/')),
                'controller' => $r[2],
                'action' => str_replace('()', '', $r[3]),
                'middleware' => $r[4] ?? NULL
            ]);
        }
    }

    public function route(string $path, string $method)
    {
        $this->registerRoutes();

        foreach ($this->routes as $route) {
            if ($route['path'] === $path && $route['method'] === $method) {
                $middleware = Middleware::MAP[$route['middleware']] ?? NULL;

                if ($middleware !== NULL)
                    (new $middleware)->handle();

                $class_name = '\src\Controller\\' . $route['controller'];

                if (!class_exists($class_name))
                    throw new \ErrorException('Controller ' . $route['controller'] . ' does not exist.');

                if (!method_exists($class_name, $route['action']))
                    throw new \ErrorException('Method  ' . $route['action'] . ' of the class ' . $route['controller'] . ' does not exist.');

                $instance = new $class_name;
                $instance->{$route['action']}();

                exit;
            }
        }

        throw new \ErrorException('Not found.', 404);
    }
}