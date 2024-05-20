<?php
namespace src\Controller;

use core\Controller, src\Model\UserModel;

class Settings extends Controller
{
    private UserModel $user_model;

    public function __construct()
    {
        parent::__construct();
        $this->user_model = new UserModel();
    }

    public function index(): void
    {
        $this->view->get('settings/index.phtml', [
            'page_title' => 'Settings - Bookmarks',
            'user_email' => $this->user_model->getEmail($_SESSION['user_id'])
        ]);
    }

    public function changeEmail(): void
    {
        if (!$this->request->isXMLHttpRequest())
            throw new \ErrorException('', 403);

        try {
            if (!$this->formFields('POST', ['email']))
                throw new \Exception('Enter a new email address.');

            $email = $this->sanitizeInput($_POST['email']);
            $this->user_model->changeEmail($_SESSION['user_id'], $email);

            echo json_encode([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function changePassword(): void
    {
        if (!$this->request->isXMLHttpRequest())
            throw new \ErrorException('', 403);

        try {
            if (!$this->formFields('POST', ['pswd', 'pswd_repeat']))
                throw new \Exception('Not all required fields are filled.');

            $this->user_model->changePassword($_SESSION['user_id'], $_POST['pswd'], $_POST['pswd_repeat']);

            echo json_encode([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteAccount(): void
    {
        if (!$this->request->isXMLHttpRequest())
            throw new \ErrorException('', 403);

        try {
            if (!$this->formFields('POST', ['pswd_confirmation']))
                throw new \Exception('Enter the password.');

            $pswd_confirmation = $this->sanitizeInput($_POST['pswd_confirmation']);

            $this->user_model->delete($_SESSION['user_id'], $pswd_confirmation);

            $this->session->deleteSession();

            echo json_encode([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}