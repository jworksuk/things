<?php

namespace Things\Application\DataTransformer\Response;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Interface ResponseTransformer
 * @package Things\Application\DataTransformer\Response
 */
class JsonResponseTransformer implements ResponseTransformer
{
    const CONTENT_TYPE = 'application/json;charset=utf-8';

    /**
     * @param Response $response
     * @param array $data
     * @param array $errors
     * @return Response
     */
    public function transform(Response $response, array $data = [], array $errors = []): Response
    {
        $jsonResponse = $response->withHeader('Content-type', self::CONTENT_TYPE);

        $body = $jsonResponse->getBody();
        $body->write(json_encode([
            'data' => $data,
            'errors' => $errors
        ]));

        return $jsonResponse;
    }
}
