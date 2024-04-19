<?php

namespace Handy\ORM;

use Couchbase\InsertOptions;

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
     * @var array
     */
    private array $groupBy;

    /**
     * @var array
     */
    private array $orderBy;

    /**
     * @var int|null
     */
    private ?int $offset = null;

    /**
     * @var int|null
     */
    private ?int $limit = null;

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
     * @return array
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * @param string $column
     */
    public function addGroupBy(string $column): void
    {
        $this->groupBy[] = $column;
    }

    /**
     * @return array
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @param string $column
     * @param string $direction
     */
    public function addOrderBy(string $column, string $direction = 'ASC'): void
    {
        $this->orderBy[] = [
            $column,
            $direction
        ];
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
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
     * @param array|string|null $values
     * @return void
     */
    public function addParam(string $name, array|string|null $values): void
    {
        $this->params[$name] = $values;
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
                $query .= " VALUES ";
                foreach ($this->getValues() as $key => $value) {
                    $query .= "(" . implode(", ", $value) . "), ";
                }
                $query = rtrim($query, ", ");
                break;
            case self::TYPE_UPDATE:
                $query = "UPDATE " . $this->table . " SET ";
                foreach ($this->columns as $key => $value) {
                    $query .= "$value = " . $this->values[0][$key] . ", ";
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

        if (!empty($this->conditions) && $this->type !== self::TYPE_INSERT) {
            $query .= " WHERE " . implode(" ", $this->conditions);
        }

        if ($this->type !== self::TYPE_SELECT) {
            return $query;
        }

        if (!empty($this->groupBy)) {
            $query .= " GROUP BY " . implode(", ", $this->groupBy);
        }

        if (!empty($this->orderBy)) {
            $query .= " ORDER BY ";
            foreach ($this->orderBy as $orderBy) {
                $query .= implode(" ", $orderBy) . ', ';
            }
            $query = trim($query, ', ');
        }

        if (!is_null($this->limit)) {
            $query .= " LIMIT " . $this->limit;
        }

        if (!is_null($this->offset)) {
            $query .= " OFFSET " . $this->offset;
        }

        return $query;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        $pdo = new \PDO($_ENV["DB_URL"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $sth = $pdo->prepare($this->getSql());

        foreach ($this->getParams() as $key => $value) {
            $sth->bindValue($key, $value);
        }

        $sth->execute();

        return $sth->fetchAll();
    }

}