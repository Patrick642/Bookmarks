<?php
namespace core\Middleware;

use core\Session;

class Auth
{
    public function handle()
    {
        $session = new Session();

        if ($session->getUserId() === null) {
            header('Location: /signin');
            exit;
        }
    }
}