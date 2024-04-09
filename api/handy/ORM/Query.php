<?php

namespace Handy\ORM;

class Query
{

    public const TYPE_SELECT = 'SELECT';
    public const TYPE_INSERT = 'INSERT';
    public const TYPE_UPDATE = 'UPDATE';
    public const TYPE_DELETE = 'DELETE';
    public const TYPES       = [
        self::TYPE_SELECT,
        self::TYPE_INSERT,
        self::TYPE_UPDATE,
        self::TYPE_DELETE
    ];

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $table;

    /**
     * @var array
     */
    private array $columns;

    /**
     * @var array
     */
    private array $conditions;

    /**
     * @var array
     */
    private array $values;

    /**
     * @var array
     */
    private array $params;

    /**
     *
     */
    public function __construct()
    {
        $this->conditions = [];
        $this->values = [];
        $this->params = [];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @throws \Exception
     */
    public function setType(string $type): void
    {
        if (!in_array($type, self::TYPES)) {
            throw new \Exception("Invalid query type: " . $type);
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @param array $conditions
     */
    public function setConditions(array $conditions): void
    {
        $this->conditions = $conditions;
    }

    /**
     * @param $condition
     * @return void
     */
    public function addCondition($condition): void
    {
        $this->conditions[] = $condition;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @param $values
     * @return void
     */
    public function addValue($values): void
    {
        $this->values[] = $values;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @param string $name
     * @param array|string $values
     * @return void
     */
    public function addParam(string $name, array|string $values): void
    {
        if (!is_array($values)) {
            $this->params[$name] = $values;
            return;
        }

        $escapedValues = [];
        foreach ($values as $value) {
            if (is_string($value)) {
                $escapedValues[] = "'" . addslashes($value) . "'";
            } else {
                $escapedValues[] = $value;
            }
        }
        $this->params[$name] = '(' . implode(', ', $escapedValues) . ')';
    }

    /**
     * @return string
     */
    public function getSQL(): string
    {
        switch ($this->type) {
            case self::TYPE_SELECT:
                $query = "SELECT " . implode(", ", $this->columns) . " FROM " . $this->table;
                break;
            case self::TYPE_INSERT:
                $query = "INSERT INTO " . $this->table;
                if (!empty($this->columns)) {
                    $query .= " (" . implode(", ", $this->columns) . ")";
                }
                $query .= " VALUES (" . implode(", ", $this->values) . ")";
                break;
            case self::TYPE_UPDATE:
                $query = "UPDATE " . $this->table . " SET ";
                foreach ($this->columns as $value) {
                    $query .= "$value = :$value, ";
                }
                $query = rtrim($query, ", ");
                break;
            case self::TYPE_DELETE:
                $query = "DELETE FROM " . $this->table;
                break;
            default:
                $query = "";
                break;
        }

        if (!empty($this->conditions)) {
            $query .= " WHERE " . implode(" ", $this->conditions);
        }

        return $query;
    }
}