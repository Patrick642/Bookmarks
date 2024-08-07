<?php
namespace src\Controller;

use core\Controller;
use src\Model\User\UserModel;
use src\Utility\AuthKey;

final class SignUp extends Controller
{
    private AuthKey $authKey;
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->authKey = new AuthKey();
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

            $this->redirect('/dashboard');
        }

        $this->session->setFlashMessage($this->userModel->validator->getError());
        $this->redirect('/signup');
    }
}