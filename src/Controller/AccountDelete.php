<?php
namespace src\Controller;

use core\Controller;
use src\Model\User\UserModel;
use src\Utility\Emails;

class AccountDelete extends Controller
{
    private Emails $emails;
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->emails = new Emails();
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
        $email = $this->userModel->getEmail($this->session->getUserId());

        if ($this->userModel->delete($this->session->getUserId(), $password)) {
            $this->emails->sendDeleteAccountConfirmation($email);
            $this->session->destroy();

            $this->redirect('/');
        }

        $this->session->setFlashMessage($this->userModel->validator->getError());
        $this->redirect('/account_delete');
    }
}