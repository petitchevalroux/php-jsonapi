<?php

namespace JsonApi;

use JsonApi\Transports\Transport as Transport;

class Client
{

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
            throw new Exception('Transport not setted');
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
