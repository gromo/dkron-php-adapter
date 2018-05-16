<?php

namespace Dkron;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;

use Dkron\Models\{
    Execution, Job, Member, Status
};

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
        return Job::createFromArray($data);
    }

    /**
     * @return array
     */
    public function getJobExecutions($name)
    {
        $executions = [];
        $responseData = $this->request('/jobs/' . $name . '/executions');
        foreach ($responseData as $executionData) {
            $executions[] = Execution::createFromArray($executionData);
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
            $jobs[] = Job::createFromArray($jobData);
        }
        return $jobs;
    }

    /**
     * @return Member
     */
    public function getLeader()
    {
        $data = $this->request('/leader');
        return Member::createFromArray($data);
    }

    /**
     * @return array
     */
    public function getMembers()
    {
        $members = [];
        $responseData = $this->request('/members');
        foreach ($responseData as $memberData) {
            $members[] = Member::createFromArray($memberData);
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
            $members[] = Member::createFromArray($memberData);
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