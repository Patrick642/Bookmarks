<?php
namespace src\Model\PasswordReset;

use core\Model\Validator;

final class PasswordResetValidator extends Validator
{
    public function email(string $email): bool
    {
        if (empty($email)) {
            $this->setError('Email cannot be empty.');
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setError('Email is incorrect.');
            return false;
        }

        if (!$this->exists($email, 'user', 'email')) {
            $this->setError('Email "' . $email . '" is not associated with any account.');
            return false;
        }

        return true;
    }

    public function authKey(string $authKey): bool
    {
        if (!$this->exists($authKey, 'password_reset', 'auth_key')) {
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