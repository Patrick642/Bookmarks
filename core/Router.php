<?php
namespace core;

use core\Middleware\Middleware;

class Router
{
    private Request $request;
    private array $routes = [];

    public function __construct()
    {
        $this->request = new Request();
    }

    private function registerRoutes()
    {
        $routeList = include_once ROOT_DIR . '/src/routes.php' ?? [];

        foreach ($routeList as $r) {
            array_push($this->routes, [
                'method' => strtoupper($r[0]),
                'path' => strtolower(rtrim($r[1], '/')),
                'controller' => $r[2],
                'action' => str_replace('()', '', $r[3]),
                'middleware' => $r[4] ?? null,
                'XMLHttpRequest' => $r[5] ?? false
            ]);
        }
    }

    public function route(string $path, string $method)
    {
        $this->registerRoutes();

        foreach ($this->routes as $route) {
            if ($route['path'] === $path && $route['method'] === $method) {
                $middleware = Middleware::MAP[$route['middleware']] ?? null;

                if ($middleware !== null)
                    (new $middleware)->handle();

                $className = '\src\Controller\\' . $route['controller'];

                if (!class_exists($className))
                    throw new \ErrorException('Controller ' . $route['controller'] . ' does not exist.');

                if (!method_exists($className, $route['action']))
                    throw new \ErrorException('Method "' . $route['action'] . '" of the class "' . $route['controller'] . '" does not exist.');

                if ($this->request->isXMLHttpRequest() !== $route['XMLHttpRequest'])
                    throw new \ErrorException('', 403);

                $instance = new $className;
                $instance->{$route['action']}();

                exit;
            }
        }

        throw new \ErrorException('Not found.', 404);
    }
}