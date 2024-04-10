<?php

namespace Handy\ORM;

class QueryBuilder
{

    /**
     * @var Query
     */
    private Query $query;

    /**
     * @var int
     */
    private int $paramsCount;

    /**
     *
     */
    public function __construct()
    {
        $this->query = new Query();
        $this->paramsCount = 1;
    }

    /**
     * @return $this
     */
    public function reset(): self
    {
        $this->query = new Query();
        $this->paramsCount = 1;
        return $this;
    }

    /**
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * @param array $columns
     * @return $this
     * @throws \Exception
     */
    public function select(array $columns = ["*"]): self
    {
        $this->query->setType('SELECT');
        $this->query->setColumns($columns);
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    public function from($table): self
    {
        $this->query->setTable($table);
        return $this;
    }

    /**
     * @param $table
     * @return $this
     * @throws \Exception
     */
    public function insertInto($table): self
    {
        $this->query->setType('INSERT');
        $this->query->setTable($table);
        return $this;
    }

    /**
     * @param $table
     * @return $this
     * @throws \Exception
     */
    public function update($table): self
    {
        $this->query->setType('UPDATE');
        $this->query->setTable($table);
        return $this;
    }

    /**
     * @param $table
     * @return $this
     * @throws \Exception
     */
    public function deleteFrom($table): self
    {
        $this->query->setType('DELETE');
        $this->query->setTable($table);
        return $this;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function where($condition): self
    {
        $this->query->addCondition($condition);
        return $this;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function andWhere($condition): self
    {
        $this->query->addCondition("AND $condition");
        return $this;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function orWhere($condition): self
    {
        $this->query->addCondition("OR $condition");
        return $this;
    }

    /**
     * @param $values
     * @return $this
     */
    public function setParam($values): self
    {
        foreach ($values as $key => $value) {
            $this->query->addParam($key, $value);
        }

        return $this;
    }

    /**
     * @param array $values
     * @return self
     */
    public function values(array $values): self
    {
        if (!is_array(array_values($values)[0])) {
            $values = [$values];
        }

        if (isset($values[0]) && is_array($values[0]) && !array_is_list($values[0])) {
            $columns = array_keys(array_merge(...$values));
            $this->query->setColumns($columns);

            $emptyRecord = array_combine($columns, array_fill(0, count($columns), null));

            foreach ($values as $record) {
                $extendedRecord = array_merge($emptyRecord, $record);
                $value = [];
                foreach ($extendedRecord as $item) {
                    $this->query->addParam("value" . $this->paramsCount, $item);
                    $value[] = ":value" . $this->paramsCount;
                    $this->paramsCount += 1;
                }
                $this->query->addValue($value);
            }

            return $this;
        }

        foreach ($values as $record) {
            $value = [];
            foreach ($record as $item) {
                $this->query->addParam("value" . $this->paramsCount, $item);
                $value[] = ":value" . $this->paramsCount;
                $this->paramsCount += 1;
            }
            $this->query->addValue($value);
        }

        return $this;
    }

}

