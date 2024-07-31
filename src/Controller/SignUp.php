<?php
namespace src\Controller;

use core\Controller;
use src\Model\EmailVerification\EmailVerificationModel;
use src\Model\User\UserModel;
use src\Utility\AuthKey;
use src\Utility\Emails;

final class SignUp extends Controller
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
        $this->view->get('signup/index.phtml', [
            'pageTitle' => 'Sign up - Bookmarks',
            'minUsernameLength' => $this->userModel->validator::MIN_USERNAME_LENGTH,
            'maxUsernameLength' => $this->userModel->validator::MAX_USERNAME_LENGTH,
            'minPasswordLength' => $this->userModel->validator::MIN_PASSWORD_LENGTH
        ]);
    }

    public function signUp(): void
    {
        if (!$this->requiredInputs('POST', ['username', 'email', 'pswd', 'pswd_repeat'])) {
            $this->session->setFlashMessage('Not all required fields are filled.');
            $this->redirect('/signup');
        }

        $username = $this->dataUtility->sanitizeInput($_POST['username']);
        $email = $this->dataUtility->sanitizeInput($_POST['email']);
        $pswd = $this->dataUtility->sanitizeInput($_POST['pswd']);
        $pswd_repeat = $this->dataUtility->sanitizeInput($_POST['pswd_repeat']);

        if ($this->userModel->add($username, $email, $pswd, $pswd_repeat)) {
            $userId = $this->userModel->getIdByEmail($email);

            $this->session->regenerateId();
            $this->session->setUserId($userId);
            $this->session->setUsername($this->userModel->getUsername($userId));

            // Redirect to verification email sending controller
            $this->redirect('/complete_registration/send');
        }

        $this->session->setFlashMessage($this->userModel->validator->getError());
        $this->redirect('/signup');
    }

    public function completeRegistration(): void
    {
        if ($this->userModel->getIsValid($this->session->getUserId()))
            $this->redirect('/dashboard');

        $this->view->get('signup/complete_registration.phtml', [
            'Verify your email' => 'Dashboard - Bookmarks',
            'email' => $this->userModel->getEmail($this->session->getUserId())
        ]);
    }

    public function completeRegistrationSendEmail(): void
    {
        // Send verification email
        $userId = $this->session->getUserId();
        $email = $this->userModel->getEmail($this->session->getUserId());
        $authKey = $this->authKey->generate();

        if ($this->emailVerificationModel->add($userId, $email, $authKey)) {
            $completeRegistrationLink = BASE_URL . '/complete_registration/verify?auth_key=' . $authKey;
            $this->emails->sendCompleteRegistrationLink($email, $completeRegistrationLink, $this->emailVerificationModel::TIME_VALID);
        }

        $this->redirect('/dashboard');
    }

    public function completeRegistrationVerify(): void
    {
        if (!$this->requiredInputs('GET', ['auth_key']))
            throw new \ErrorException('Not found', 404);

        $authKey = $this->dataUtility->sanitizeInput($_GET['auth_key']);

        if (!$this->emailVerificationModel->completeRegistration($authKey))
            $this->session->setFlashMessage($this->emailVerificationModel->validator->getError());

        $this->view->get('signup/complete_registration_verify.phtml', [
            'pageTitle' => 'Verify your email - Bookmarks'
        ]);
    }
}