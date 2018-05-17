<?php

namespace Dkron\Tests\Models;

use Dkron\Models\Job;
use PHPUnit\Framework\TestCase;
use stdClass;

class JobTest extends TestCase
{

    public function testCreateFromArray()
    {
        $mockData = [
            'name' => 'test:name',
            'schedule' => 'test:schedule',
            'concurrency' => Job::CONCURRENCY_FORBID,
            'dependent_jobs' => ['job1', 'job2'],
            'disabled' => true,
            'error_count' => 10,
            'executor' => 'shell',
            'executor_config' => [
                'command' => 'ln -ls /tmp',
                'env' => 'FOO=bar',
                'shell' => true
            ],
            'last_error' => '0001-01-01T00:00:00Z',
            'last_success' => '2018-05-17T09:00:01.150388465Z',
            'owner' => 'owner',
            'owner_email' => 'owner@test.com',
            'parent_job' => 'parent-job',
            'processors' => [
                'log' => [
                    'forward' => true,
                ],
            ],
            'retries' => 2,
            'success_count' => 99,
            'tags' => [
                'role' => 'web'
            ],
            'timezone' => "Europe/London",
        ];
        $job = Job::createFromArray($mockData);

        $this->assertInstanceOf(Job::class, $job);

        foreach ($mockData as $key => $value) {
            $getter = 'get' . str_replace('_', '', ucwords($key, '_'));
            $this->assertEquals($value, $job->$getter());
        }
    }

    public function testGetDataToSubmit()
    {
        $mockData = [
            'name' => 'test:name',
            'schedule' => 'test:schedule',
        ];

        $job = new Job(
            $mockData['name'],
            $mockData['schedule'],
            777,
            'Last Error Date',
            'Last Success Date',
            999
        );
        $job->setExecutorConfig([]);
        $job->setProcessors([]);
        $job->setTags([]);

        $dataToSubmit = $job->getDataToSubmit();

        $requiredFields = [
            'concurrency',
            'dependent_jobs',
            'disabled',
            'executor',
            'executor_config',
            'name',
            'owner',
            'owner_email',
            'parent_job',
            'processors',
            'retries',
            'schedule',
            'tags',
            'timezone',
        ];
        foreach ($requiredFields as $field) {
            $this->assertArrayHasKey($field, $dataToSubmit);
        }

        $readonlyFields = [
            'error_count',
            'last_error',
            'last_success',
            'success_count',
        ];
        foreach ($readonlyFields as $field) {
            $this->assertArrayNotHasKey($field, $dataToSubmit);
        }

        // check values
        foreach ($mockData as $key => $value) {
            $this->assertEquals($value, $dataToSubmit[$key]);
        }

        // check key-value objects
        $this->assertInstanceOf(stdClass::class, $dataToSubmit['executor_config']);
        $this->assertInstanceOf(stdClass::class, $dataToSubmit['processors']);
        $this->assertInstanceOf(stdClass::class, $dataToSubmit['tags']);
    }
}