<?php
namespace src\Utility;

use Core\DataUtility;
use core\Mailer;
use core\View;

class Emails
{
    private Mailer $mailer;
    private View $view;
    private DataUtility $dataUtility;

    public function __construct()
    {
        $this->mailer = new Mailer();
        $this->view = new View();
        $this->dataUtility = new DataUtility();
    }

    public function sendPasswordResetLink(string $email, string $link, int $timeValid): bool
    {
        return $this->mailer->send(
            $email,
            'Password reset',
            $this->view->getString('email/password_reset/html.phtml', [
                'baseUrl' => BASE_URL,
                'timeValid' => $this->dataUtility->convertSecondsToHuman($timeValid),
                'resetLink' => $link
            ]),
            $this->view->getString('email/password_reset/txt.phtml', [
                'timeValid' => $this->dataUtility->convertSecondsToHuman($timeValid),
                'resetLink' => $link
            ])
        );
    }

    public function sendCompleteRegistrationLink(string $email, string $link, int $timeValid)
    {
        return $this->mailer->send(
            $email,
            'Account verification',
            $this->view->getString('email/complete_registration/html.phtml', [
                'baseUrl' => BASE_URL,
                'timeValid' => $this->dataUtility->convertSecondsToHuman($timeValid),
                'verificationLink' => $link
            ]),
            $this->view->getString('email/complete_registration/txt.phtml', [
                'timeValid' => $this->dataUtility->convertSecondsToHuman($timeValid),
                'verificationLink' => $link
            ])
        );
    }

    public function sendChangeEmailLink(string $email, string $link, int $timeValid)
    {
        return $this->mailer->send(
            $email,
            'Change email',
            $this->view->getString('email/change_email/html.phtml', [
                'baseUrl' => BASE_URL,
                'newEmail' => $email,
                'timeValid' => $this->dataUtility->convertSecondsToHuman($timeValid),
                'verificationLink' => $link
            ]),
            $this->view->getString('email/change_email/txt.phtml', [
                'newEmail' => $email,
                'timeValid' => $this->dataUtility->convertSecondsToHuman($timeValid),
                'verificationLink' => $link
            ])
        );
    }

    public function sendDeleteAccountConfirmation($email)
    {
        return $this->mailer->send(
            $email,
            'Account deleted',
            $this->view->getString('email/account_deleted/html.phtml', [
                'baseUrl' => BASE_URL
            ]),
            $this->view->getString('email/account_deleted/txt.phtml')
        );
    }
}