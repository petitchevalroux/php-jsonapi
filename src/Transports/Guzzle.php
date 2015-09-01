<?php

namespace JsonApi\Transports;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use JsonApi\Exception;
use JsonApi\Middlewares\GuzzleLastRequest as GuzzleLastRequestMiddleware;

class Guzzle extends Transport
{

    /**
     * Return Guzzle Client Configuration
     * @return array
     */
    private function getGuzzleClientConfiguration()
    {
        $config = [
            'handler' => $this->getHandlerStack(),
            'base_uri' => $this->getEndPoint(),
            'http_errors' => false,
            'timeout' => $this->getTimeout()
        ];
        return $config;
    }

    /**
     *
     * @var HandlerStack
     */
    private $handlerStack;

    /**
     *
     * @return HandlerStack
     * @throws Exception
     */
    private function getHandlerStack()
    {
        if (!isset($this->handlerStack)) {
            $this->handlerStack = HandlerStack::create();
        }
        return $this->handlerStack;
    }

    /**
     * GuzzleLastRequestMiddleware
     * @var type 
     */
    private $lastRequestMiddleware;


    /**
     * Set GuzzleLastRequestMiddleware instance
     * @param GuzzleLastRequestMiddleware $middleware
     */
    public function setLastRequestMiddleware(GuzzleLastRequestMiddleware $middleware) {
        $this->lastRequestMiddleware = $middleware;
    }

    /**
     * Return GuzzleLastRequestMiddleware instance.
     *
     * @return GuzzleLastRequestMiddleware
     */
    private function getLastRequestMiddleware()
    {
        if (!isset($this->lastRequestMiddleware)) {
            $this->lastRequestMiddleware = new GuzzleLastRequestMiddleware();
        }

        return $this->lastRequestMiddleware;
    }

    /**
     * Guzzle client
     * @var Client
     */
    private $client;

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
    public function getClient()
    {
        if (!isset($this->client)) {
            $config = $this->getGuzzleClientConfiguration();
            $config['handler']->push(Middleware::mapRequest($this->getLastRequestMiddleware()), 'last_request');
            $this->client = new Client($config);
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
        $response = $this->getClient()->patch($uri, [
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
            throw new Exception('Unexpected status code ' . json_encode($this->getDebugInfos($response)));
        }

        return $response->getBody()->__toString();
    }

    public function getDebugInfos(Response $response)
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
