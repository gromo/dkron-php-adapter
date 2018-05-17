<?php

namespace Dkron\Tests;

use Dkron\Api;
use Dkron\Models\{
    Execution, Job, Member, Status
};
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use stdClass;

class ApiTest extends TestCase
{
    public function testDeleteJob()
    {
        $http = $this->getHttpCient();
        $api = new Api($http->client);
        $jobName = 'job001';

        $api->deleteJob($jobName);

        $request = $this->getRequest($http);
        $this->assertEquals('/v1/jobs/' . $jobName, $request->getUri()->getPath());
        $this->assertEquals('DELETE', mb_strtoupper($request->getMethod()));
    }

    public function testGetJob()
    {
        $mockData = [
            'name' => 'test:name',
            'schedule' => 'test:schedule',
        ];
        $http = $this->getHttpCient($mockData);
        $api = new Api($http->client);

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

    public function testGetJobExecutions()
    {
        $mockData = [
            [
                'job_name' => 'nameA',
                'success' => true,
            ],
            [
                'job_name' => 'nameB',
                'success' => false,
            ],
        ];
        $http = $this->getHttpCient($mockData);
        $api = new Api($http->client);
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

    public function testGetJobs()
    {
        $mockData = [
            [
                'name' => 'nameA',
                'schedule' => 'scheduleA',
            ],
            [
                'name' => 'nameB',
                'schedule' => 'scheduleB',
            ],
        ];
        $http = $this->getHttpCient($mockData);
        $api = new Api($http->client);

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

    public function testGetLeader()
    {
        $mockData = [
            'Name' => 'leader:name',
            'Addr' => 'leader:addr',
        ];
        $http = $this->getHttpCient($mockData);
        $api = new Api($http->client);

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

    public function testGetMembers()
    {
        $mockData = [
            [
                'Name' => 'nameA',
                'Addr' => 'addrA',
            ],
            [
                'Name' => 'nameB',
                'Addr' => 'addrB',
            ],
        ];
        $http = $this->getHttpCient($mockData);
        $api = new Api($http->client);

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

    public function testGetStatus()
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
        $http = $this->getHttpCient($mockData);
        $api = new Api($http->client);

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

    public function testLeave()
    {
        $mockData = [
            [
                'Name' => 'nameA',
                'Addr' => 'addrA',
            ],
            [
                'Name' => 'nameB',
                'Addr' => 'addrB',
            ],
        ];
        $http = $this->getHttpCient($mockData);
        $api = new Api($http->client);

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

    public function testRunJob()
    {
        $http = $this->getHttpCient();
        $api = new Api($http->client);
        $jobName = 'job001';

        $api->runJob($jobName);

        // check request
        $request = $this->getRequest($http);
        $this->assertEquals('/v1/jobs/' . $jobName, $request->getUri()->getPath());
        $this->assertEquals('POST', mb_strtoupper($request->getMethod()));
    }

    public function testSaveJob()
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
        $http = $this->getHttpCient($mockData);
        $api = new Api($http->client);
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
     * @param array $data
     * @return stdClass
     */
    protected function getHttpCient(array $data = null): stdClass
    {
        $output = new stdClass();
        $output->transactions = [];

        $handler = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type: application/json'], json_encode($data)),
        ]));
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
