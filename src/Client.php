<?php

namespace JsonApi;

use JsonApi\Transports\Guzzle;
use JsonApi\Transports\Transport as Transport;

class Client
{
    /**
     * Default timeout for a request in seconds.
     */
    const DEFAULT_TIMEOUT = 3;

    /**
     * Client transport layer.
     *
     * @var Transport
     */
    private $transport;

    /**
     * Set transport layer.
     *
     * @param Transport $transport
     */
    public function setTransport(Transport $transport)
    {
        $this->transport = $transport;
    }

    /**
     * Return client Transport.
     *
     * @return Transport
     */
    public function getTransport()
    {
        // Set default transport
        if (isset($this->transport) === false) {
            $this->transport = new Guzzle();
            $this->transport->setEndPoint($this->getEndPoint());
            $this->transport->setTimeout($this->getTimeout());
        }

        return $this->transport;
    }

    /**
     * Return resource identified by $uri.
     *
     * @param string $uri
     *
     * @return array
     */
    public function getResource($uri)
    {
        $transport = $this->getTransport();
        $response = $transport->get($uri);

        return $this->getResponse($response);
    }

    /**
     * Create a resource and return created resource.
     *
     * @param string         $uri
     * @param {array|object} $resource
     *
     * @return array
     */
    public function createResource($uri, $resource)
    {
        $transport = $this->getTransport();
        $response = $transport->post($uri, json_encode($resource));

        return $this->getResponse($response);
    }


    /**
     * Return resources identified by $url.
     * @param string $uri
     * @param array $params
     * @return array
     */
    public function getResources($uri, $params = [])
    {
        $transport = $this->getTransport();
        $response = $transport->get($uri, $params);
        return $this->getResponse($response);
    }

    /**
     * Create a resource and return updated resource.
     *
     * @param string         $uri
     * @param {array|object} $resource
     *
     * @return array
     */
    public function updateResource($uri, $resource)
    {
        $transport = $this->getTransport();
        $response = $transport->put($uri, json_encode($resource));

        return $this->getResponse($response);
    }

    /**
     * Delete a resource.
     *
     * @param string         $uri
     * @param {array|object} $resource
     */
    public function deleteResource($uri)
    {
        $transport = $this->getTransport();
        $transport->delete($uri);
    }

    /**
     * Format a response.
     *
     * @param string $response
     *
     * @return array
     */
    private function getResponse($response)
    {
        return json_decode($response, true);
    }

    /**
     * API EndPoint.
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

    /**
     * Set request timeout in seconds.
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }
}
