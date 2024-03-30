<?php
namespace src\Controller;

use core\Controller, core\View, src\Model\Bookmark, src\Model\User;

class Board extends Controller
{
    public function index(): void
    {
        if ($this->isEmpty($_GET['u']))
            throw new \ErrorException('Not found', 404);

        $username = $this->sanitizeInput($_GET['u']);

        $user_id = (new User)->getIdByUsername($username);

        if ($user_id === NULL)
            throw new \ErrorException('Not found', 404);

        $username = (new User)->getUsernameById($user_id);

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id)
            header('Location: /dashboard');

        $is_public = (new User)->isPublic($user_id);

        (new View)->getView('board', [
            'is_public' => $is_public,
            'username' => $username,
            'data' => ($is_public) ? (new Bookmark)->getAllByUserId($user_id) : []
        ]);
    }
}