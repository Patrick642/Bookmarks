<?php
namespace Src\Controller;

use core\Controller;
use core\Email;
use src\Model\PasswordResetModel;
use src\Model\UserModel;

class PasswordReset extends Controller
{
    private PasswordResetModel $password_reset_model;
    private UserModel $user_model;

    public function __construct()
    {
        parent::__construct();
        $this->password_reset_model = new PasswordResetModel();
        $this->user_model = new UserModel();
    }

    public function index(): void
    {
        $this->view->get('password_reset/index.phtml', [
            'page_title' => 'Password reset - Bookmarks',
        ]);
    }

    public function sendEmail(): void
    {
        if (!$this->formFields('POST', ['email'])) {
            $this->sessionMessage->set('Enter an email address.');
            header('Location: /password_reset');
            exit;
        }

        $entered_email = $this->sanitizeInput($_POST['email']);

        $reset_key = $this->password_reset_model->generateKey();
        $reset_link = BASE_URL . '/password_reset/reset?key=' . $reset_key;

        try {
            if ($this->user_model->getIdByEmail($entered_email) === null)
                throw new \Exception($entered_email . ' is not associated with any account.');

            $this->password_reset_model->add($entered_email, $reset_key);

        } catch (\Exception $e) {
            $this->sessionMessage->set($e->getMessage());
            header('Location: /password_reset');
            exit;
        }

        $email = new Email();
        $email->send(
            $entered_email,
            'Password reset',
            $email->render('password_reset.html', [
                '__baseUrl__' => BASE_URL,
                '__timeValid__' => $this->password_reset_model::TIME_VALID,
                '__resetLink__' => $reset_link
            ]),
            $email->render('password_reset.txt', [
                '__timeValid__' => $this->password_reset_model::TIME_VALID,
                '__resetLink__' => $reset_link
            ])
        );

        header('Location: /password_reset/email_sent');
    }

    public function emailSent(): void
    {
        $this->view->get('password_reset/email_sent.phtml', [
            'page_title' => 'Password reset - Bookmarks'
        ]);
    }

    public function resetIndex(): void
    {
        if (!$this->formFields('GET', ['key']))
            throw new \ErrorException('', 404);

        $key = $this->sanitizeInput($_GET['key']);

        try {
            $email = $this->password_reset_model->getEmailByKey($key);

        } catch (\Exception $e) {
            $this->sessionMessage->set($e->getMessage());
        }

        $this->view->get('password_reset/reset.phtml', [
            'page_title' => 'Password reset - Bookmarks',
            'email' => $email ?? null
        ]);
    }

    public function reset(): void
    {
        if (!$this->formFields('POST', ['pswd', 'pswd_repeat']))
            throw new \Exception('Not all required fields are filled.');

        try {
            $email = $this->password_reset_model->getEmailByKey($_POST['reset_key']);
            $user_id = $this->user_model->getIdByEmail($email);
            $this->user_model->changePassword($user_id, $_POST['pswd'], $_POST['pswd_repeat']);
            $this->password_reset_model->delete($_POST['reset_key']);

        } catch (\Exception $e) {
            $this->sessionMessage->set($e->getMessage());
            header('Location: /password_reset/reset?key=' . $_POST['reset_key']);
            exit;
        }

        header('Location: /password_reset/success');
    }

    public function success(): void
    {
        $this->view->get('password_reset/success.phtml', [
            'page_title' => 'Password has been reset - Bookmarks'
        ]);
    }
}