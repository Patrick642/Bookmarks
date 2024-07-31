<?php
namespace src\Model\EmailVerification;

use core\Model\Validator;

final class EmailVerificationValidator extends Validator
{
    public function authKey(string $authKey): bool
    {
        if (!$this->exists($authKey, 'email_verification', 'auth_key')) {
            $this->setError('The link is invalid.');
            return false;
        }

        return true;
    }

    public function expiresAt(string $expiresAt): bool
    {
        if ($expiresAt < (new \DateTimeImmutable())->format('Y-m-d H:i:s')) {
            $this->setError('The link has expired.');
            return false;
        }

        return true;
    }
}