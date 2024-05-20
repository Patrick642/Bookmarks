<?php
namespace src\Controller;

use core\Controller, src\Model\UserModel;

class SignUp extends Controller
{
    public function index(): void
    {
        $this->view->get('signup/index.phtml', [
            'page_title' => 'Sign up - Bookmarks'
        ]);
    }

    public function signUp(): void
    {
        if (!$this->formFields('POST', ['username', 'email', 'pswd', 'pswd_repeat'])) {
            $this->sessionMessage->set('Not all required fields are filled.');
            header('Location: /signup');
            exit;
        }

        $username = $this->sanitizeInput($_POST['username']);
        $email = $this->sanitizeInput($_POST['email']);
        $pswd = $this->sanitizeInput($_POST['pswd']);
        $pswd_repeat = $this->sanitizeInput($_POST['pswd_repeat']);

        $user_model = new UserModel();

        try {
            $user_model->add($username, $email, $pswd, $pswd_repeat);
            $user = $user_model->get($email, $pswd);
        } catch (\Exception $e) {
            $this->sessionMessage->set($e->getMessage());
            header('Location: /signup');
            exit;
        }

        session_regenerate_id(true);

        $this->session->setUserId($user['id']);
        $this->session->setUsername($user['username']);

        header('Location: /dashboard');
    }
}