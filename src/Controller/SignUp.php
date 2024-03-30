<?php
namespace src\Controller;

use core\Controller, core\Session, core\View, src\Model\User;

class SignUp extends Controller
{
    public function index(): void
    {
        (new View)->getView('signup', [
            'page_title' => 'Sign up - Bookmarks'
        ]);
    }

    public function signUp(): void
    {
        if (!$this->request->isXMLHttpRequest())
            exit;

        try {
            if ($this->isEmpty($_POST['username']) || $this->isEmpty($_POST['email']) || $this->isEmpty($_POST['pswd']) || $this->isEmpty($_POST['pswd_repeat']))
                throw new \Exception('Not all required fields are filled.');

            $username = $this->sanitizeInput($_POST['username']);
            $email = $this->sanitizeInput($_POST['email']);
            $pswd = $this->sanitizeInput($_POST['pswd']);
            $pswd_repeat = $this->sanitizeInput($_POST['pswd_repeat']);

            $user_model = new User();

            $user_model->add($username, $email, $pswd, $pswd_repeat);

            $user = $user_model->get($email, $pswd);

            (new Session)->createSession([
                'user_id' => $user['id'],
                'username' => $user['username']
            ]);

            echo json_encode([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);

            exit;
        }
    }
}