<?php

namespace Things\Infrastructure\Persistence\Sql;

use PDO;
use PDOStatement;

/**
 * Class SqlRepository
 * @package Things\Infrastructure\Persistence\Sql
 */
abstract class SqlRepository
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Execute mysql statement with parameters.
     *
     * @param  string $sql
     * @param  array  $parameters
     * @return PDOStatement
     */
    protected function execute(string $sql, array $parameters = [])
    {
        $st = $this->pdo->prepare($sql);

        $st->execute($parameters);

        return $st;
    }
}
