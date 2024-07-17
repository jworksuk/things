<?php

namespace Things\Application\Http\Controller;

use Assert\AssertionFailedException;
use Things\Application\DataTransformer\Response\ResponseTransformer;
use Things\Application\Service\UserService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Things\Domain\Model\User\UserAlreadyExistsException;
use Things\Domain\Model\User\UserId;
use Things\Domain\Model\User\UserNotFoundException;

/**
 * Class UserController
 * @package Things\Application\Http\Controller
 */
class UserController
{
    /**
     * UserController constructor.
     * @param UserService $userService
     * @param ResponseTransformer $responseTransformer
     */
    public function __construct(protected UserService $userService, protected ResponseTransformer $responseTransformer)
    {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws AssertionFailedException
     * @throws UserAlreadyExistsException
     */
    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        return $this->responseTransformer->transform(
            $response,
            [
                $this->userService->createUser(
                    $body['email'],
                    $body['password']
                )
            ]
        );
    }

    /**
     * @throws UserNotFoundException
     */
    public function show(Request $request, Response $response, $userId): Response
    {
        return $this->responseTransformer->transform(
            $response,
            [
                $this->userService->getUserById(new UserId($userId))
            ]
        );
    }
}
