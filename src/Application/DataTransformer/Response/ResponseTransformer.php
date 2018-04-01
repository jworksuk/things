<?php

namespace Things\Application\DataTransformer\Response;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Interface ResponseTransformer
 * @package Things\Application\DataTransformer\Response
 */
interface ResponseTransformer
{
    /**
     * @param Response $response
     * @param array $data
     * @param array $errors
     * @return Response
     */
    public function transform(Response $response, array $data = [], array $errors = []);
}
