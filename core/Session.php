<?php

namespace core;

class Session
{
    public function setUserId(int $user_id): void
    {
        $_SESSION['user_id'] = $user_id;
    }

    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public function setUsername(string $username): void
    {
        $_SESSION['username'] = $username;
    }

    public function getUsername(): ?string
    {
        return $_SESSION['username'] ?? null;
    }

    public function setFlashMessage(string $message): void
    {
        $_SESSION['message'] = $message;
    }

    public function getFlashMessage(): ?string
    {
        $message = $_SESSION['message'] ?? null;
        unset($_SESSION['message']);
        return $message;
    }

    public function regenerateId(): void
    {
        session_regenerate_id(true);
    }

    public function destroy(): void
    {
        session_unset();
        session_destroy();
    }
}