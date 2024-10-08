<?php
namespace core\Model;

abstract class Validator extends Model
{
    private ?string $error = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(string $error): void
    {
        $this->error = $error;
    }

    public function validate(array $methods = [])
    {
        foreach ($methods as $method => $args) {
            if ($this->$method(...(array) $args) === false)
                return false;
        }

        return true;
    }

    /**
     * **IMPORTANT: only $value parameter should contain data passed by a client!**
     * Check if a given value is/would be unique in a specific table, specific column.
     * 
     * @param  mixed $value
     * @param  string $tableName
     * @param  string $columnName
     * @return bool
     */
    protected function unique(mixed $value, string $tableName, string $columnName): bool
    {
        return !$this->exists($value, $tableName, $columnName);
    }

    /**
     * **IMPORTANT: only $value parameter should contain data passed by a client!**
     * Check if a given value exists in a specific table, specific column.
     *
     * @param  mixed $value
     * @param  string $tableName
     * @param  string $columnName
     * @return bool
     */
    protected function exists(mixed $value, string $tableName, string $columnName): bool
    {
        $query = 'SELECT COUNT(*) FROM ' . $tableName . ' WHERE ' . $columnName . ' = :value';

        $stmt = $this->db()->prepare($query);

        $stmt->bindValue(':value', $value);
        $stmt->execute();
        $fetch = $stmt->fetchColumn();

        return ($fetch > 0) ? true : false;
    }
}