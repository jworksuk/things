<?php

namespace Things\Application\Http\Middleware;

use Things\Application\Exception\HttpException;
use Things\Domain\Model\User\UserRepository;
use Things\Domain\Service\PasswordHashing;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class UserFromBasicAuthMiddleware
{
    /**
     * UserFromBasicAuthMiddleware constructor.
     * @param UserRepository $userRepository
     * @param PasswordHashing $passwordHashing
     */
    public function __construct(protected UserRepository $userRepository, protected PasswordHashing $passwordHashing)
    {
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     * @throws HttpException
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
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

        return $handler->handle($request);
    }
}
