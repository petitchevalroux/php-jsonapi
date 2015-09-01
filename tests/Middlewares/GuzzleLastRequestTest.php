<?php

use JsonApi\Middlewares\GuzzleLastRequest;

class GuzzleLastRequestTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $request = $this->getMockBuilder('\GuzzleHttp\Psr7\Request')
                ->disableOriginalConstructor()
                ->getMock();
        $middleware = new GuzzleLastRequest();
        $this->assertEquals($request, $middleware($request));
        $this->assertEquals($request, $middleware->getLastRequest());
    }
}
