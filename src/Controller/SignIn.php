<?php
namespace src\Controller;

use core\Controller;
use src\Model\User\UserModel;

final class SignIn extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        $this->view->get('signin/index.phtml', [
            'pageTitle' => 'Sign in - Bookmarks'
        ]);
    }

    public function signIn(): void
    {
        if (!$this->requiredInputs('POST', ['login', 'pswd'])) {
            $this->session->setFlashMessage('Not all required fields are filled.');
            $this->redirect('/signin');
        }

        $login = $this->dataUtility->sanitizeInput($_POST['login']);
        $pswd = $this->dataUtility->sanitizeInput($_POST['pswd']);

        if ($this->userModel->checkCredentials($login, $pswd)) {
            $userId = $this->userModel->getIdByEmail($login) ?? $this->userModel->getIdByUsername($login);

            $this->session->regenerateId();
            $this->session->setUserId($userId);
            $this->session->setUsername($this->userModel->getUsername($userId));

            $this->redirect('/dashboard');
        }

        $this->session->setFlashMessage($this->userModel->validator->getError());
        $this->redirect('/signin');
    }
}