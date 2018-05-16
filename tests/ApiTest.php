<?php

namespace Dkron\Tests;

use Dkron\Api;
use Dkron\Models\{
    Execution, Job, Member, Status
};
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testGetJob()
    {
        $data = [
            'name' => 'test:name',
            'schedule' => 'test:schedule',
        ];
        $api = new Api($this->getHttpCientWithResponse($data));

        $job = $api->getJob($data['name']);

        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($data['name'], $job->getName());
        $this->assertEquals($data['schedule'], $job->getSchedule());
    }

    public function testGetJobs()
    {
        $data = [
            [
                'name' => 'nameA',
                'schedule' => 'scheduleA',
            ],
            [
                'name' => 'nameB',
                'schedule' => 'scheduleB',
            ],
        ];
        $api = new Api($this->getHttpCientWithResponse($data));

        $jobs = $api->getJobs();

        $this->assertCount(2, $jobs);
        foreach ($data as $i => $jobData) {
            $job = $jobs[$i];
            $this->assertInstanceOf(Job::class, $job);
            $this->assertEquals($jobData['name'], $job->getName());
            $this->assertEquals($jobData['schedule'], $job->getSchedule());
        }
    }

    public function testGetJobExecutions()
    {
        $data = [
            [
                'job_name' => 'nameA',
                'success' => true,
            ],
            [
                'job_name' => 'nameB',
                'success' => false,
            ],
        ];
        $api = new Api($this->getHttpCientWithResponse($data));

        $executions = $api->getJobExecutions('job:name');

        $this->assertCount(2, $executions);
        foreach ($data as $i => $executionData) {
            $execution = $executions[$i];

            $this->assertInstanceOf(Execution::class, $execution);
            $this->assertEquals($executionData['job_name'], $execution->getJobName());
            $this->assertEquals($executionData['success'], $execution->isSuccess());
        }
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
        $api = new Api($this->getHttpCientWithResponse($mockData));

        $response = $api->getMembers();

        $this->assertCount(2, $response);
        foreach ($mockData as $i => $mockItemData) {
            $item = $response[$i];

            $this->assertInstanceOf(Member::class, $item);
            $this->assertEquals($mockItemData['Name'], $item->getName());
            $this->assertEquals($mockItemData['Addr'], $item->getAddr());
        }
    }

    /**
     * @param array $data
     * @return Client
     */
    protected function getHttpCientWithResponse(array $data): Client
    {
        $mockHandler = new MockHandler([
            new Response(200, ['Content-Type: application/json'], json_encode($data)),
        ]);

        return new Client([
            'handler' => HandlerStack::create($mockHandler),
        ]);
    }
}