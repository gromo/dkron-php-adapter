<?php

namespace Dkron\Tests;

use Dkron\Api;
use Dkron\Exception\DkronNoAvailableServersException;
use Dkron\Models\{
    Execution, Job, Member, Status
};
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\{
    ConnectException, RequestException
};
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class ApiTest extends TestCase
{
    public function constructorDataProvider()
    {
        $defaults = $this->getHttpClient();
        $defaults->client = null;

        return [
            'success:defaults' => [
                'http' => $defaults,
            ],
            'success:endpointsAsString' => [
                'http' => $this->getHttpClient(null, 'http://192.168.0.1:8080/')
            ],
            'success:endpointsAsArray' => [
                'http' => $this->getHttpClient(null, [
                    'http://192.168.0.1:8080/',
                    'http://localhost/',
                    'https://example.com/'
                ]),
            ],
            'error:endpointsAsNumber' => [
                'http' => $this->getHttpClient(null, 10),
                'exception' => InvalidArgumentException::class,
            ],
            'error:endpointsAsInvalidUrl' => [
                'http' => $this->getHttpClient(null, 'test.com'),
                'exception' => InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * @param mixed $http
     * @param string|null $exception
     *
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($http, string $exception = null)
    {
        if ($exception) {
            $this->expectException($exception);
        }
        $api = new Api($http->endpoints, $http->client);

        // check api was created successfully
        $this->assertInstanceOf(Api::class, $api);
    }

    /**
     * Make sure all servers from the list were called
     */
    public function testAllEndpointsCalled()
    {
        $request = new Request('GET', '');
        $http = $this->getHttpClient([
            new ConnectException('Client Error', $request),
            new ConnectException('Client Error', $request),
            new ConnectException('Client Error', $request),
        ], [
            'http://192.168.0.1/',
            'http://192.168.0.2/',
            'http://192.168.0.3/'
        ]);

        $api = new Api($http->endpoints, $http->client);
        $exceptionHandled = false;

        try {
            $api->getStatus();
        } catch (Exception $exception) {
            $this->assertInstanceOf(DkronNoAvailableServersException::class, $exception);
            $exceptionHandled = true;
        }

        $this->assertTrue($exceptionHandled);
        $this->assertCount(3, $http->transactions);

    }

    public function testMethodDeleteJob()
    {
        $http = $this->getHttpClient();
        $api = new Api($http->endpoints, $http->client);
        $jobName = 'job001';

        $api->deleteJob($jobName);

        $request = $this->getRequest($http);
        $this->assertEquals('/v1/jobs/' . $jobName, $request->getUri()->getPath());
        $this->assertEquals('DELETE', mb_strtoupper($request->getMethod()));
    }

    public function testMethodGetJob()
    {
        $mockData = [
            'name' => 'test:name',
            'schedule' => 'test:schedule',
        ];
        $http = $this->getHttpClient([$mockData]);
        $api = new Api($http->endpoints, $http->client);

        $job = $api->getJob($mockData['name']);

        // check request
        $request = $this->getRequest($http);
        $this->assertEquals('/v1/jobs/' . $mockData['name'], $request->getUri()->getPath());
        $this->assertEquals('GET', mb_strtoupper($request->getMethod()));

        // check result
        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($mockData['name'], $job->getName());
        $this->assertEquals($mockData['schedule'], $job->getSchedule());
    }

    public function testMethodGetJobExecutions()
    {
        $mockData = [
            ['job_name' => 'nameA', 'success' => true],
            ['job_name' => 'nameB', 'success' => false],
        ];
        $http = $this->getHttpClient([$mockData]);
        $api = new Api($http->endpoints, $http->client);
        $jobName = 'job001';

        $executions = $api->getJobExecutions($jobName);

        // check request
        $request = $this->getRequest($http);
        $this->assertEquals('/v1/jobs/' . $jobName . '/executions', $request->getUri()->getPath());
        $this->assertEquals('GET', mb_strtoupper($request->getMethod()));

        // check result
        $this->assertCount(2, $executions);
        foreach ($mockData as $i => $executionData) {
            $execution = $executions[$i];

            $this->assertInstanceOf(Execution::class, $execution);
            $this->assertEquals($executionData['job_name'], $execution->getJobName());
            $this->assertEquals($executionData['success'], $execution->isSuccess());
        }
    }

    public function testMethodGetJobs()
    {
        $mockData = [
            ['name' => 'nameA', 'schedule' => 'scheduleA'],
            ['name' => 'nameB', 'schedule' => 'scheduleB'],
        ];
        $http = $this->getHttpClient([$mockData]);
        $api = new Api($http->endpoints, $http->client);

        $jobs = $api->getJobs();

        // check request
        $request = $this->getRequest($http);
        $this->assertEquals('/v1/jobs', $request->getUri()->getPath());
        $this->assertEquals('GET', mb_strtoupper($request->getMethod()));

        // check result
        $this->assertCount(2, $jobs);
        foreach ($mockData as $i => $jobData) {
            $job = $jobs[$i];
            $this->assertInstanceOf(Job::class, $job);
            $this->assertEquals($jobData['name'], $job->getName());
            $this->assertEquals($jobData['schedule'], $job->getSchedule());
        }
    }

    public function testMethodGetLeader()
    {
        $mockData = [
            'Name' => 'leader:name',
            'Addr' => 'leader:addr',
        ];
        $http = $this->getHttpClient([$mockData]);
        $api = new Api($http->endpoints, $http->client);

        $leader = $api->getLeader();

        // check request
        $request = $this->getRequest($http);
        $this->assertEquals('/v1/leader', $request->getUri()->getPath());
        $this->assertEquals('GET', mb_strtoupper($request->getMethod()));

        // check result
        $this->assertInstanceOf(Member::class, $leader);
        $this->assertEquals($mockData['Name'], $leader->getName());
        $this->assertEquals($mockData['Addr'], $leader->getAddr());
    }

    public function testMethodGetMembers()
    {
        $mockData = [
            ['Name' => 'nameA', 'Addr' => 'addrA'],
            ['Name' => 'nameB', 'Addr' => 'addrB'],
        ];
        $http = $this->getHttpClient([$mockData]);
        $api = new Api($http->endpoints, $http->client);

        $members = $api->getMembers();

        // check request
        $request = $this->getRequest($http);
        $this->assertEquals('/v1/members', $request->getUri()->getPath());
        $this->assertEquals('GET', mb_strtoupper($request->getMethod()));

        // check result
        $this->assertCount(2, $members);
        foreach ($mockData as $i => $mockItemData) {
            $member = $members[$i];

            $this->assertInstanceOf(Member::class, $member);
            $this->assertEquals($mockItemData['Name'], $member->getName());
            $this->assertEquals($mockItemData['Addr'], $member->getAddr());
        }
    }

    public function testMethodGetStatus()
    {
        $mockData = [
            'agent' => [
                'backend' => 'consul',
                'name' => '217f633ff07d',
                'version' => '0.10.0',
            ],
            'serf' => [
                'encrypted' => 'false',
                'event_queue' => '0',
                'event_time' => '1',
                'failed' => '0',
            ],
            'tags' => [
                'dkron_rpc_addr' => '172.21.0.7:6868',
                'dkron_server' => 'true',
                'dkron_version' => '0.10.0',
            ]
        ];
        $http = $this->getHttpClient([$mockData]);
        $api = new Api($http->endpoints, $http->client);

        $status = $api->getStatus();

        // check request
        $request = $this->getRequest($http);
        $this->assertEquals('/v1/', $request->getUri()->getPath());
        $this->assertEquals('GET', mb_strtoupper($request->getMethod()));

        // check result
        $this->assertInstanceOf(Status::class, $status);
        $this->assertEquals($mockData['agent'], $status->getAgent());
        $this->assertEquals($mockData['serf'], $status->getSerf());
        $this->assertEquals($mockData['tags'], $status->getTags());
    }

    public function testMethodLeaveWithOneEndpoint()
    {
        $mockData = [
            ['Name' => 'nameA', 'Addr' => 'addrA'],
            ['Name' => 'nameB', 'Addr' => 'addrB'],
        ];
        $http = $this->getHttpClient([$mockData]);
        $api = new Api($http->endpoints, $http->client);

        $members = $api->leave();

        // check request
        $request = $this->getRequest($http);
        $this->assertEquals('/v1/leave', $request->getUri()->getPath());
        $this->assertEquals('GET', mb_strtoupper($request->getMethod()));

        // check result
        $this->assertCount(2, $members);
        foreach ($mockData as $i => $mockItemData) {
            $member = $members[$i];

            $this->assertInstanceOf(Member::class, $member);
            $this->assertEquals($mockItemData['Name'], $member->getName());
            $this->assertEquals($mockItemData['Addr'], $member->getAddr());
        }
    }

    public function testMethodLeaveWithEmptyEndpoint()
    {
        $mockData = [
            ['Name' => 'nameA', 'Addr' => 'addrA'],
            ['Name' => 'nameB', 'Addr' => 'addrB'],
        ];
        $mockEndpoints = [
            'http://192.168.0.1',
            'http://192.168.0.2',
            'http://192.168.0.3',
        ];
        $http = $this->getHttpClient([$mockData], $mockEndpoints);
        $api = new Api($http->endpoints, $http->client);

        $this->expectException(InvalidArgumentException::class);
        $api->leave();
    }

    public function testMethodLeaveWithSpecificEndpoint()
    {
        $mockData = [
            ['Name' => 'nameA', 'Addr' => 'addrA'],
            ['Name' => 'nameB', 'Addr' => 'addrB'],
        ];
        $mockEndpoints = [
            'http://192.168.0.1',
            'http://192.168.0.2',
            'http://192.168.0.3',
        ];
        $http = $this->getHttpClient([$mockData], $mockEndpoints);
        $api = new Api($http->endpoints, $http->client);

        $members = $api->leave($mockEndpoints[0]);
        $this->assertCount(2, $members);
    }

    public function testMethodRunJob()
    {
        $http = $this->getHttpClient();
        $api = new Api($http->endpoints, $http->client);
        $jobName = 'job001';

        $api->runJob($jobName);

        // check request
        $request = $this->getRequest($http);
        $this->assertEquals('/v1/jobs/' . $jobName, $request->getUri()->getPath());
        $this->assertEquals('POST', mb_strtoupper($request->getMethod()));
    }

    public function testMethodSaveJob()
    {
        $mockData = [
            'name' => 'test:name',
            'schedule' => 'test:schedule',
            'executor' => 'shell',
            'executor_config' => [
                'command' => 'ls -la /tmp',
                'shell' => true
            ],
            'processors' => [
                'log' => [
                    'forward' => true,
                ],
            ],
        ];
        $http = $this->getHttpClient([$mockData]);
        $api = new Api($http->endpoints, $http->client);
        $job = Job::createFromArray($mockData);

        $api->saveJob($job);

        // check request
        $request = $this->getRequest($http);
        $this->assertEquals('/v1/jobs', $request->getUri()->getPath());
        $this->assertEquals('POST', mb_strtoupper($request->getMethod()));
        $requestData = json_decode($request->getBody()->__toString(), true);
        $this->assertArraySubset($mockData, $requestData);
    }


    /**
     * @param array $responses
     * @param mixed $endpoints
     * @return stdClass
     */
    protected function getHttpClient(array $responses = null, $endpoints = 'http://127.0.0.1/'): stdClass
    {
        $output = new stdClass();
        $output->endpoints = $endpoints;
        $output->transactions = [];

        if (is_null($responses)) {
            $responses = [null];
        }
        $responses = array_map(function ($response) {
            return ($response instanceof ResponseInterface) || ($response instanceof RequestException)
                ? $response
                : new Response(200, ['Content-Type: application/json'], json_encode($response));
        }, $responses);

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push(Middleware::history($output->transactions));

        $output->client = new Client([
            'handler' => $handler,
        ]);

        return $output;
    }

    protected function getRequest($http): RequestInterface
    {
        $this->assertCount(1, $http->transactions, 'Request is not available in transactions');
        return $http->transactions[0]['request'];
    }
}
