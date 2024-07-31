<?php
namespace src\Model\PasswordReset;

use core\Model\Model;
use src\Model\User\UserModel;

final class PasswordResetModel extends Model
{
    // The number of seconds that the password reset auth key is valid.
    public const TIME_VALID = 15 * 60;

    public PasswordResetValidator $validator;
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->validator = new PasswordResetValidator();
        $this->userModel = new UserModel();
    }

    /**
     * Add password reset auth key to the database.
     *
     * @param  string $email
     * @param  string $authKey
     * @return bool
     */
    public function add(string $email, string $authKey): bool
    {
        if (!$this->validator->validate(['email' => $email]))
            return false;

        $userId = $this->userModel->getIdByEmail($email);

        $query = 'INSERT INTO password_reset (user_id, auth_key, expires_at) VALUES (:user_id, :auth_key, :expires_at)';

        $stmt = $this->db()->prepare($query);

        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':auth_key', $authKey, \PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', (new \DateTimeImmutable('+' . self::TIME_VALID . ' seconds'))->format('Y-m-d H:i:s'), \PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Verify auth key.
     *
     * @param  string $authKey
     * @return bool
     */
    public function verify(string $authKey): bool
    {
        if (!$this->validator->validate(['authKey' => $authKey]))
            return false;

        $query = 'SELECT password_reset.expires_at FROM password_reset WHERE auth_key = :auth_key';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':auth_key', $authKey, \PDO::PARAM_STR);

        $stmt->execute();

        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['expires_at'];

        if (!$this->validator->validate(['expiresAt' => $fetch]))
            return false;

        return true;
    }

    /**
     * Change user password with auth key.
     *
     * @param  string $authKey
     * @param  string $password
     * @param  string $passwordRepeat
     * @return bool
     */
    public function changePassword(string $authKey, string $password, string $passwordRepeat): bool
    {
        if (!$this->verify($authKey))
            return false;

        if (!$this->userModel->updatePassword($this->getUserId($authKey), $password, $passwordRepeat)) {
            $this->validator->setError($this->userModel->validator->getError());
            return false;
        }

        $this->delete($authKey);

        return true;
    }

    /**
     * Get user ID with auth key.
     *
     * @param  string $authKey
     * @return int
     */
    public function getUserId(string $authKey): ?int
    {
        $query = 'SELECT password_reset.user_id FROM password_reset WHERE auth_key = :auth_key';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':auth_key', $authKey, \PDO::PARAM_STR);

        $stmt->execute();

        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['user_id'] ?? null;

        return $fetch;
    }

    /**
     * Delete auth key from the database.
     *
     * @param  string $authKey
     * @return bool
     */
    public function delete(string $authKey): bool
    {
        $query = 'DELETE FROM password_reset WHERE auth_key = :auth_key';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':auth_key', $authKey, \PDO::PARAM_STR);

        return $stmt->execute();
    }
}