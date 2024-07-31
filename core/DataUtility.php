<?php
namespace Core;

class DataUtility
{
    public function sanitizeInput(mixed $input): mixed
    {
        return htmlspecialchars(trim($input));
    }

    public function convertToPascalCase(string $string): string
    {
        return str_replace(' ', '', ucwords(strtolower(preg_replace('/[\\s\-_]+/', ' ', $string))));
    }

    public function convertToCamelCase(string $string): string
    {
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $string)), '_');
    }

    public function convertSecondsToHuman(int $seconds): string
    {
        $human = '';

        if ($seconds >= 3600) {
            $human .= floor($seconds / 3600) . ' hours';
            $seconds %= 3600;
        }

        if ($seconds > 60) {
            $human .= ((strlen($human) > 0) ? ', ' : '') . floor($seconds / 60) . ' minutes';
            $seconds %= 60;
        }

        if ($seconds > 0) {
            $human .= ((strlen($human) > 0) ? ', ' : '') . $seconds . ' seconds';
        }

        return $human;
    }

    public function addProtocol(string $url)
    {
        if (empty(parse_url($url, PHP_URL_SCHEME)))
            $url = 'http://' . ltrim($url, '/');

        return $url;
    }
}