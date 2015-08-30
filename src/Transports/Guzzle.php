<?php

namespace Jsonapi\Transports;

use Jsonapi\Middlewares\GuzzleLastRequest as GuzzleLastRequestMiddleware;
use Jsonapi\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class Guzzle extends Transport
{
    /**
     * Guzzle http client.
     *
     * @var Client
     */
    private $client = false;

    /**
     * Guzzle middleware to get last request.
     *
     * @var GuzzleLastRequestMiddleware
     */
    private $lastRequestMiddleware = false;

    /**
     * Build client and set its configuration.
     *
     * @param array $config
     */
    public function setClientConfiguration($config)
    {
        if (!isset($config['handler'])) {
            $config['handler'] = HandlerStack::create();
        }
        $config['handler']->push(Middleware::mapRequest($this->getLastRequestMiddleware()));
        $config['http_errors'] = false;
        $client = new Client($config);
        $this->setClient($client);
    }

    /**
     * Return GuzzleLastRequestMiddleware instance.
     *
     * @return GuzzleLastRequestMiddleware
     */
    public function getLastRequestMiddleware()
    {
        if ($this->lastRequestMiddleware === false) {
            $this->lastRequestMiddleware = new GuzzleLastRequestMiddleware();
        }

        return $this->lastRequestMiddleware;
    }

    /**
     * Set Guzzle Client.
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Return current client.
     *
     * @return Client
     *
     * @throws Exception
     */
    private function getClient()
    {
        if ($this->client === false) {
            throw new Exception('Guzzle client not setted properly');
        }

        return $this->client;
    }

    /**
     * Make a get request.
     *
     * @param base url $uri
     * @param array    $queryParams
     */
    public function get($uri, $queryParams = [])
    {
        $response = $this->getClient()->get($uri, ['query' => $queryParams]);

        return $this->getResponse($response, [200 => true]);
    }

    /**
     * Make a post request.
     *
     * @param string $uri
     * @param string $body
     *
     * @return array
     */
    public function post($uri, $body)
    {
        $response = $this->getClient()->post($uri, [
            'headers' => [
                'Content-Type' => static::DEFAULT_CONTENT_TYPE,
            ],
            'body' => $body,
        ]);

        return $this->getResponse($response, [201 => true]);
    }

    /**
     * Make a patch request.
     *
     * @param string $uri
     * @param type   $body
     */
    public function patch($uri, $body)
    {
        $response = $this->getClient()->put($uri, [
            'headers' => [
                'Content-Type' => static::DEFAULT_CONTENT_TYPE,
            ],
            'body' => $body,
        ]);

        return $this->getResponse($response, [200 => true]);
    }

    /**
     * Make a put request.
     *
     * @param string $uri
     * @param string $body
     *
     * @return array
     */
    public function put($uri, $body)
    {
        $response = $this->getClient()->put($uri, [
            'headers' => [
                'Content-Type' => static::DEFAULT_CONTENT_TYPE,
            ],
            'body' => $body,
        ]);

        return $this->getResponse($response, [200 => true]);
    }

    /**
     * Make a delete request.
     *
     * @param string $uri
     */
    public function delete($uri)
    {
        $response = $this->getClient()->delete($uri);
        $this->getResponse($response, [204 => true]);
    }

    /**
     * @param Response $response
     * @param array    $allowedStatusCodes
     *
     * @return string
     *
     * @throws Exception
     */
    private function getResponse(Response $response, $allowedStatusCodes)
    {
        $responseStatus = $response->getStatusCode();
        //echo $this->getDebugInfos($response);
        if (!isset($allowedStatusCodes[$responseStatus])) {
            throw new Exception('Unexpected status code '.json_encode($this->getDebugInfos($response)));
        }

        return $response->getBody()->__toString();
    }

    protected function getDebugInfos(Response $response)
    {
        $lastRequest = $this->getLastRequestMiddleware()->getLastRequest();

        return json_encode([
            'response' => [
                'status' => $response->getStatusCode(),
                'body' => $response->getBody()->__toString(),
                'header' => $response->getHeaders(),
            ],
            'request' => [
                'uri' => $lastRequest->getUri()->__toString(),
                'method' => $lastRequest->getMethod(),
                'body' => $lastRequest->getBody()->__toString(),
                'headers' => $lastRequest->getHeaders(),
            ],
        ]);
    }
}
