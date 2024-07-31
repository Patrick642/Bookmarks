<?php
namespace src\Controller;

use core\Controller;

final class SignOut extends Controller
{
    public function index(): void
    {
        $this->session->destroy();

        $this->redirect('/');
    }
}