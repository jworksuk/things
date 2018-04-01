<?php

namespace Things\Application\Http\Middleware;

use Things\Application\Exception\HttpException;
use Things\Domain\Model\User\UserRepository;
use Things\Domain\Service\PasswordHashing;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UserFromBasicAuthMiddleware
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var PasswordHashing
     */
    protected $passwordHashing;

    /**
     * UserFromBasicAuthMiddleware constructor.
     * @param UserRepository $userRepository
     * @param PasswordHashing $passwordHashing
     */
    public function __construct(UserRepository $userRepository, PasswordHashing $passwordHashing)
    {
        $this->userRepository = $userRepository;
        $this->passwordHashing = $passwordHashing;
    }

    /**
     * Example middleware invokable class
     *
     * @param  Request $request PSR7 request
     * @param  Response $response PSR7 response
     * @param  callable $next Next middleware
     * @return Response
     * @throws HttpException
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $severParams = $request->getServerParams();
        $email = ($severParams['PHP_AUTH_USER']) ?? '';
        $password = ($severParams['PHP_AUTH_PW']) ?? '';

        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            throw new HttpException('401');
        }

        if (!$this->passwordHashing->verify($password, $user->getPassword())) {
            throw new HttpException('401');
        }

        $request = $request->withAttribute('user', $user);

        $response = $next($request, $response);

        return $response;
    }
}
