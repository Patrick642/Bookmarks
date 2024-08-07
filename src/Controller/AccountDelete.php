<?php
namespace src\Controller;

use core\Controller;
use src\Model\User\UserModel;

final class AccountDelete extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        $this->view->get('account_delete/index.phtml', [
            'pageTitle' => 'Delete your account - Bookmarks'
        ]);
    }

    public function delete(): void
    {
        if (!$this->requiredInputs('POST', ['pswd_confirmation'])) {
            $this->session->setFlashMessage('Enter the password.');
            $this->redirect('/account_delete');
        }

        $password = $_POST['pswd_confirmation'];

        if ($this->userModel->delete($this->session->getUserId(), $password)) {
            $this->redirect('/');
        }

        $this->session->setFlashMessage($this->userModel->validator->getError());
        $this->redirect('/account_delete');
    }
}