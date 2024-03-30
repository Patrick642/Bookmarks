<?php
namespace src\Controller;

use core\Controller, core\Session, core\View, src\Model\User;

class Settings extends Controller
{
    public function index(): void
    {
        (new View)->getView('settings', [
            'page_title' => 'Settings - Bookmarks',
            'user_email' => (new User)->getEmail($_SESSION['user_id'])
        ]);
    }

    public function changeEmail(): void
    {
        if (!$this->request->isXMLHttpRequest())
            exit;

        try {
            if ($this->isEmpty($_POST['email']))
                throw new \Exception('Enter a new email address.');

            $email = $this->sanitizeInput($_POST['email']);

            (new User)->changeEmail($_SESSION['user_id'], $email);

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

    public function changePassword(): void
    {
        if (!$this->request->isXMLHttpRequest())
            exit;

        try {
            if ($this->isEmpty($_POST['pswd']) || $this->isEmpty($_POST['pswd_repeat']))
                throw new \Exception('Not all required fields are filled.');

            (new User)->changePassword($_SESSION['user_id'], $_POST['pswd'], $_POST['pswd_repeat']);

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

    public function deleteAccount(): void
    {
        if (!$this->request->isXMLHttpRequest())
            exit;

        try {
            if ($this->isEmpty($_POST['pswd_confirmation']))
                throw new \Exception('Enter the password.');

            $pswd_confirmation = $this->sanitizeInput($_POST['pswd_confirmation']);

            (new User)->delete($_SESSION['user_id'], $pswd_confirmation);

            (new Session())->deleteSession();

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