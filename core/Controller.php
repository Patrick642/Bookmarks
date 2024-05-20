<?php
namespace core;

abstract class Controller
{
    protected Request $request;
    protected View $view;
    protected SessionMessage $sessionMessage;
    protected Session $session;

    public function __construct()
    {
        $this->request = new Request();
        $this->view = new View();
        $this->sessionMessage = new SessionMessage();
        $this->session = new Session();
    }

    protected function sanitizeInput(mixed $input): mixed
    {
        $input = trim($input);
        $input = htmlspecialchars($input);
        return $input;
    }

    function formFields(string $method, array $fields = [])
    {
        switch ($method) {
            case 'POST':
                $array = $_POST;
                break;
            case 'GET':
                $array = $_GET;
                break;
            default:
                return false;
        }

        foreach ($fields as $field) {
            if (!isset($array[$field]) || empty($this->sanitizeInput($array[$field]))) {
                return false;
            }
        }

        return true;
    }
}