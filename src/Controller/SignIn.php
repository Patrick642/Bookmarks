<?php
namespace src\Controller;

use core\Controller, core\Session, core\View, src\Model\User;

class SignIn extends Controller
{
    public function index(): void
    {
        (new View)->getView('signin', [
            'page_title' => 'Sign in - Bookmarks'
        ]);
    }

    public function signIn(): void
    {
        if (!$this->request->isXMLHttpRequest())
            exit;

        try {
            if ($this->isEmpty($_POST['login']) || $this->isEmpty($_POST['pswd']))
                throw new \Exception('Not all required fields are filled.');

            $login = $this->sanitizeInput($_POST['login']);
            $pswd = $this->sanitizeInput($_POST['pswd']);

            $user = (new User)->get($login, $pswd);

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