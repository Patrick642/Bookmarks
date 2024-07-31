<?php
namespace src\Model\Bookmark;

use core\Model\Validator;

final class BookmarkValidator extends Validator
{
    public const MAX_LABEL_LENGTH = 64;
    public const MAX_URL_LENGTH = 2048;

    public function __construct()
    {
        parent::__construct();
    }

    public function label(string $label): bool
    {
        if (empty($label)) {
            $this->setError('Bookmark label cannot be empty.');
            return false;
        }

        if (strlen($label) > self::MAX_LABEL_LENGTH) {
            $this->setError('Label can contain a maximum of ' . self::MAX_LABEL_LENGTH . ' characters.');
            return false;
        }

        return true;
    }

    public function url(string $url): bool
    {
        if (empty($url)) {
            $this->setError('Bookmark url cannot be empty.');
            return false;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->setError('Bookmark url is incorrect.');
            return false;
        }

        if (strlen($url) > self::MAX_URL_LENGTH) {
            $this->setError('Url can contain a maximum of ' . self::MAX_URL_LENGTH . ' characters.');
            return false;
        }

        return true;
    }
}