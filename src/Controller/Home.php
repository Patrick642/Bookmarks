<?php
namespace src\Controller;

use core\Controller;

final class Home extends Controller
{
    public function index(): void
    {
        $this->view->get('home/index.phtml');
    }
}