<?php

namespace Things\Application\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Things\Domain\Model\User\User;
use Things\Domain\Model\User\UserId;
use Things\Domain\Model\User\UserRepository;
use Things\Domain\Service\PasswordHashing;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UserFromBasicAuthTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function testInvoke()
    {
        $email = 'test@test.com';
        $password = 'password1';

        $mockUserRepository = self::createMock(UserRepository::class);
        $mockUserRepository->method('findByEmail')
            ->willReturn(
                new User(
                    new UserId,
                    $email,
                    '',
                    $password,
                    new \DateTime,
                    new \DateTime
                )
            );

        $mockPasswordHashing = self::createMock(PasswordHashing::class);
        $mockPasswordHashing->method('verify')
            ->willReturn($password);

        $mockRequest = self::createMock(Request::class);
        $mockRequest->method('getServerParams')
            ->willReturn([
                'PHP_AUTH_USER' => $email,
                'PHP_AUTH_PW' => $password
            ]);

        $middleware = $this->createMiddleWare($mockUserRepository, $mockPasswordHashing);
        $middleware(
            $mockRequest,
            self::createMock(Response::class),
            function () {
                return 200;
            }
        );
    }

    /**
     * @expectedException \Exception
     * @throws \Exception
     */
    public function testInvokeCannotFindUser()
    {
        $email = 'test@test.com';
        $password = 'password1';

        $mockUserRepository = self::createMock(UserRepository::class);
        $mockUserRepository->method('findByEmail')
            ->willReturn(null);

        $mockPasswordHashing = self::createMock(PasswordHashing::class);

        $mockRequest = self::createMock(Request::class);
        $mockRequest->method('getServerParams')
            ->willReturn([
                'PHP_AUTH_USER' => $email,
                'PHP_AUTH_PW' => $password
            ]);

        $middleware = $this->createMiddleWare($mockUserRepository, $mockPasswordHashing);
        $middleware(
            $mockRequest,
            self::createMock(Response::class),
            function () {
                return 200;
            }
        );
    }

    /**
     * @expectedException \Exception
     * @throws \Exception
     * @throws \Assert\AssertionFailedException
     */
    public function testInvokeTestPassword()
    {
        $email = 'test@test.com';
        $password = 'password1';

        $_SERVER['PHP_AUTH_USER'] = $email;
        $_SERVER['PHP_AUTH_PW'] = $password;

        $mockUserRepository = self::createMock(UserRepository::class);
        $mockUserRepository->method('findByEmail')
            ->willReturn(
                new User(
                    new UserId,
                    $email,
                    '',
                    $password,
                    new \DateTime,
                    new \DateTime
                )
            );

        $mockPasswordHashing = self::createMock(PasswordHashing::class);
        $mockPasswordHashing->method('calculateHash')
            ->willReturn(rand(0, 99999999));

        $mockRequest = self::createMock(Request::class);
        $mockRequest->method('getServerParams')
            ->willReturn([
                'PHP_AUTH_USER' => $email,
                'PHP_AUTH_PW' => $password
            ]);

        $middleware = $this->createMiddleWare($mockUserRepository, $mockPasswordHashing);
        $middleware(
            $mockRequest,
            self::createMock(Response::class),
            function () {
                return 200;
            }
        );
    }

    /**
     * @param $userRepository
     * @param $passwordHashing
     * @return UserFromBasicAuthMiddleware
     */
    protected function createMiddleWare($userRepository, $passwordHashing)
    {
        return new UserFromBasicAuthMiddleware($userRepository, $passwordHashing);
    }
}
