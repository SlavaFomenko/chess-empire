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

    public const QUERY_STRATEGIES = [
        "mysql8" => MySQL8QueryStrategy::class
    ];

    private ?PDO $pdo;
    private QueryStrategy $queryStrategy;

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

        $queryStrategyClass = self::QUERY_STRATEGIES[$_ENV["DB_MS"]];
        if (!class_exists($queryStrategyClass)) {
            throw new ClassNotFoundException(self::QUERY_STRATEGIES[$_ENV["DB_MS"]] . " class not found");
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

        $this->queryStrategy = new $queryStrategyClass;
    }

    public function isConnected(): bool
    {
        return $this->pdo !== null;
    }

    public function execute(Query $query): array
    {
        $sql = $this->queryStrategy->getSQL($query);

        $sth = $this->pdo->prepare($sql);
        foreach ($query->getParams() as $key => $value) {
            $sth->bindValue($key, $value);
        }

        $sth->execute();

        return $sth->fetchAll();
    }

}