<?php

namespace Handy\ORM\QueryStrategy;

use Handy\ORM\Exception\InvalidQueryTypeException;
use Handy\ORM\Query;

class MySQL8QueryStrategy implements QueryStrategy
{

    /**
     * @param Query $q
     * @return string
     * @throws InvalidQueryTypeException
     */
    public function getSQL(Query $q): string
    {
        return match ($q->getType()) {
            Query::TYPE_SELECT => $this->select($q),
            Query::TYPE_INSERT => $this->insert($q),
            Query::TYPE_UPDATE => $this->update($q),
            Query::TYPE_DELETE => $this->delete($q),
            Query::TYPE_CREATE_TABLE => $this->createTable($q),
            default => throw new InvalidQueryTypeException("Query type: \"" . $q->getType() . "\" is not supported by the " . self::class)
        };
    }

    /**
     * @param Query $q
     * @return string
     */
    private function createTable(Query $q): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$q->getTable()}";

        $columns = array_map(function ($column) {
            $columnDefinition = "{$column['name']} {$column['type']->value}";

            if (!empty($column['size']) && $column['type']->hasLength()) {
                $columnDefinition .= "(" . $column['size'][0] . ")";
            } else if (!empty($column['size']) && $column['type']->hasPrecisionAndScale()) {
                $columnDefinition .= "(" . implode(", ", $column['size']) . ")";
            }

            $columnDefinition .= $column['pk'] ? " PRIMARY KEY" : "";
            $columnDefinition .= $column['nullable'] === false ? " NOT NULL" : "";
            $columnDefinition .= $column['increment'] ? " AUTO_INCREMENT" : "";
            $columnDefinition .= $column['unique'] ? " UNIQUE" : "";

            return $columnDefinition;
        }, $q->getColumnDefinitions());


        if (!empty($columns)) {
            $sql .= "(" . implode(", ", $columns) . ");";
        }

        return $sql;
    }

    /**
     * @param Query $q
     * @return string
     */
    private function select(Query $q): string
    {
        $sql = "SELECT " . implode(", ", $q->getColumns()) . " FROM " . $q->getTable();

        $sql .= $this->where($q) . $this->groupBy($q) . $this->orderBy($q) . $this->limit($q) . $this->offset($q);

        return $sql;
    }

    /**
     * @param Query $q
     * @return string
     */
    private function insert(Query $q): string
    {
        $sql = "INSERT INTO " . $q->getTable();

        if (!empty($q->getColumns())) {
            $sql .= " (" . implode(", ", $q->getColumns()) . ")";
        }

        $sql .= " VALUES ";
        foreach ($q->getValues() as $key => $value) {
            $sql .= "(" . implode(", ", $value) . "), ";
        }

        return rtrim($sql, ", ");
    }

    /**
     * @param Query $q
     * @return string
     */
    private function update(Query $q): string
    {
        $sql = "UPDATE " . $q->getTable() . " SET ";
        foreach ($q->getColumns() as $key => $value) {
            $sql .= "$value = " . $q->getValues()[0][$key] . ", ";
        }

        return rtrim($sql, ", ") . $this->where($q) . $this->orderBy($q) . $this->limit($q);
    }

    /**
     * @param Query $q
     * @return string
     */
    private function delete(Query $q): string
    {
        $sql = "DELETE FROM " . $q->getTable();

        return $sql .= $this->where($q) . $this->orderBy($q) . $this->limit($q);
    }

    /**
     * @param Query $q
     * @return string
     */
    private function where(Query $q): string
    {
        return empty($q->getConditions()) ? "" : " WHERE " . implode(" ", $q->getConditions());
    }

    /**
     * @param Query $q
     * @return string
     */
    private function groupBy(Query $q): string
    {
        return empty($q->getGroupBy()) ? "" : " GROUP BY " . implode(", ", $q->getGroupBy());
    }

    /**
     * @param Query $q
     * @return string
     */
    private function orderBy(Query $q): string
    {
        if (empty($q->getOrderBy())) {
            return "";
        }

        $orderBys = "";
        foreach ($q->getOrderBy() as $orderBy) {
            $orderBys .= implode(" ", $orderBy) . ', ';
        }

        return " ORDER BY " . trim($orderBys, ', ');
    }

    /**
     * @param Query $q
     * @return string
     */
    private function limit(Query $q): string
    {
        return is_null($q->getLimit()) ? "" : " LIMIT " . $q->getLimit();
    }

    /**
     * @param Query $q
     * @return string
     */
    private function offset(Query $q): string
    {
        return is_null($q->getOffset()) ? "" : " OFFSET " . $q->getOffset();
    }

}