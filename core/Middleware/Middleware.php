<?php
namespace core\Middleware;

class Middleware
{
    const MAP = [
        'all' => null,
        'guest' => Guest::class,
        'auth' => Auth::class
    ];
}