<?php
namespace src\Model\User;

use core\Model\Model;

final class UserModel extends Model
{
    public UserValidator $validator;

    public function __construct()
    {
        parent::__construct();
        $this->validator = new UserValidator();
    }

    public function add(string $username, string $email, string $password, string $passwordRepeat): bool
    {
        if (!$this->validator->validate(['username' => $username, 'email' => $email, 'passwords' => [$password, $passwordRepeat]]))
            return false;

        $query = 'INSERT INTO user (username, email, password_hash, is_public, date_joined, is_valid) VALUES (:username, :email, :password_hash, :is_public, :date_joined, :is_valid)';

        $stmt = $this->db()->prepare($query);

        $stmt->bindValue(':username', $username, \PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $this->hashPassword($password), \PDO::PARAM_STR);
        $stmt->bindValue(':is_public', false, \PDO::PARAM_BOOL);
        $stmt->bindValue(':date_joined', (new \DateTimeImmutable())->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
        $stmt->bindValue(':is_valid', false, \PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function getUsername(int $userId): ?string
    {
        $query = 'SELECT user.username FROM user WHERE user.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['username'] ?? null;

        return $fetch;
    }

    public function getEmail(int $userId): ?string
    {
        $query = 'SELECT user.email FROM user WHERE user.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['email'] ?? null;

        return $fetch;
    }

    public function getPasswordHash(int $userId): ?string
    {
        $query = 'SELECT user.password_hash FROM user WHERE user.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['password_hash'] ?? null;

        return $fetch;
    }

    public function getIsPublic(int $userId): ?bool
    {
        $query = 'SELECT user.is_public FROM user WHERE user.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['is_public'] ?? null;

        return $fetch;
    }

    public function getDateJoined(int $userId): ?string
    {
        $query = 'SELECT user.date_joined FROM user WHERE user.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['date_joined'] ?? null;

        return $fetch;
    }

    public function getIsValid(int $userId): ?bool
    {
        $query = 'SELECT user.is_valid FROM user WHERE user.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['is_valid'] ?? null;

        return $fetch;
    }

    public function updateEmail(int $userId, string $email): bool
    {
        if (!$this->validator->validate(['id' => $userId, 'email' => $email]))
            return false;

        $query = 'UPDATE user SET user.email = :email WHERE user.id = :id';

        $stmt = $this->db()->prepare($query);

        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function updatePassword(int $userId, string $password, string $passwordRepeat): bool
    {
        if (!$this->validator->validate(['id' => $userId, 'passwords' => [$password, $passwordRepeat]]))
            return false;

        $passwordHash = $this->hashPassword($password);

        $query = 'UPDATE user SET user.password_hash = :password_hash WHERE user.id = :id';

        $stmt = $this->db()->prepare($query);

        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':password_hash', $passwordHash, \PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function updateVisibility(int $userId, bool $bool): bool
    {
        $query = 'UPDATE user SET is_public = :is_public WHERE user.id = :id';

        $stmt = $this->db()->prepare($query);

        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':is_public', $bool, \PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function updateValidity(int $userId, bool $bool): bool
    {
        $query = 'UPDATE user SET is_valid = :is_valid WHERE user.id = :id';

        $stmt = $this->db()->prepare($query);

        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':is_valid', $bool, \PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function delete(int $userId, string $password): bool
    {
        if (!password_verify($password, $this->getPasswordHash($userId))) {
            $this->validator->setError('Wrong password.');
            return false;
        }

        $query = 'DELETE FROM user WHERE user.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getIdByUsername(string $username): ?int
    {
        $query = 'SELECT user.id FROM user WHERE user.username = :username';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':username', $username, \PDO::PARAM_STR);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['id'] ?? null;

        return $fetch;
    }

    public function getIdByEmail(string $email): ?int
    {
        $query = 'SELECT user.id FROM user WHERE user.email = :email';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['id'] ?? null;

        return $fetch;
    }

    /**
     * Check sign in credentials.
     *
     * @param  mixed $login Email/username
     * @param  mixed $password Text password
     * @return bool
     */
    public function checkCredentials(string $login, string $password): bool
    {
        // Get user ID by email.
        $userId = $this->getIdByEmail($login);

        // First, check if entered login match any email in the database.
        if ($userId === null) {

            // Then, get user ID by username.
            $userId = $this->getIdByUsername($login);

            // If entered login doesn't match username, return false, user with typed login does not exist.
            if ($userId === null) {
                $this->validator->setError('The login does not match any account.');
                return false;
            }
        }

        // Last, check if typed password is correct.
        if (!password_verify($password, $this->getPasswordHash($userId))) {
            $this->validator->setError('Wrong password.');
            return false;
        }

        return true;
    }

    /**
     * Hash the password with the unified hashing algorithm.
     *
     * @param  string $password Text password
     * @return string Password hash
     */
    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}