<?php

namespace core;

class Error
{
    /**
     * Error handler
     *
     * @param  mixed $level
     * @param  mixed $message
     * @param  mixed $file
     * @param  mixed $line
     * @return void
     */
    public function errorHandler($level, $message, $file, $line): void
    {
        $msg = "Error: [$level] $message - $file:$line";
        $this->logError($msg);
    }

    /**
     * Exception handler
     *
     * @param  mixed $exception
     * @return void
     */
    public function exceptionHandler($exception): void
    {
        $msg = "[Exception] {$exception->getMessage()} in {$exception->getFile()} on line {$exception->getLine()}";

        $code = $exception->getCode();

        if ($code !== 403 && $code !== 404) {
            $code = 500;
            $this->logError($msg);
        }

        http_response_code($code);

        (new View)->get('error/' . $code . '.phtml');
    }

    /**
     * Save error log to file
     *
     * @param  mixed $message
     * @return void
     */
    public function logError(string $message): void
    {
        $log = '[' . date('Y-m-d, H:i:s') . '] ' . $message . PHP_EOL;
        error_log($log, 3, LOG_ERRORS);
    }
}