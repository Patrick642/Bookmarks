<?php
namespace core;

use core\Route\Router;

class App
{
    protected Router $router;
    protected Request $request;
    protected Error $error;

    public function run()
    {
        $this->error = new Error();
        set_error_handler([$this->error, 'errorHandler']);
        set_exception_handler([$this->error, 'exceptionHandler']);

        $this->request = new Request();
        $this->router = new Router();

        $this->router->route($this->request->getPath(), $this->request->getMethod());
    }
}