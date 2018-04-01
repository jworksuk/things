<?php

namespace Things\Domain\Model\User;

use DateTime;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 * @package Things\Domain\Model\User
 */
class UserTest extends TestCase
{
    public function userProvider()
    {
        return [
            [new User(new UserId('MyId'), 'test@test.com', 'Jane Mane', '12345678', new \DateTime, new \DateTime)]
        ];
    }

    /**
     * @dataProvider userProvider
     * @param User $user
     * @expectedException \InvalidArgumentException
     * @throws \Assert\AssertionFailedException
     */
    public function testSetEmailInvalidArgumentException(User $user)
    {
        $user->setEmail('');
    }

    /**
     * @dataProvider userProvider
     * @param User $user
     * @expectedException \InvalidArgumentException
     */
    public function testSetPasswordInvalidArgumentException(User $user)
    {
        $user->setPassword('');
    }

    /**
     * @dataProvider userProvider
     * @param User $user
     */
    public function testGetters(User $user)
    {
        Assert::assertInstanceOf(DateTime::class, $user->getCreatedAt());
        Assert::assertInstanceOf(DateTime::class, $user->getUpdatedAt());
    }
}
