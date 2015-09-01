<?php

use Faker\Factory;
use GuzzleHttp\Client;
use JsonApi\Transports\Guzzle;

class GuzzleTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Factory
     */
    private $faker;

    function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->faker = Factory::create();
    }

    public function testEndPointException()
    {
        $transport = new Guzzle();
        $this->setExpectedException(
                'JsonApi\Exception', 'endPoint not setted'
        );
        $transport->get($this->faker->url());
    }

    public function testGetClient()
    {
        $transport = new Guzzle();
        $transport->setEndPoint($this->faker->url());
        $client = $transport->getClient();
        $this->assertTrue($client instanceof Client);
    }

    public function testSetClient()
    {
        $transport = new Guzzle();
        $transport->setEndPoint($this->faker->url());
        $client = new Client();
        $transport->setClient($client);
        $this->assertEquals($client, $transport->getClient());
    }

    public function testGet()
    {
        $expectedBody = $this->faker->text;
        $expectedUrl = $this->faker->url();
        $transport = $this->getTransportForResponse(
                'get', $expectedUrl, 200, $expectedBody
        );

        $this->assertEquals(
                $expectedBody, $transport->get($expectedUrl)
        );
    }

    public function testGetException()
    {
        $expectedBody = $this->faker->text;
        $expectedUrl = $this->faker->url();
        $transport = $this->getTransportForResponse(
                'get', $expectedUrl, 404, $expectedBody
        );
        $this->setExpectedException('JsonApi\Exception');
        $transport->get($expectedUrl);
    }

    public function testPost()
    {
        $expectedBody = $this->faker->text;
        $postedBody = $this->faker->text;
        $expectedUrl = $this->faker->url();
        $transport = $this->getTransportForResponse(
                'post', $expectedUrl, 201, $expectedBody
        );

        $this->assertEquals(
                $expectedBody, $transport->post($expectedUrl, $postedBody)
        );
    }

    public function testPostException()
    {
        $expectedBody = $this->faker->text;
        $postedBody = $this->faker->text;
        $expectedUrl = $this->faker->url();
        $transport = $this->getTransportForResponse(
                'post', $expectedUrl, 404, $expectedBody
        );
        $this->setExpectedException('JsonApi\Exception');
        $transport->post($expectedUrl, $postedBody);
    }

    public function testPatch()
    {
        $expectedBody = $this->faker->text;
        $postedBody = $this->faker->text;
        $expectedUrl = $this->faker->url();
        $transport = $this->getTransportForResponse(
                'patch', $expectedUrl, 200, $expectedBody
        );

        $this->assertEquals(
                $expectedBody, $transport->patch($expectedUrl, $postedBody)
        );
    }

    public function testPatchException()
    {
        $expectedBody = $this->faker->text;
        $postedBody = $this->faker->text;
        $expectedUrl = $this->faker->url();
        $transport = $this->getTransportForResponse(
                'patch', $expectedUrl, 404, $expectedBody
        );
        $this->setExpectedException('JsonApi\Exception');
        $transport->patch($expectedUrl, $postedBody);
    }

    public function testPut()
    {
        $expectedBody = $this->faker->text;
        $postedBody = $this->faker->text;
        $expectedUrl = $this->faker->url();
        $transport = $this->getTransportForResponse(
                'put', $expectedUrl, 200, $expectedBody
        );

        $this->assertEquals(
                $expectedBody, $transport->put($expectedUrl, $postedBody)
        );
    }

    public function testPutException()
    {
        $expectedBody = $this->faker->text;
        $postedBody = $this->faker->text;
        $expectedUrl = $this->faker->url();
        $transport = $this->getTransportForResponse(
                'put', $expectedUrl, 404, $expectedBody
        );
        $this->setExpectedException('JsonApi\Exception');
        $transport->put($expectedUrl, $postedBody);
    }

    public function testDelete()
    {
        $expectedBody = $this->faker->text;
        $expectedUrl = $this->faker->url();
        $transport = $this->getTransportForResponse(
                'delete', $expectedUrl, 204, $expectedBody
        );

        $this->assertEquals(
                null, $transport->delete($expectedUrl)
        );
    }

    public function testDeleteException()
    {
        $expectedBody = $this->faker->text;
        $postedBody = $this->faker->text;
        $expectedUrl = $this->faker->url();
        $transport = $this->getTransportForResponse(
                'delete', $expectedUrl, 404, $expectedBody
        );
        $this->setExpectedException('JsonApi\Exception');
        $transport->delete($expectedUrl);
    }

    public function getTransportForResponse($method, $expectedUri, $expectedStatus, $expectedBody)
    {

        $stream = $this->getMockBuilder('\GuzzleHttp\Psr7\Stream')
                ->disableOriginalConstructor()
                ->setMethods(['__toString'])
                ->getMock();
        $stream->expects($this->atLeastOnce())
                ->method('__toString')
                ->willReturn($expectedBody);

        $response = $this->getMockBuilder('\GuzzleHttp\Psr7\Response')
                ->setMethods(['getStatusCode', 'getBody'])
                ->getMock();
        $response->expects($this->atLeastOnce())
                ->method('getStatusCode')
                ->willReturn($expectedStatus);
        $response->expects($this->atLeastOnce())
                ->method('getBody')
                ->willReturn($stream);

        $uri = $this->getMockBuilder('\GuzzleHttp\Psr7\Uri')
                ->setMethods(['__toString'])
                ->getMock();
        $uri->expects($this->any())
                ->method('__toString')->willReturn($expectedUri);

        $lastRequest = $this->getMockBuilder('\GuzzleHttp\Psr7\Request')
                ->disableOriginalConstructor()
                ->setMethods(['getUri'])
                ->getMock();
        $lastRequest->expects($this->any())
                ->method('getUri')
                ->willReturn($uri);

        $middleware = $this->getMockBuilder('\JsonApi\Middlewares\GuzzleLastRequest')
                ->setMethods(['getLastRequest'])
                ->getMock();
        $middleware->expects($this->any())
                ->method('getLastRequest')
                ->willReturn($lastRequest);

        $client = $this->getMockBuilder('\GuzzleHttp\Client')
                ->setMethods([$method, 'getLastRequestMiddleware'])
                ->getMock();
        $client->expects($this->once())
                ->method($method)
                ->willReturn($response);

        $transport = new Guzzle();
        $transport->setLastRequestMiddleware($middleware);
        $transport->setClient($client);

        return $transport;
    }

}
