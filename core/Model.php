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
            try {
                $dsn = 'mysql: host=' . DB_HOST . '; dbname=' . DB_NAME;
                $dbh = new PDO($dsn, DB_USER, DB_PASSWORD);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db = $dbh;
            } catch (PDOException $e) {
                (new Error)->logError($e->getMessage());
                throw new \Exception('The app is currently unavailable, please try again later.');
            }
        }
    }

    protected function db()
    {
        return $this->db;
    }
}