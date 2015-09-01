<?php

namespace JsonApi\Middlewares;

use Psr\Http\Message\RequestInterface;

class GuzzleLastRequest
{
    /**
     * Last request.
     *
     * @var RequestInterface
     */
    private $lastRequest;

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    public function __invoke(RequestInterface $request)
    {
        $this->lastRequest = $request;

        return $request;
    }

    /**
     * @return RequestInterface
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }
}
