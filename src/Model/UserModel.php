<?php
namespace src\Model;

use core\Model, PDO, Exception;

class UserModel extends Model
{
    private int $min_username_length = 4;
    private int $min_password_length = 4;

    /**
     * Add new user
     *
     * @param  string $username
     * @param  string $email
     * @param  string $password
     * @param  string $password_repeat
     * @return bool
     */
    public function add(string $username, string $email, string $password, string $password_repeat): bool
    {
        if (!preg_match('/^\w+$/', $username))
            throw new Exception('The username is incorrect. It can contain only: lowercase letters, uppercase letters, numbers and underline sign(_).');

        if (strlen($username) < $this->min_username_length)
            throw new Exception('Username should contain at least ' . $this->min_username_length . ' characters.');

        if ($this->isUsernameTaken($username) === true)
            throw new Exception('The username is already taken. Try using a different one.');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new Exception('Email is incorrect.');

        if ($this->isEmailTaken($email) === true)
            throw new Exception('Account associated with this email address already exist.');

        if (strlen($password) < $this->min_password_length)
            throw new Exception('Password should contain at least ' . $this->min_password_length . ' characters.');

        if ($password !== $password_repeat)
            throw new Exception('Passwords are not the same.');

        $password_hash = $this->hashPassword($password);

        $query = $this->db()->prepare('INSERT INTO user(username, email, password_hash, is_public) VALUES(:username, :email, :password_hash, 0)');

        return $query->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $password_hash
        ]);
    }

    /**
     * Check if username is taken
     *
     * @param  string $username
     * @return bool
     */
    public function isUsernameTaken(string $username): bool
    {
        $query = $this->db()->prepare('SELECT user.id FROM user WHERE user.username = :username');

        $query->execute([
            ':username' => $username
        ]);

        if ($query->rowCount() !== 0)
            return true;

        return false;
    }

    /**
     * Check if email is taken
     *
     * @param  string $email
     * @return bool
     */
    public function isEmailTaken(string $email): bool
    {
        $query = $this->db()->prepare('SELECT user.id FROM user WHERE user.email = :email');

        $query->execute([
            ':email' => $email
        ]);

        return ($query->rowCount() !== 0);
    }

    /**
     * Change user email
     *
     * @param  string $user_id
     * @param  string $email
     * @return bool
     */
    public function changeEmail(string $user_id, string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new Exception('Email is incorrect.');

        if ($this->getEmail($user_id) === $email)
            throw new Exception('This email address is already assigned to your account.');

        if ($this->isEmailTaken($email) === true)
            throw new Exception('This email address is already taken by another user, please try another one.');

        $query = $this->db()->prepare('UPDATE user SET user.email = :email WHERE user.id = :user_id');

        return $query->execute([
            ':user_id' => $user_id,
            ':email' => $email
        ]);
    }

    /**
     * Change user password
     *
     * @param  string $user_id
     * @param  string $password
     * @param  string $password_repeat
     * @return bool
     */
    public function changePassword(string $user_id, string $password, string $password_repeat): bool
    {
        if (strlen($password) < $this->min_password_length)
            throw new Exception('Password should contain at least ' . $this->min_password_length . ' characters.');

        if ($password !== $password_repeat)
            throw new Exception('Passwords are not the same.');

        $password_hash = $this->hashPassword($password);

        $query = $this->db()->prepare('UPDATE user SET user.password_hash = :password_hash WHERE user.id = :user_id');

        return $query->execute([
            ':user_id' => $user_id,
            ':password_hash' => $password_hash
        ]);
    }

    /**
     * Get user ID and username with login and password
     *
     * @param  string $login
     * @param  string $password
     * @return array
     */
    public function get(string $login, string $password): array
    {
        $query = $this->db()->prepare('SELECT user.id, user.username, user.password_hash FROM user WHERE user.username = :login OR user.email = :login');

        $query->execute([
            ':login' => $login
        ]);

        $data_fetch = $query->fetch(PDO::FETCH_ASSOC);

        if ($query->rowCount() === 0)
            throw new Exception('The login does not match any account.');

        if (!password_verify($password, $data_fetch['password_hash']))
            throw new Exception('Wrong password.');

        return [
            'id' => $data_fetch['id'],
            'username' => $data_fetch['username']
        ];
    }

    /**
     * Delete user
     *
     * @param  string $user_id
     * @param  string $password
     * @return bool
     */
    public function delete(string $user_id, string $password): bool
    {
        $query = $this->db()->prepare('SELECT user.id, user.password_hash FROM user WHERE user.id = :user_id');

        $query->execute([
            ':user_id' => $user_id
        ]);

        if ($query->rowCount() === 0)
            throw new Exception('User does not exist.');

        $data_fetch = $query->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($password, $data_fetch['password_hash']))
            throw new Exception('Wrong password.');

        $del_bookmarks = $this->db()->prepare('DELETE FROM bookmark WHERE bookmark.user_id = :user_id');

        if (!$del_bookmarks->execute([':user_id' => $user_id])) {
            throw new Exception('Something went wrong. Try again later.');
        }

        $del_user = $this->db()->prepare('DELETE FROM user WHERE user.id = :user_id');

        return $del_user->execute([
            ':user_id' => $user_id
        ]);
    }

    /**
     * Get user email
     *
     * @param  string $user_id
     * @return void
     */
    public function getEmail(string $user_id)
    {
        $query = $this->db()->prepare('SELECT user.email FROM user WHERE user.id = :user_id');

        $query->execute([
            ':user_id' => $user_id
        ]);

        $data_fetch = $query->fetch(PDO::FETCH_ASSOC);

        return $data_fetch['email'];
    }

    /**
     * Set user bookmarks visibility (public/private)
     *
     * @param  string $user_id
     * @param  bool $bool
     * @return bool
     */
    public function setVisibility(string $user_id, string $bool): bool
    {
        $bool = filter_var($bool, FILTER_VALIDATE_BOOLEAN);

        $query = $this->db()->prepare('UPDATE user SET is_public = :bool WHERE user.id = :user_id');

        return $query->execute([
            ':bool' => $bool,
            ':user_id' => $user_id
        ]);
    }

    /**
     * Check if user bookmarks are public
     *
     * @param  string $user_id
     * @return bool
     */
    public function isPublic(string $user_id): bool
    {
        $query = $this->db()->prepare('SELECT user.is_public FROM user WHERE user.id = :user_id');

        $query->execute([
            ':user_id' => $user_id
        ]);

        $data_fetch = $query->fetch(PDO::FETCH_ASSOC);

        return $data_fetch['is_public'];
    }

    /**
     * Get user ID with username
     *
     * @param  string $username
     * @return mixed
     */
    public function getIdByUsername(string $username): mixed
    {
        $query = $this->db()->prepare('SELECT user.id FROM user WHERE user.username = :username');

        $query->execute([
            ':username' => $username
        ]);

        $data_fetch = $query->fetch(PDO::FETCH_ASSOC);

        return $data_fetch['id'] ?? null;
    }
    /**
     * Get username with user ID
     *
     * @param  string $user_id
     * @return mixed
     */
    public function getUsernameById(string $user_id): mixed
    {
        $query = $this->db()->prepare('SELECT user.username FROM user WHERE user.id = :user_id');

        $query->execute([
            ':user_id' => $user_id
        ]);

        $data_fetch = $query->fetch(PDO::FETCH_ASSOC);

        return $data_fetch['username'] ?? null;
    }

    /**
     * Get user id by email.
     *
     * @param  mixed $email
     * @return mixed
     */
    public function getIdByEmail(string $email): mixed
    {
        $query = $this->db()->prepare('SELECT user.id FROM user WHERE user.email = :email');

        $query->execute([
            ':email' => $email
        ]);

        $data_fetch = $query->fetch(PDO::FETCH_ASSOC);

        return $data_fetch['id'] ?? null;
    }

    /**
     * Hash password with the same password hashing algorithm 
     *
     * @param  string $password
     * @return string
     */
    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}