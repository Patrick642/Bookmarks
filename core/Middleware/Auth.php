<?php
namespace core\Middleware;

class Auth
{
    public function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
    }
}