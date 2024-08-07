<?php
namespace src\Controller;

use core\Controller;
use src\Model\User\UserModel;
use src\Utility\AuthKey;

final class Settings extends Controller
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
        $this->view->get('settings/index.phtml', [
            'pageTitle' => 'Settings - Bookmarks',
            'userEmail' => $this->userModel->getEmail($this->session->getUserId()),
            'minPasswordLength' => $this->userModel->validator::MIN_PASSWORD_LENGTH
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