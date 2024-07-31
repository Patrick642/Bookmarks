<?php
namespace core\Middleware;

use core\Session;

class Guest
{
    public function handle()
    {
        $session = new Session();

        if ($session->getUserId() !== null) {
            header('Location: /');
            exit;
        }
    }
}