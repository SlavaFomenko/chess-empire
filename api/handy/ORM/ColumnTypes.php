<?php

namespace Handy\ORM;

enum ColumnTypes: string
{

    case INT = 'INT';
    case BIGINT = 'BIGINT';
    case DECIMAL = 'DECIMAL';
    case DATETIME = 'DATETIME';
    case TIMESTAMP = 'TIMESTAMP';
    case VARCHAR = 'VARCHAR';
    case TEXT = 'TEXT';
    case BINARY = 'BINARY';
    case JSON = 'JSON';

}
