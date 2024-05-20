<?php
namespace src\Controller;

use core\Controller;

class Home extends Controller
{
    public function index(): void
    {
        $this->view->get('home/index.phtml');
    }
}