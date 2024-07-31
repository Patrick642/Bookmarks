<?php
namespace src\Utility;

class AuthKey
{
    public function generate(): string
    {
        return substr(bin2hex(random_bytes(128)), 0, 255);
    }
}