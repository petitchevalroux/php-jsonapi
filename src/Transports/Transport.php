<?php

namespace Jsonapi\Transports;

abstract class Transport
{
    const DEFAULT_CONTENT_TYPE = 'application/json';

    abstract public function get($uri, $queryParams = []);

    /**
     * @param string $uri
     * @param string $body
     */
    abstract public function post($uri, $body);

    abstract public function patch($uri, $value);

    abstract public function put($uri, $value);

    abstract public function delete($uri);
}
