<?php

namespace Things\Application\Http;

/**
 * Class HttpError
 * @package Things\Application\Http
 */
class HttpError
{
    /**
     * @var int|string
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

    public function __construct($code = 500, $message = 'Something went wrong!')
    {
        $this->code = $code;
        $this->message = $message;
    }
}
