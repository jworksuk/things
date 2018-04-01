<?php

namespace Things\Application\Http\Controller;

use Things\Application\DataTransformer\Response\ResponseTransformer;
use Things\Application\Service\UserService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class UserController
 * @package Things\Application\Http\Controller
 */
class UserController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var ResponseTransformer
     */
    protected $responseTransformer;

    /**
     * UserController constructor.
     * @param UserService $userService
     * @param ResponseTransformer $responseTransformer
     */
    public function __construct(UserService $userService, ResponseTransformer $responseTransformer)
    {
        $this->userService = $userService;
        $this->responseTransformer = $responseTransformer;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Assert\AssertionFailedException
     * @throws \Things\Domain\Model\User\UserAlreadyExistsException
     */
    public function create(Request $request, Response $response)
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
}
