<?php
namespace core;

class Request
{
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getPath(): string
    {
        return rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/');
    }

    public function isXMLHttpRequest(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}