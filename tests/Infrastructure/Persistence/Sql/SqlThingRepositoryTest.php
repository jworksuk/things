<?php

namespace Things\Infrastructure\Persistence\Sql;

use PHPUnit\Framework\Assert;
use Things\Domain\Model\Thing\Thing;
use Things\Domain\Model\Thing\ThingId;
use Things\Domain\Model\User\UserId;

/**
 * Class SqlUserRepositoryTest
 * @package Things\Infrastructure\Persistence\Sql
 */
class SqlThingRepositoryTest extends PDOTestCase
{
    public function testFindAllByUserId()
    {
        $userId = new UserId;

        $sqlThingRepository = new SqlThingRepository(
            $this->createMockPDO([
                [
                    'id' => new ThingId,
                    'userId' => $userId->getId(),
                    'name' => 'Test #1',
                    'description' => 'Description #1',
                ],
                [
                    'id' => new ThingId,
                    'userId' => $userId->getId(),
                    'name' => 'Test #2',
                    'description' => 'Description #2',
                ]
            ], 'fetchAll')
        );
        $things = $sqlThingRepository->findAllByUserId($userId);

        foreach ($things as $thing) {
            Assert::assertInstanceOf(Thing::class, $thing);
            Assert::assertTrue($userId->equals($thing->getUserId()));
        }
    }

    public function testFindById()
    {
        $thingId = new ThingId;

        $sqlThingRepository = new SqlThingRepository(
            $this->createMockPDO([
                'id' => $thingId->getId(),
                'userId' => new UserId,
                'name' => 'Test',
                'description' => 'Description',
            ])
        );
        $thing = $sqlThingRepository->findById($thingId);

        Assert::assertInstanceOf(Thing::class, $thing);
        Assert::assertTrue($thingId->equals($thing->getThingId()));
    }

    public function testFindByIdNull()
    {
        $sqlThingRepository = new SqlThingRepository($this->createMockPDO(false));
        $thing = $sqlThingRepository->findById(new ThingId);

        Assert::assertNull($thing);
    }

    public function testUpdate()
    {
        $sqlThingRepository = new SqlThingRepository($this->createMockPDO(true));
        $result = $sqlThingRepository->update(
            new Thing(
                new ThingId,
                new UserId,
                'name',
                'description'
            )
        );

        Assert::assertTrue(is_bool($result));
    }

    public function testSave()
    {
        $sqlThingRepository = new SqlThingRepository($this->createMockPDO(true));
        $result = $sqlThingRepository->save(
            new Thing(
                new ThingId,
                new UserId,
                'name',
                'description'
            )
        );

        Assert::assertTrue(is_bool($result));
    }

    public function testDelete()
    {
        $sqlThingRepository = new SqlThingRepository($this->createMockPDO(true));
        $result = $sqlThingRepository->delete(
            new Thing(
                new ThingId,
                new UserId,
                'name',
                'description'
            )
        );

        Assert::assertTrue(is_bool($result));
    }

    public function testNextId()
    {
        $sqlThingRepository = new SqlThingRepository(self::createMock(\PDO::class));
        $thingId = $sqlThingRepository->nextId();
        Assert::assertInstanceOf(ThingId::class, $thingId);
    }
}
