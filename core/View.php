<?php
namespace core;

class View
{
    const VIEW_DIR = ROOT_DIR . '/src/View/';

    public function getView(string $view_name, array $variables = []): void
    {
        $file_path = self::VIEW_DIR . $view_name . '.phtml';

        extract($variables);

        if (!file_exists($file_path)) {
            throw new \ErrorException('View ' . $view_name . ' not found!');
        }

        $view = include_once ($file_path);
    }
}