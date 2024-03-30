<?php
namespace src\Controller;

use core\Session;

class SignOut
{
    public function index(): void
    {
        $session_controller = new Session;

        $session_controller->deleteSession();
        
        header('Location: /');
    }
}