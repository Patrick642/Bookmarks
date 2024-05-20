<?php
namespace Core;

class SessionMessage
{
    /**
     * Set session message
     *
     * @param  mixed $message
     * @return void
     */
    public function set(string $message): void
    {
        $_SESSION['message'] = $message;
    }

    /**
     * Get session message and remove it from session after
     *
     * @return string
     */
    public function get(): ?string
    {
        $message = $_SESSION['message'] ?? null;
        unset($_SESSION['message']);
        return $message;
    }
}