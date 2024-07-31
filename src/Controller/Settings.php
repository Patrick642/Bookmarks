<?php
namespace src\Controller;

use core\Controller;
use src\Model\EmailVerification\EmailVerificationModel;
use src\Model\User\UserModel;
use src\Utility\AuthKey;
use src\Utility\Emails;

final class Settings extends Controller
{
    private AuthKey $authKey;
    private Emails $emails;
    private EmailVerificationModel $emailVerificationModel;
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->authKey = new AuthKey();
        $this->emails = new Emails();
        $this->emailVerificationModel = new EmailVerificationModel();
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        $this->view->get('settings/index.phtml', [
            'pageTitle' => 'Settings - Bookmarks',
            'userEmail' => $this->userModel->getEmail($this->session->getUserId()),
            'minPasswordLength' => $this->userModel->validator::MIN_PASSWORD_LENGTH
        ]);
    }

    public function changeEmail(): void
    {
        if (!$this->requiredInputs('POST', ['email']))
            $this->jsonEncode(success: false, message: 'Enter a new email address.');

        $newEmail = $this->dataUtility->sanitizeInput($_POST['email']);

        // Send verification email
        $userId = $this->session->getUserId();
        $authKey = $this->authKey->generate();

        if ($this->emailVerificationModel->addChangeEmail($userId, $newEmail, $authKey)) {
            $emailVerificationLink = BASE_URL . '/change_email/verify?auth_key=' . $authKey;
            if ($this->emails->sendChangeEmailLink($newEmail, $emailVerificationLink, $this->emailVerificationModel::TIME_VALID)) {
                $this->session->setFlashMessage('Check new email inbox and click sent link to confirm email change.');
                $this->jsonEncode();
            }
        }

        $this->jsonEncode(success: false, message: $this->emailVerificationModel->validator->getError());
    }

    public function changeEmailVerify(): void
    {
        if (!$this->requiredInputs('GET', ['auth_key']))
            throw new \ErrorException('Not found', 404);

        $emailChanged = false;
        $authKey = $this->dataUtility->sanitizeInput($_GET['auth_key']);

        if ($this->emailVerificationModel->changeEmail($authKey)) {
            $this->session->setFlashMessage('Email address has been successfully changed!');
            $emailChanged = true;
        } else {
            $this->session->setFlashMessage($this->emailVerificationModel->validator->getError());
        }

        $this->view->get('settings/change_email_verify.phtml', [
            'pageTitle' => 'Change email - Bookmarks',
            'emailChanged' => $emailChanged
        ]);
    }

    public function changePassword(): void
    {
        if (!$this->requiredInputs('POST', ['pswd', 'pswd_repeat']))
            $this->jsonEncode(success: false, message: 'Not all required fields are filled.');

        $password = $_POST['pswd'];
        $passwordRepeat = $_POST['pswd_repeat'];

        if ($this->userModel->updatePassword($this->session->getUserId(), $password, $passwordRepeat))
            $this->jsonEncode();

        $this->jsonEncode(success: false, message: $this->userModel->validator->getError());
    }
}