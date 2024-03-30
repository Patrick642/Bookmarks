<?php
namespace core;

abstract class Controller
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    protected function sanitizeInput(mixed $input): mixed
    {
        $input = trim($input);
        $input = htmlspecialchars($input);
        return $input;
    }

    protected function isEmpty(mixed $input): bool
    {
        if (!isset ($input))
            return true;

        if (empty ($input))
            return true;

        return false;
    }
}