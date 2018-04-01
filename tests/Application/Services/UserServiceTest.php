<?php

namespace Things\Application\Service;

use Things\Application\Service\UserService;
use Things\Domain\Model\User\UserAlreadyExistsException;
use Things\Domain\Model\User\UserId;
use Things\Mock\Repository\InMemoryUserRepository;
use Things\Infrastructure\Domain\Service\Md5PasswordHashing;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * Class UserServiceTest
 * @package Things\Application\Service
 */
class UserServiceTest extends TestCase
{
    /**
     * @var UserService
     */
    protected $userService;

    public function setUp()
    {
        $this->userService = new class(
            new InMemoryUserRepository,
            new Md5PasswordHashing(getenv('APP_SALT'))
        ) extends UserService{
            public function getUserRepository()
            {
                return $this->userRepository;
            }
        };
    }

    public function testCreate()
    {
        $response = $this->userService->createUser('test@test.com', 12345678, '');
        Assert::assertTrue(isset($response['id']));
    }

    /**
     * @expectedException \Things\Domain\Model\User\UserAlreadyExistsException
     */
    public function testUserAlreadyExistsException()
    {
        $this->userService->createUser('test@test.com', 12345678, '');
        $this->userService->createUser('test@test.com', 12345678, '');
    }

    public function testGetUserById()
    {
        $response = $this->userService->getUserById($this->userService->getUserRepository()->oldUserId);
        Assert::assertTrue(isset($response['id']));
    }

    /**
     * @expectedException  \Things\Domain\Model\User\UserNotFoundException
     */
    public function testUserDoesNotExistsException()
    {
        $response = $this->userService->getUserById(new UserId('1234'));
    }
}