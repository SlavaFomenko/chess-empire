<?php

namespace Handy\ORM;

use Exception;
use Handy\Exception\ClassNotFoundException;
use Handy\ORM\Exception\DatabaseConnectionFailedException;
use Handy\ORM\Exception\InvalidDatabaseConfigException;
use Handy\ORM\Exception\UnsupportedDbmsException;
use Handy\ORM\QueryStrategy\MySQL8QueryStrategy;
use Handy\ORM\QueryStrategy\QueryStrategy;
use PDO;

class Connection
{

    // TODO place here a valid strategy class
    public const QUERY_STRATEGIES = [
        "mysql8" => "MYSQL_STRATEGY_CLASS"
    ];

    private ?PDO $pdo;

    public function __construct()
    {
        $this->pdo = null;
    }

    public function connect(): void
    {
        $missingVars = array_filter([
            "DB_MS",
            "DB_URL",
            "DB_USER",
            "DB_PASSWORD"
        ], fn($var) => !isset($_ENV[$var]));
        if (!empty($missingVars)) {
            throw new InvalidDatabaseConfigException("Missing " . implode(", ", $missingVars) . " in database config");
        }

        if (!in_array($_ENV["DB_MS"], array_keys(self::QUERY_STRATEGIES))) {
            throw new UnsupportedDbmsException("Unsupported DBMS: " . $_ENV["DB_MS"]);
        }

        $options = [
            PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($_ENV["DB_URL"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"], $options);
        } catch (Exception $e) {
            throw new DatabaseConnectionFailedException($e->getMessage());
        }
    }

    public function isConnected(): bool
    {
        return $this->pdo !== null;
    }

    public function execute(Query $query): array
    {
        return [];
    }

}