<?php
namespace core\Model;

use core\Config;
use PDO;
use PDOException;

abstract class Model
{
    private $db = null;
    private Config $config;

    public function __construct()
    {
        $this->config = new Config();

        if ($this->db === null) {
            $this->config->get('db');

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
}