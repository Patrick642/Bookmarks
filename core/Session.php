<?php

namespace core;

class Session
{
    public function createSession(array $variables = [])
    {
        $new_session_id = session_create_id();
        $_SESSION['SID'] = $new_session_id;
        foreach ($variables as $k => $v) {
            $_SESSION[$k] = $v;
        }
    }

    public function deleteSession()
    {
        session_unset();
        session_destroy();
    }
}