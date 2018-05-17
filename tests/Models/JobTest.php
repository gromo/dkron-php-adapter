<?php

namespace Dkron\Tests\Models;

use Dkron\Models\Job;
use PHPUnit\Framework\TestCase;

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
        $job = new Job(
            'test:name',
            'test:schedule',
            'allow',
            [],
            false,
            777,
            null,
            [],
            'Last Error Date',
            'Last Success Date',
            null,
            null,
            null,
            [],
            null,
            999,
            [],
            null
        );

        $dataToSubmit = $job->getDataToSubmit();

        // check required fields
        $this->assertArrayHasKey('name', $dataToSubmit);
        $this->assertArrayHasKey('schedule', $dataToSubmit);
        $this->assertArrayHasKey('concurrency', $dataToSubmit);
        $this->assertArrayHasKey('dependent_jobs', $dataToSubmit);
        $this->assertArrayHasKey('disabled', $dataToSubmit);
        $this->assertArrayHasKey('executor', $dataToSubmit);
        $this->assertArrayHasKey('executor_config', $dataToSubmit);
        $this->assertArrayHasKey('owner', $dataToSubmit);
        $this->assertArrayHasKey('owner_email', $dataToSubmit);
        $this->assertArrayHasKey('parent_job', $dataToSubmit);
        $this->assertArrayHasKey('processors', $dataToSubmit);
        $this->assertArrayHasKey('retries', $dataToSubmit);
        $this->assertArrayHasKey('tags', $dataToSubmit);
        $this->assertArrayHasKey('timezone', $dataToSubmit);

        // check read-only fields are ignored
        $this->assertArrayNotHasKey('error_count', $dataToSubmit);
        $this->assertArrayNotHasKey('last_error', $dataToSubmit);
        $this->assertArrayNotHasKey('last_success', $dataToSubmit);
        $this->assertArrayNotHasKey('success_count', $dataToSubmit);

        // check empty arrays are converted to nulls
        $this->assertNull($dataToSubmit['dependent_jobs']);
        $this->assertNull($dataToSubmit['executor_config']);
        $this->assertNull($dataToSubmit['processors']);
        $this->assertNull($dataToSubmit['tags']);


    }
}