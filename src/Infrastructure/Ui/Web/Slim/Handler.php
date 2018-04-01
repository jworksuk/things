<?php

namespace Things\Infrastructure\Ui\Web\Slim;

use Bugsnag\Client;
use Bugsnag\Report;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Things\Application\DataTransformer\Response\ResponseTransformer;
use Exception;
use Things\Application\Exception\HttpException;
use Things\Domain\Model\User\User;

/**
 * Class Handler
 * @package Things\Infrastructure\Ui\Web\Slim
 */
class Handler
{
    /**
     * @var ResponseTransformer
     */
    protected $responseTransformer;

    /**
     * @var Client
     */
    protected $bugsnagClient;

    public function __construct(ResponseTransformer $responseTransformer, Client $bugsnagClient)
    {
        $this->responseTransformer = $responseTransformer;
        $this->bugsnagClient = $bugsnagClient;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array|Exception $args
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $code = 500;
        $message = 'Something went wrong!';

        if ($args instanceof HttpException) {
            $code = $args->getCode();
            $message = $args->getMessage();
            $this->bugsnagClient->notifyException($args);
        } elseif ($args instanceof Exception) {
            $this->bugsnagClient->notifyException($args);
        }

        return $this->responseTransformer->transform(
            $response->withStatus($code),
            [],
            [
                [
                    'code' => $code,
                    'message' => $message
                ]
            ]
        );
    }

    /**
     * @param Request $request
     * @return User|null
     */
    protected function addUserToBugsnag(Request $request)
    {
        $user = $request->getAttribute('user');

        if ($user instanceof User) {
            $this->bugsnagClient->registerCallback(function (Report $report) use ($user) {
                $report->setUser([
                    'id' => $user->getUserId()->getId(),
                    'name' => 'Leeroy Jenkins',
                    'email' => 'leeeeroy@jenkins.com',
                ]);
            });
            return $user;
        }

        return null;
    }
}
