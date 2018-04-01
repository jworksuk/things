<?php

namespace Things\Infrastructure\Persistence\Sql;

use DateTime;
use PHPUnit\Framework\Assert;
use Things\Domain\Model\User\User;
use Things\Domain\Model\User\UserId;

/**
 * Class SqlUserRepositoryTest
 * @package Things\Infrastructure\Persistence\Sql
 */
class SqlUserRepositoryTest extends PDOTestCase
{
    public function testFindByUserId()
    {
        $userId = new UserId;
        $sqlUserRepository = new SqlUserRepository(
            $this->createMockPDO(
                [
                    'id' => $userId->getId(),
                    'email' => 'test@test.com',
                    'name' => 'Test',
                    'password' => '12345678',
                    'created_at' => '2017-01-01',
                    'updated_at' => '2017-01-01'
                ]
            )
        );

        $user = $sqlUserRepository->findByUserId($userId);

        Assert::assertInstanceOf(User::class, $user);
        Assert::assertTrue($userId->equals($user->getUserId()));
        Assert::assertInstanceOf(DateTime::class, $user->getCreatedAt());
    }

    public function testFindByUserIdNull()
    {
        $sqlUserRepository = new SqlUserRepository($this->createMockPDO());
        $user = $sqlUserRepository->findByUserId(new UserId);

        Assert::assertNull($user);
    }

    public function testFindByUserEmail()
    {
        $email = 'test@test.com';

        $sqlUserRepository = new SqlUserRepository(
            $this->createMockPDO([
                'id' => new UserId,
                'email' => $email,
                'name' => 'Test',
                'password' => '12345678',
                'created_at' => '2017-01-01',
                'updated_at' => '2017-01-01'
            ])
        );
        $user = $sqlUserRepository->findByEmail($email);

        Assert::assertInstanceOf(User::class, $user);
        Assert::assertEquals($email, $user->getEmail());
        Assert::assertInstanceOf(DateTime::class, $user->getCreatedAt());
    }

    public function testFindByUserEmailNull()
    {
        $sqlUserRepository = new SqlUserRepository($this->createMockPDO());
        $user = $sqlUserRepository->findByEmail('test@test.com');

        Assert::assertNull($user);
    }

    public function testSave()
    {
        $sqlUserRepository = new SqlUserRepository($this->createMockPDO(true));

        $result = $sqlUserRepository->save(
            new User(
                new UserId,
                'test@test.com',
                'Hi',
                '87654321',
                new DateTime,
                new DateTime
            )
        );

        Assert::assertTrue(is_bool($result));
    }

    public function testNextId()
    {
        $sqlUserRepository = new SqlUserRepository(self::createMock(\PDO::class));
        $userId = $sqlUserRepository->nextId();
        Assert::assertInstanceOf(UserId::class, $userId);
    }
}
