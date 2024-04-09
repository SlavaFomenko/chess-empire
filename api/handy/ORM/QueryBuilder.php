<?php

namespace Handy\ORM;

class QueryBuilder
{

    /**
     * @var Query
     */
    private Query $query;

    /**
     *
     */
    public function __construct()
    {
        $this->query = new Query();
    }

    /**
     * @return $this
     */
    public function reset(): self
    {
        $this->query = new Query();
        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     * @throws \Exception
     */
    public function select(array $columns): self
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
     * @param $values
     * @return self
     */
    public function values($values): self
    {

        if (isset($values[0]) && is_array($values[0])) {
            $paramsCounter = 1;
            foreach ($values as $value) {
                $this->query->addValue(":value" . $paramsCounter);
                $this->query->addParam("value" . $paramsCounter, $value);
                $paramsCounter++;
            }
            return $this;
        }

        $columns = array_keys($values);
        $this->query->setColumns($columns);

        foreach ($values as $key => $value) {

            $this->query->addValue(":" . $key);
            $this->query->addParam($key, $value);

        }

        return $this;
    }
}