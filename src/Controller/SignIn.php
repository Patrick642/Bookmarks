<?php
namespace src\Controller;

use core\Controller, src\Model\UserModel;

class SignIn extends Controller
{
    public function index(): void
    {
        $this->view->get('signin/index.phtml', [
            'page_title' => 'Sign in - Bookmarks'
        ]);
    }

    public function signIn(): void
    {
        if (!$this->formFields('POST', ['login', 'pswd'])) {
            $this->sessionMessage->set('Not all required fields are filled.');
            header('Location: /signin');
            exit;
        }

        $login = $this->sanitizeInput($_POST['login']);
        $pswd = $this->sanitizeInput($_POST['pswd']);

        $user_model = new UserModel();

        try {
            $user = $user_model->get($login, $pswd);
        } catch (\Exception $e) {
            $this->sessionMessage->set($e->getMessage());
            header('Location: /signin');
            exit;
        }

        session_regenerate_id(true);

        $this->session->setUserId($user['id']);
        $this->session->setUsername($user['username']);

        header('Location: /dashboard');
    }
}