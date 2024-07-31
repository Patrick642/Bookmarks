<?php
namespace src\Model\User;

use core\Model\Validator;

final class UserValidator extends Validator
{
    public const MIN_USERNAME_LENGTH = 4;
    public const MAX_USERNAME_LENGTH = 32;
    public const MIN_PASSWORD_LENGTH = 6;

    public function __construct()
    {
        parent::__construct();
    }

    public function id(int $id): bool
    {
        if (empty($id)) {
            $this->setError('User ID is not set.');
            return false;
        }

        if (!$this->exists($id, 'user', 'id')) {
            $this->setError('User does not exist.');
            return false;
        }

        return true;
    }

    public function username(string $username): bool
    {
        if (empty($username)) {
            $this->setError('Username cannot be empty.');
            return false;
        }

        if (!preg_match('/^\w+$/', $username)) {
            $this->setError('The username is incorrect. It can contain only: lowercase letters, uppercase letters, numbers and underline sign(_).');
            return false;
        }

        if (strlen($username) < self::MIN_USERNAME_LENGTH) {
            $this->setError('Username should contain at least ' . self::MIN_USERNAME_LENGTH . ' characters.');
            return false;
        }

        if (strlen($username) > self::MAX_USERNAME_LENGTH) {
            $this->setError('Username can contain a maximum of ' . self::MAX_USERNAME_LENGTH . ' characters.');
            return false;
        }

        if (!$this->unique($username, 'user', 'username')) {
            $this->setError('The username is already taken. Try using a different one.');
            return false;
        }

        return true;
    }

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

        if (!$this->unique($email, 'user', 'email')) {
            $this->setError('Account associated with this email address already exist. Try using a different one.');
            return false;
        }

        return true;
    }

    public function passwords(string $password, string $passwordRepeat): bool
    {
        if (empty($password) || empty($passwordRepeat)) {
            $this->setError('Password fields cannot be empty.');
            return false;
        }

        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            $this->setError('Password should contain at least ' . self::MIN_PASSWORD_LENGTH . ' characters.');
            return false;
        }

        if ($password !== $passwordRepeat) {
            $this->setError('Passwords are not the same.');
            return false;
        }

        return true;
    }
}