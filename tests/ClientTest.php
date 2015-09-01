<?php

use Faker\Factory;
use JsonApi\Client;
use JsonApi\Transports\Transport;

class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    private $faker;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->faker = Factory::create();
    }

    public function testGetResource()
    {
        $expectedObject = [
            'dummyText' => $this->faker->text(),
            'dummyNumber' => $this->faker->randomDigit(),
        ];

        $transport = $this->getMockBuilder('\JsonApi\Transports\Guzzle')
                ->setMethods(['get'])
                ->getMock();

        $transport->expects($this->once())
                ->method('get')->willReturn(json_encode($expectedObject));

        $client = new Client();
        $client->setTransport($transport);
        $this->assertEquals($expectedObject, $client->getResource($this->faker->url()));
    }

    public function testGetResources()
    {
        $expectedObject = [
            'dummyText' => $this->faker->text(),
            'dummyNumber' => $this->faker->randomDigit(),
        ];

        $transport = $this->getMockBuilder('\JsonApi\Transports\Guzzle')
                ->setMethods(['get'])
                ->getMock();

        $transport->expects($this->once())
                ->method('get')->willReturn(json_encode($expectedObject));

        $client = new Client();
        $client->setTransport($transport);
        $this->assertEquals($expectedObject, $client->getResources($this->faker->url()));
    }

    public function testCreateResource()
    {
        $expectedObject = [
            'dummyText' => $this->faker->text(),
            'dummyNumber' => $this->faker->randomDigit(),
        ];

        $transport = $this->getMockBuilder('\JsonApi\Transports\Guzzle')
                ->setMethods(['post'])
                ->getMock();

        $transport->expects($this->once())
                ->method('post')->willReturn(json_encode($expectedObject));

        $client = new Client();
        $client->setTransport($transport);
        $this->assertEquals(
                $expectedObject, $client->createResource(
                        $this->faker->url(), $expectedObject
                )
        );
    }

    public function testUpdateResource()
    {
        $expectedObject = [
            'dummyText' => $this->faker->text(),
            'dummyNumber' => $this->faker->randomDigit(),
        ];

        $transport = $this->getMockBuilder('\JsonApi\Transports\Guzzle')
                ->setMethods(['put'])
                ->getMock();

        $transport->expects($this->once())
                ->method('put')->willReturn(json_encode($expectedObject));

        $client = new Client();
        $client->setTransport($transport);
        $this->assertEquals(
                $expectedObject, $client->updateResource(
                        $this->faker->url(), $expectedObject
                )
        );
    }

    public function testDeleteResource()
    {
        $expectedObject = [
            'dummyText' => $this->faker->text(),
            'dummyNumber' => $this->faker->randomDigit(),
        ];

        $transport = $this->getMockBuilder('\JsonApi\Transports\Guzzle')
                ->setMethods(['delete'])
                ->getMock();

        $transport->expects($this->once())
                ->method('delete')->willReturn(json_encode($expectedObject));

        $client = new Client();
        $client->setTransport($transport);
        $client->deleteResource($this->faker->url());
    }

    public function testGetTransportDefault()
    {
        $client = new Client();
        $client->setEndPoint($this->faker->url());
        $this->assertTrue($client->getTransport() instanceof Transport);
    }

    public function testGetEndPointException()
    {
        $client = new Client();
        $this->setExpectedException('\JsonApi\Exception', 'endPoint not setted');
        $client->getTransport();
    }

    public function testGetDefaultTimeout()
    {
        $client = new Client();
        $this->assertEquals(Client::DEFAULT_TIMEOUT, $client->getTimeout());
    }

    public function testGetTimeout()
    {
        $client = new Client();
        $expectedTimeout = $this->faker->randomDigit();
        $client->setTimeout($expectedTimeout);
        $this->assertEquals($expectedTimeout, $client->getTimeout());
    }
}
