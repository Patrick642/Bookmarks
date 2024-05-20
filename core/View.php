<?php
namespace core;

class View
{
    const VIEW_DIR = ROOT_DIR . '/src/View/';

    private SessionMessage $sessionMessage;

    public function __construct()
    {
        $this->sessionMessage = new SessionMessage();
    }

    public function get(string $view_name, array $variables = []): void
    {
        $variables['session_message'] = $this->sessionMessage->get();
        $file_path = self::VIEW_DIR . $view_name;

        extract($variables);

        if (!file_exists($file_path)) {
            throw new \ErrorException('View ' . $view_name . ' not found!');
        }

        $view = include_once ($file_path);
    }
}