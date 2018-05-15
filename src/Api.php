<?php

namespace Dkron;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;

use Dkron\Models\Execution;
use Dkron\Models\Job;
use Dkron\Models\Member;
use Dkron\Models\Status;

class Api
{
    const METHOD_DELETE = 'DELETE';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const URL_PREFIX = '/v1/';

    /** @var ClientInterface */
    private $httpClient;

    /**
     * Api constructor.
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $name
     */
    public function deleteJob($name)
    {
        $this->request('/jobs/' . $name, self::METHOD_DELETE);
    }

    /**
     * @param string $name
     * @return Job
     * @throws \Exception
     */
    public function getJob(string $name)
    {
        $data = $this->request('/jobs/' . $name);
        return $this->createJobFromData($data);
    }

    /**
     * @return array
     */
    public function getJobExecutions($name)
    {
        $executions = [];
        $responseData = $this->request('/jobs/' . $this->name . '/executions');
        foreach ($responseData as $executionData) {
            $executions[] = $this->createExecutionFromData($executionData);
        }
        return $executions;
    }

    /**
     * @return array
     */
    public function getJobs()
    {
        $jobs = [];
        $responseData = $this->request('/jobs');
        foreach ($responseData as $jobData) {
            $jobs[] = $this->createJobFromData($jobData);
        }
        return $jobs;
    }

    /**
     * @return Member
     */
    public function getLeader()
    {
        $data = $this->request('/leader');
        return $this->createMemberFromData($data);
    }

    /**
     * @return array
     */
    public function getMembers()
    {
        $members = [];
        $responseData = $this->request('/members');
        foreach ($responseData as $memberData) {
            $members[] = $this->createMemberFromData($memberData);
        }
        return $members;
    }

    /**
     * @return Status
     */
    public function getStatus()
    {
        $data = $this->request('/');
        return new Status(
            $data['agent'],
            $data['serf'],
            $data['tags']
        );
    }

    public function leave()
    {
        $members = [];
        $responseData = $this->request('/leave');
        foreach ($responseData as $memberData) {
            $members[] = $this->createMemberFromData($memberData);
        }
        return $members;
    }

    /**
     * @param string $name
     */
    public function runJob($name)
    {
        $this->request('/jobs/' . $name, self::METHOD_POST);
    }

    /**
     * @param Job $job
     * @return array|null
     */
    public function saveJob(Job $job)
    {
        return $this->request('/jobs', self::METHOD_POST, $job);
    }


    protected function createExecutionFromData(array $data)
    {
        return new Execution(
            $data['job_name'],
            $data['started_at'],
            $data['finished_at'],
            $data['success'],
            $data['output'],
            $data['node_name']
        );
    }

    /**
     * @param array $data
     * @return Job
     */
    protected function createJobFromData(array $data)
    {
        return new Job(
            $data['name'],
            $data['schedule'],
            $data['command'],
            $data['concurrency'],
            $data['dependent_jobs'],
            $data['disabled'],
            $data['error_count'],
            $data['last_error'],
            $data['last_success'],
            $data['owner'],
            $data['owner_email'],
            $data['parent_job'],
            $data['processors'],
            $data['retries'],
            $data['shell'],
            $data['success_count'],
            $data['tags']
        );
    }

    protected function createMemberFromData(array $data)
    {
        return new Member(
            $data['Name'],
            $data['Addr'],
            $data['Port'],
            $data['Tags'],
            $data['Status'],
            $data['ProtocolMin'],
            $data['ProtocolMax'],
            $data['ProtocolCur'],
            $data['DelegateMin'],
            $data['DelegateMax'],
            $data['DelegateCur']
        );
    }

    /**
     * @param string $url
     * @param string $method
     * @param mixed $data
     * @return array|null
     */
    protected function request($url, $method = self::METHOD_GET, $data = null)
    {
        /** @var Response $response */
        $response = $this->httpClient->request($method, self::URL_PREFIX . ltrim($url, '/'), [
            'json' => $data,
        ]);
        return json_decode($response->getBody(), true);
    }
}