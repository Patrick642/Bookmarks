<?php
namespace src\Controller;

use core\View;

class Home
{
    public function index(): void
    {
        (new View)->getView('home');
    }
}