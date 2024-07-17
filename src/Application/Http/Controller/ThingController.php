<?php

namespace Things\Application\Http\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Things\Domain\Model\Thing\ThingId;
use Things\Application\DataTransformer\Response\ResponseTransformer;
use Things\Application\Service\ThingService;

/**
 * Class ThingController
 * @package Things\Application\Http\Controller
 */
class ThingController
{
    /**
     * ThingController constructor.
     * @param ThingService $thingService
     * @param ResponseTransformer $responseTransformer
     */
    public function __construct(
        protected ThingService $thingService,
        protected ResponseTransformer $responseTransformer
    ) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function list(Request $request, Response $response): Response
    {
        return $this->responseTransformer->transform(
            $response,
            $this->thingService->getThingsByUserId(
                $request->getAttribute('user')->getUserId()
            )
        );
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Things\Domain\Model\Thing\ThingCannotBeSavedException
     */
    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        return $this->responseTransformer->transform(
            $response,
            [
                $this->thingService->createThing(
                    $body['name'],
                    ($body['description']) ?? '',
                    $request->getAttribute('user')->getUserId()
                )
            ]
        );
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws \Things\Domain\Model\Thing\ThingCannotBeAccessedByUserException
     * @throws \Things\Domain\Model\Thing\ThingNotFoundException
     */
    public function read(Request $request, Response $response, string $thingId): Response
    {
        return $this->responseTransformer->transform(
            $response,
            [
                $this->thingService->getThingById(
                    new ThingId($thingId),
                    $request->getAttribute('user')->getUserId()
                )
            ]
        );
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws \Things\Domain\Model\Thing\ThingCannotBeAccessedByUserException
     * @throws \Things\Domain\Model\Thing\ThingNotFoundException
     */
    public function update(Request $request, Response $response, string $thingId): Response
    {
        $body = $request->getParsedBody();

        return $this->responseTransformer->transform(
            $response,
            [
                $this->thingService->updateThing(
                    new ThingId($thingId),
                    $request->getAttribute('user')->getUserId(),
                    ($body['name']) ?? null,
                    ($body['description']) ?? null
                )
            ]
        );
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws \Things\Domain\Model\Thing\ThingCannotBeAccessedByUserException
     * @throws \Things\Domain\Model\Thing\ThingNotFoundException
     */
    public function delete(Request $request, Response $response, string $thingId): Response
    {
        return $this->responseTransformer->transform(
            $response,
            [
                $this->thingService->deleteThingById(
                    new ThingId($thingId),
                    $request->getAttribute('user')->getUserId()
                )
            ]
        );
    }
}
