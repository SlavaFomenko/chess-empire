<?php

namespace Handy\ORM\QueryStrategy;

use Handy\ORM\Exception\InvalidQueryTypeException;
use Handy\ORM\Query;

class MySQL8QueryStrategy implements QueryStrategy
{

    public function getSQL(Query $q): string
    {
        return match ($q->getType()) {
            Query::TYPE_SELECT => $this->select($q),
            Query::TYPE_INSERT => $this->insert($q),
            Query::TYPE_UPDATE => $this->update($q),
            Query::TYPE_DELETE => $this->delete($q),
            default => throw new InvalidQueryTypeException("Query type: \"" . $q->getType() . "\" is not supported by the " . self::class)
        };
    }

    private function select(Query $q): string
    {
        $sql = "SELECT " . implode(", ", $q->getColumns()) . " FROM " . $q->getTable();

        $sql .= $this->where($q) . $this->groupBy($q) . $this->orderBy($q) . $this->limit($q) . $this->offset($q);

        return $sql;
    }

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

    private function update(Query $q): string
    {
        $sql = "UPDATE " . $q->getTable() . " SET ";
        foreach ($q->getColumns() as $key => $value) {
            $sql .= "$value = " . $q->getValues()[0][$key] . ", ";
        }

        return rtrim($sql, ", ") . $this->where($q) . $this->orderBy($q) . $this->limit($q);
    }

    private function delete(Query $q): string
    {
        $sql = "DELETE FROM " . $q->getTable();

        return $sql .= $this->where($q) . $this->orderBy($q) . $this->limit($q);
    }

    private function where(Query $q): string
    {
        return empty($q->getConditions()) ? "" : " WHERE " . implode(" ", $q->getConditions());
    }

    private function groupBy(Query $q): string
    {
        return empty($q->getGroupBy()) ? "" : " GROUP BY " . implode(", ", $q->getGroupBy());
    }

    private function orderBy(Query $q): string
    {
        if (empty($q->getOrderBy())) {
            return "";
        }

        $sql = " ORDER BY ";
        foreach ($q->getOrderBy() as $orderBy) {
            $sql .= implode(" ", $orderBy) . ', ';
        }

        return trim($sql, ', ');
    }

    private function limit(Query $q): string
    {
        return is_null($q->getLimit()) ? "" : " LIMIT " . $q->getLimit();
    }

    private function offset(Query $q): string
    {
        return is_null($q->getOffset()) ? "" : " OFFSET " . $q->getOffset();
    }

}