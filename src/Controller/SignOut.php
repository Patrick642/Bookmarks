<?php
namespace src\Controller;

use core\Controller;

class SignOut extends Controller
{
    public function index(): void
    {
        $this->session->destroy();

        $this->redirect('/');
    }
}