<?php

namespace Jsonapi;

use Transports\Transport as Transport;
use Jsonapi\Transports\Guzzle as Guzzle;

class Jsonapi
{
    /**
     * Default timeout for a request in seconds.
     */
    const DEFAULT_TIMEOUT = 60;

    /**
     * Api End Point.
     *
     * @var string
     */
    private $endPoint = '';

    /**
     * Transport used to make request.
     *
     * @var Transport
     */
    private $transport = false;

    /**
     * Set endPoint.
     *
     * @param string $endpoint
     */
    public function setEndPoint($endpoint)
    {
        $this->endPoint = trim($endpoint, '/').'/';
    }

    /**
     * Return endPoint.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getEndPoint()
    {
        if ($this->endPoint === '') {
            throw new Exception('EndPoint not setted');
        }

        return $this->endPoint;
    }

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
     * Return client Jsonapi\Transport.
     *
     * @return Transport
     */
    public function getTransport()
    {
        // Set default transport
        if ($this->transport === false) {
            $this->transport = new Guzzle();
            $this->transport->setClientConfiguration([
                'base_uri' => $this->getEndPoint(),
                'timeout' => static::DEFAULT_TIMEOUT,
            ]);
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
     *
     * @param string $uri
     *
     * @return array
     */
    public function getResources($uri)
    {
        $transport = $this->getTransport();
        $response = $transport->get($uri);

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
}
