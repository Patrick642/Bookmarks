<?php
namespace src\Controller;

use core\Controller, src\Model\BookmarkModel, src\Model\UserModel;

class Board extends Controller
{
    public function index(): void
    {
        if (!$this->formFields('GET', ['u']))
            throw new \ErrorException('Not found', 404);

        $username = $this->sanitizeInput($_GET['u']);

        $user_model = new UserModel();

        $user_id = $user_model->getIdByUsername($username);

        if ($user_id === null)
            throw new \ErrorException('Not found', 404);

        $username = $user_model->getUsernameById($user_id);

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id)
            header('Location: /dashboard');

        $is_public = $user_model->isPublic($user_id);

        $this->view->get('board/index.phtml', [
            'page_title' => $username . ' - Bookmarks',
            'is_public' => $is_public,
            'username' => $username,
            'data' => ($is_public) ? (new BookmarkModel)->getAllByUserId($user_id) : []
        ]);
    }
}