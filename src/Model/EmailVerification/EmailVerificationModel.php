<?php
namespace src\Model\EmailVerification;

use core\Model\Model;
use src\Model\User\UserModel;

final class EmailVerificationModel extends Model
{
    // The number of seconds that the email verification auth key is valid.
    public const TIME_VALID = 12 * 60 * 60;

    public UserModel $userModel;
    public EmailVerificationValidator $validator;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->validator = new EmailVerificationValidator();
    }

    /**
     * Add email verification auth key to the database.
     *
     * @param  int $userId
     * @param  string $email
     * @param  string $authKey
     * @return bool
     */
    public function add(int $userId, string $email, string $authKey): bool
    {
        $query = 'INSERT INTO email_verification (user_id, email, auth_key, expires_at) VALUES (:user_id, :email, :auth_key, :expires_at)';

        $stmt = $this->db()->prepare($query);

        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->bindValue(':auth_key', $authKey, \PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', (new \DateTimeImmutable('+' . self::TIME_VALID . ' seconds'))->format('Y-m-d H:i:s'), \PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Add auth key with new email to the database.
     *
     * @param  int $userId
     * @param  string $newEmail
     * @param  string $authKey
     * @return bool
     */
    public function addChangeEmail(int $userId, string $newEmail, string $authKey): bool
    {
        if (!$this->userModel->validator->validate(['email' => $newEmail])) {
            $this->validator->setError($this->userModel->validator->getError());
            return false;
        }

        return $this->add($userId, $newEmail, $authKey);
    }

    /**
     * Complete registration process.
     *
     * @param  string $authKey
     * @return bool
     */
    public function completeRegistration(string $authKey): bool
    {
        if (!$this->validator->validate(['authKey' => $authKey]))
            return false;

        $query = 'SELECT email_verification.user_id, email_verification.expires_at FROM email_verification WHERE auth_key = :auth_key';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':auth_key', $authKey, \PDO::PARAM_STR);

        $stmt->execute();

        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$this->validator->validate(['expiresAt' => $fetch['expires_at']]))
            return false;

        if ($this->userModel->updateValidity($fetch['user_id'], true)) {
            $this->delete($authKey);
            return true;
        }

        return false;
    }

    /**
     * Verify auth key.
     *
     * @param  string $authKey
     * @return bool
     */
    function verify(string $authKey): bool
    {
        if (!$this->validator->validate(['authKey' => $authKey]))
            return false;

        $query = 'SELECT email_verification.expires_at FROM email_verification WHERE auth_key = :auth_key';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':auth_key', $authKey, \PDO::PARAM_STR);

        $stmt->execute();

        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['expires_at'];

        if (!$this->validator->validate(['expiresAt' => $fetch]))
            return false;

        return true;
    }

    /**
     * Change user email with auth key.
     *
     * @param  mixed $authKey
     * @return bool
     */
    public function changeEmail(string $authKey): bool
    {
        if (!$this->verify($authKey))
            return false;

        $query = 'SELECT email_verification.user_id, email_verification.email FROM email_verification WHERE auth_key = :auth_key';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':auth_key', $authKey, \PDO::PARAM_STR);

        $stmt->execute();

        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$this->userModel->updateEmail($fetch['user_id'], $fetch['email']))
            return false;

        $this->userModel->updateValidity($fetch['user_id'], true);
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
        $query = 'SELECT email_verification.user_id FROM email_verification WHERE auth_key = :auth_key';

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
        $query = 'DELETE FROM email_verification WHERE auth_key = :auth_key';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':auth_key', $authKey, \PDO::PARAM_STR);

        return $stmt->execute();
    }
}