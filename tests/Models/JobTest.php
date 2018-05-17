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
        ];
        $job = Job::createFromArray($mockData);

        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($mockData['name'], $job->getName());
        $this->assertEquals($mockData['schedule'], $job->getSchedule());
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

    /**
     * @param string $value
     * @param string $exception
     *
     * @dataProvider setConcurrencyDataProvider
     */
    public function testSetConcurrency($value, $exception = null)
    {
        $job = new Job('name', 'schedule');

        if ($exception) {
            $this->expectException($exception);
        }
        $job->setConcurrency($value);
        $this->assertEquals($value, $job->getConcurrency());
    }

    public function setConcurrencyDataProvider()
    {
        return [
            'success:allow' => [
                'value' => Job::CONCURRENCY_ALLOW,
                'exception' => null,
            ],
            'success:forbid' => [
                'value' => Job::CONCURRENCY_FORBID,
                'exception' => null,
            ],
            'error:empty' => [
                'value' => '',
                'exception' => \InvalidArgumentException::class,
            ],
            'error:invalid' => [
                'value' => 'invalid',
                'exception' => \InvalidArgumentException::class,
            ],
        ];
    }
}
