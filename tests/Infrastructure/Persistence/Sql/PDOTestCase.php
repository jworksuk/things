<?php

namespace Things\Infrastructure\Persistence\Sql;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

/**
 * Trait CreateMockPDOTrait
 * @package Things\Infrastructure\Persistence\Sql
 */
abstract class PDOTestCase extends TestCase
{
    /**
     * @param array|null|bool $result
     * @param string $fetchType
     * @return \PHPUnit_Framework_MockObject_MockObject|PDO
     */
    protected function createMockPDO($result = null, string $fetchType = 'fetch')
    {
        $mockPDO = self::createMock(PDO::class);
        $mockPDOStatement = self::createMock(PDOStatement::class);

        $mockPDO->method('prepare')->willReturn($mockPDOStatement);
        $mockPDOStatement->method('execute')->willReturn(true);
        $mockPDOStatement->method($fetchType)->willReturn($result);
        return $mockPDO;
    }
}
