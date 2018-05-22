<?php

namespace Dkron\Tests;

use Dkron\Endpoints;
use Dkron\Exception\DkronNoAvailableServersException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EndpointsTest extends TestCase
{
    public function constructorDataProvider()
    {
        return [
            'success:endpointsAsString' => [
                'endpoints' => 'http://192.168.0.1:8080/',
            ],
            'success:endpointsAsArray' => [
                'endpoints' => [
                    'http://192.168.0.1:8080/',
                    'https://example.com/',
                    'http://localhost',
                    'http://localhost/',
                    'http://localhost/test/1234?abc=123',
                ],
            ],
            'error:endpointsAsNumber' => [
                'endpoints' => 10,
                'exception' => InvalidArgumentException::class,
            ],
            'error:endpointsAsInvalidUrl' => [
                'endpoints' => 'test.com',
                'exception' => InvalidArgumentException::class,
            ],
            'error:endpointsAsArray' => [
                'endpoints' => ['http://192.168.0.1:8080', 'http://localhost', 'test.com'],
                'exception' => InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * @param mixed $endpoints
     * @param string|null $exception
     *
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($endpoints, string $exception = null)
    {
        if ($exception) {
            $this->expectException($exception);
        }
        $instance = new Endpoints($endpoints);

        if (!is_array($endpoints)) {
            $endpoints = [$endpoints];
        }
        foreach ($endpoints as $endpoint) {
            $this->assertTrue($instance->hasEndpoint($endpoint));
        }
    }

    public function testMethodGetAvailableEndpoint()
    {
        $endpoints = [
            'http://192.168.0.1/',
            'http://192.168.0.2/',
            'http://192.168.0.3/',
        ];
        $instance = new Endpoints($endpoints);

        $availableEndpoints = [];
        for ($i = 0; $i < 9; $i++) {
            $availableEndpoint = $instance->getAvailableEndpoint();
            if (!in_array($availableEndpoint, $availableEndpoints)) {
                $availableEndpoints[] = $availableEndpoint;
            } else {
                $this->assertEquals($availableEndpoints[$i % 3], $availableEndpoint);
            }
        }

        // handle exception if no endpoints available
        foreach ($endpoints as $endpoint) {
            $instance->setEndpointAsUnavailable($endpoint);
        }
        $this->expectException(DkronNoAvailableServersException::class);
        $instance->getAvailableEndpoint();
    }

    public function testMethodGetAvailableEndpoints()
    {
        $endpoints = [
            'http://192.168.0.1:8080/',
            'https://example.com/',
            'http://localhost',
            'http://localhost/',
            'http://localhost/test/1234?abc=123',
        ];
        $instance = new Endpoints($endpoints);

        $expectedEndpoints = [
            'http://192.168.0.1:8080',
            'http://localhost',
            'https://example.com',
        ];
        for ($i = 0; $i < 3; $i++) {
            $availableEndpoints = $instance->getAvailableEndpoints();
            sort($availableEndpoints);

            $this->assertEquals($expectedEndpoints, $availableEndpoints);
            $instance->setEndpointAsUnavailable(array_shift($expectedEndpoints));
        }

        $availableEndpoints = $instance->getAvailableEndpoints();
        $this->assertCount(0, $availableEndpoints);
    }

    public function testBooleanEndpointMethods()
    {
        $endpoints = [
            'http://192.168.0.1/',
            'http://192.168.0.2/',
        ];
        $instance = new Endpoints($endpoints);

        foreach ($endpoints as $endpoint) {
            $this->assertTrue($instance->hasEndpoint($endpoint));
            $this->assertTrue($instance->isEndpointAvailable($endpoint));
            $instance->setEndpointAsUnavailable($endpoint);
            $this->assertFalse($instance->isEndpointAvailable($endpoint));
        }
    }
}
