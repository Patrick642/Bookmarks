<?php
namespace core;

class View
{
    const VIEW_DIR = ROOT_DIR . '/src/View/';

    private Session $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Include view.
     *
     * @param  string $viewName
     * @param  array $variables
     * @return void
     */
    public function get(string $viewName, array $variables = []): void
    {
        $variables['session_message'] = $this->session->getFlashMessage();
        $filePath = self::VIEW_DIR . $viewName;

        extract($variables);

        if (!file_exists($filePath)) {
            throw new \ErrorException('View ' . $viewName . ' not found!');
        }

        include $filePath;
    }

    /**
     * Include view and return it as a string.
     *
     * @param  string $viewName
     * @param  array $variables
     * @return string
     */
    public function getString(string $viewName, array $variables = []): string
    {
        ob_start();
        $this->get($viewName, $variables);
        $string = ob_get_clean();

        return $string;
    }
}