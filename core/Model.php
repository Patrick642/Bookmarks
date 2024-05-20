<?php
namespace core;

use PDO, PDOException;

abstract class Model
{
    private $db = null;

    protected $query;

    public function __construct()
    {
        if ($this->db === null) {
            $this->getConfig();

            try {
                $dsn = 'mysql: host=' . DB_HOST . '; dbname=' . DB_NAME;
                $dbh = new PDO($dsn, DB_USER, DB_PASSWORD);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db = $dbh;
            } catch (PDOException $e) {
                throw new \ErrorException($e->getMessage());
            }
        }
    }

    protected function db()
    {
        return $this->db;
    }

    private function getConfig(): void
    {
        $config_file = null;

        switch (ENV) {
            case 'prod':
                $config_file = ROOT_DIR . '/config/db.php';
                break;

            case 'dev':
            default:
                $config_file = ROOT_DIR . '/config/db_local.php';
                break;
        }

        if (file_exists($config_file))
            include_once $config_file;
        else
            throw new \ErrorException($config_file . ' not found!');
    }
}