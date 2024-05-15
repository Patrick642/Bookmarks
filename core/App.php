<?php
namespace core;

use core\Route\Router;

class App
{
    protected Router $router;
    protected Request $request;
    protected Error $error;

    public function __construct()
    {
        $this->router = new Router();
        $this->request = new Request();
        $this->error = new Error();
    }

    public function run()
    {
        set_error_handler([$this->error, 'errorHandler']);
        set_exception_handler([$this->error, 'exceptionHandler']);

        $this->request = new Request();
        $this->router = new Router();

        $this->router->route($this->request->getPath(), $this->request->getMethod());
    }
}