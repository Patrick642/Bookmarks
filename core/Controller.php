<?php
namespace core;

abstract class Controller
{
    protected DataUtility $dataUtility;
    protected Request $request;
    protected Session $session;
    protected View $view;

    public function __construct()
    {
        $this->dataUtility = new DataUtility();
        $this->request = new Request();
        $this->session = new Session();
        $this->view = new View();
    }

    public function requiredInputs(string $method, array $fields = []): bool
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
            if (!isset($array[$field]) || empty($this->dataUtility->sanitizeInput($array[$field]))) {
                return false;
            }
        }

        return true;
    }

    public function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }

    public function jsonEncode(array $parameters = [], bool $success = true, ?string $message = null): void
    {
        echo json_encode(array_merge(['success' => $success, 'message' => $message], $parameters));
        exit;
    }
}