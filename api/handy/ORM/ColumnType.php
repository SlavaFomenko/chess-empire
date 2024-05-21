<?php

namespace Handy\ORM;

use Closure;
use DateTime;
use DateTimeInterface;

enum ColumnType: string
{

    case INT = 'INT';
    case BIGINT = 'BIGINT';
    case DECIMAL = 'DECIMAL';
    case DATETIME = 'DATETIME';
    case TIMESTAMP = 'TIMESTAMP';
    case VARCHAR = 'VARCHAR';
    case BOOL = "BOOL";
    case TEXT = 'TEXT';
    case JSON = 'JSON';

    public const DATETIME_FORMAT = "Y-m-d H:i:s";

    public function hasLength(): bool
    {
        return match ($this) {
            self::INT, self::BIGINT, self::VARCHAR => true,
            default => false
        };
    }

    public function hasPrecisionAndScale(): bool
    {
        return match ($this) {
            self::DECIMAL => true,
            default => false
        };
    }

    public function phpTypeName(): string
    {
        return match ($this) {
            self::INT, self::BIGINT, self::TIMESTAMP => "int",
            self::DECIMAL => "float",
            self::DATETIME => DateTimeInterface::class,
            self::JSON => "array",
            self::BOOL => "bool",
            default => "string"
        };
    }

    public function sqlToPhp(): Closure
    {
        return match ($this) {
            self::INT => fn($value) => (int)$value,
            self::BIGINT => fn($value) => (int)$value,
            self::DECIMAL => fn($value) => (float)$value,
            self::DATETIME => fn($value) => DateTime::createFromFormat(self::DATETIME_FORMAT, $value),
            self::TIMESTAMP => fn($value) => (int)$value,
            self::BOOL => fn($value) => (bool)$value,
            self::VARCHAR => fn($value) => (string)$value,
            self::TEXT => fn($value) => (string)$value,
            self::JSON => fn($value) => json_decode($value, true)
        };
    }

    public function phpToSql(): Closure
    {
        return match ($this) {
            self::INT => fn(?int $value) => $value,
            self::BIGINT => fn(?int $value) => $value,
            self::DECIMAL => fn(?float $value) => $value,
            self::DATETIME => fn(?DateTimeInterface $value) => $value->format(self::DATETIME_FORMAT),
            self::TIMESTAMP => fn(?int $value) => $value,
            self::BOOL => fn(?bool $value) => $value ? 1 : 0,
            self::VARCHAR => fn(?string $value) => $value,
            self::TEXT => fn(?string $value) => $value,
            self::JSON => fn(?array $value) => json_encode($value)
        };
    }

}
