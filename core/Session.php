<?php

namespace core;

class Session
{
    public function setUserId(string $user_id)
    {
        $_SESSION['user_id'] = $user_id;
    }

    public function setUsername(string $username)
    {
        $_SESSION['username'] = $username;
    }

    public function deleteSession()
    {
        session_unset();
        session_destroy();
    }
}