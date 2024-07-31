<?php
namespace Src\Controller;

use core\Controller;
use src\Model\PasswordReset\PasswordResetModel;
use src\Model\User\UserModel;
use src\Utility\AuthKey;
use src\Utility\Emails;

final class PasswordReset extends Controller
{
    private AuthKey $authKey;
    private Emails $emails;
    private PasswordResetModel $passwordResetModel;
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->authKey = new AuthKey();
        $this->emails = new Emails();
        $this->passwordResetModel = new PasswordResetModel();
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        $this->view->get('password_reset/index.phtml', [
            'pageTitle' => 'Password reset - Bookmarks',
        ]);
    }

    public function sendEmail(): void
    {
        if (!$this->requiredInputs('POST', ['email'])) {
            $this->session->setFlashMessage('Enter an email address.');
            $this->redirect('/password_reset');
        }

        $enteredEmail = $this->dataUtility->sanitizeInput($_POST['email']);
        $authKey = $this->authKey->generate();

        if ($this->passwordResetModel->add($enteredEmail, $authKey)) {
            $resetPasswordLink = BASE_URL . '/password_reset/reset?auth_key=' . $authKey;

            if ($this->emails->sendPasswordResetLink($enteredEmail, $resetPasswordLink, $this->passwordResetModel::TIME_VALID))
                $this->redirect('/password_reset/email_sent');

            $this->session->setFlashMessage('An error occurred while sending the email. Please try again later.');
            $this->redirect('/password_reset');
        }

        $this->session->setFlashMessage($this->passwordResetModel->validator->getError());
        $this->redirect('/password_reset');
    }

    public function emailSent(): void
    {
        $this->view->get('password_reset/email_sent.phtml', [
            'pageTitle' => 'Password reset - Bookmarks'
        ]);
    }

    public function resetIndex(): void
    {
        if (!$this->requiredInputs('GET', ['auth_key']))
            throw new \ErrorException('Not found', 404);

        $email = null;
        $authKey = $this->dataUtility->sanitizeInput($_GET['auth_key']);

        if (!$this->passwordResetModel->verify($authKey)) {
            $this->session->setFlashMessage($this->passwordResetModel->validator->getError());
        } else {
            $email = $this->userModel->getEmail($this->passwordResetModel->getUserId($authKey));
        }

        $this->view->get('password_reset/reset.phtml', [
            'pageTitle' => 'Password reset - Bookmarks',
            'email' => $email,
            'minPasswordLength' => $this->userModel->validator::MIN_PASSWORD_LENGTH
        ]);
    }

    public function reset(): void
    {
        if (!$this->requiredInputs('POST', ['pswd', 'pswd_repeat', 'auth_key'])) {
            $this->session->setFlashMessage('Not all required fields are filled.');
            $this->redirect('/password_reset/reset?auth_key=' . $_POST['auth_key'] ?? null);
        }

        $authKey = $this->dataUtility->sanitizeInput($_POST['auth_key']);
        $password = $this->dataUtility->sanitizeInput($_POST['pswd']);
        $passwordRepeat = $this->dataUtility->sanitizeInput($_POST['pswd_repeat']);

        if (!$this->passwordResetModel->changePassword($authKey, $password, $passwordRepeat)) {
            $this->session->setFlashMessage($this->passwordResetModel->validator->getError());
            $this->redirect('/password_reset/reset?auth_key=' . $authKey);
        }

        $this->redirect('/password_reset/success');
    }

    public function success(): void
    {
        $this->view->get('password_reset/success.phtml', [
            'pageTitle' => 'Password changed - Bookmarks'
        ]);
    }
}