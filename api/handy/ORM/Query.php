<?php

namespace Handy\ORM;

use Handy\ORM\Exception\InvalidQueryTypeException;

class Query
{

    public const TYPE_SELECT       = 'SELECT';
    public const TYPE_INSERT       = 'INSERT';
    public const TYPE_UPDATE       = 'UPDATE';
    public const TYPE_DELETE       = 'DELETE';
    public const TYPE_CREATE_TABLE = 'CREATE TABLE';
    public const TYPES             = [
        self::TYPE_SELECT,
        self::TYPE_INSERT,
        self::TYPE_UPDATE,
        self::TYPE_DELETE,
        self::TYPE_CREATE_TABLE,
    ];

    public const OPERATOR_AND = "AND";
    public const OPERATOR_OR  = "OR";

    /**
     * @var string
     */
    private string $type;

    /**
     * @var array
     */
    private array $columnDefinitions;

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
        $this->groupBy = [];
        $this->orderBy = [];
        $this->columnDefinitions = [];
    }

    /**
     * @param array $column
     * @return void
     */
    public function addColumnDefinition(array $column): void
    {
        $this->columnDefinitions[] = $column;
    }

    /**
     * @return array
     */
    public function getColumnDefinitions(): array
    {
        return $this->columnDefinitions;
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
     * @throws InvalidQueryTypeException
     */
    public function setType(string $type): void
    {
        if (!in_array($type, self::TYPES)) {
            throw new InvalidQueryTypeException("Invalid query type: " . $type);
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
     * @param string ...$conditions
     * @return void
     */
    public function addCondition(string ...$conditions): void
    {
        $this->conditions = array_merge($this->conditions, $conditions);
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

}