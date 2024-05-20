<?php
namespace src\Model;

use core\Model, PDO;

class PasswordResetModel extends Model
{
    // The number of minutes that the password reset link is valid. For security reasons, this value should not be too high.
    public const TIME_VALID = 15;
    
    /**
     * Add reset key to database
     *
     * @param  mixed $email
     * @param  mixed $reset_key
     * @return bool
     */
    public function add(string $email, string $reset_key): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new \Exception($email . ' is not a valid email.');

        $query = $this->db()->prepare('INSERT INTO password_reset(email, reset_key, expire) VALUES(:email, :reset_key, :expire)');

        return $query->execute([
            ':email' => $email,
            ':reset_key' => $reset_key,
            ':expire' => (new \DateTimeImmutable('+' . self::TIME_VALID . ' minutes'))->format('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Check if key is valid
     *
     * @param  mixed $reset_key
     * @return bool
     */
    public function validate(string $reset_key): bool
    {
        $query = $this->db()->prepare('SELECT password_reset.reset_key, password_reset.expire FROM password_reset WHERE reset_key = :reset_key');

        $query->execute([
            ':reset_key' => $reset_key
        ]);

        if ($query->rowCount() === 0)
            throw new \Exception('Invalid link.');

        $data_fetch = $query->fetch(PDO::FETCH_ASSOC);

        if (new \DateTimeImmutable($data_fetch['expire']) < new \DateTimeImmutable())
            throw new \Exception('The password reset link has expired.');

        return true;
    }
    
    /**
     * Get email by reset key
     *
     * @param  mixed $reset_key
     * @return string
     */
    public function getEmailByKey(string $reset_key): ?string
    {
        if (!$this->validate($reset_key))
            return null;

        $query = $this->db()->prepare('SELECT password_reset.email FROM password_reset WHERE password_reset.reset_key = :reset_key');

        $query->execute([
            ':reset_key' => $reset_key
        ]);

        $data_fetch = $query->fetch(PDO::FETCH_ASSOC);

        return $data_fetch['email'];
    }
    
    /**
     * Delete reset key from database
     *
     * @param  mixed $reset_key
     * @return bool
     */
    public function delete(string $reset_key): bool
    {
        $query = $this->db()->prepare('DELETE FROM password_reset WHERE reset_key = :reset_key');

        return $query->execute([
            ':reset_key' => $reset_key
        ]);
    }
    
    /**
     * Generate reset key of 255 characters
     *
     * @return string
     */
    public function generateKey(): string
    {
        return substr(bin2hex(random_bytes(128)), 0, 255);
    }
}