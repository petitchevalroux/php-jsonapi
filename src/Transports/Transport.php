<?php

namespace JsonApi\Transports;

use JsonApi\Exception;

abstract class Transport
{
    const DEFAULT_CONTENT_TYPE = 'application/json';

    /**
     * Default timeout for a request in seconds.
     */
    const DEFAULT_TIMEOUT = 3;

    abstract public function get($uri, $queryParams = []);

    /**
     * @param string $uri
     * @param string $body
     */
    abstract public function post($uri, $body);

    abstract public function patch($uri, $value);

    abstract public function put($uri, $value);

    abstract public function delete($uri);

    /**
     * Base endpoint url.
     *
     * @var string
     */
    private $endPoint;

    /**
     * set transport endpoint.
     *
     * @param string $endPoint
     */
    public function setEndPoint($endPoint)
    {
        $this->endPoint = $endPoint;
    }

    /**
     * Return transport endpoint.
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getEndPoint()
    {
        if (!isset($this->endPoint)) {
            throw new Exception('endPoint not setted');
        }

        return $this->endPoint;
    }
    /**
     * Request timeout in seconds.
     *
     * @var int
     */
    private $timeout;
    /**
     * Set request timeout.
     *
     * @param int $timeout in seconds
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Get request timeout.
     *
     * @return int timeout in seconds
     */
    public function getTimeout()
    {
        if (!isset($this->timeout)) {
            $this->timeout = static::DEFAULT_TIMEOUT;
        }

        return $this->timeout;
    }
}
