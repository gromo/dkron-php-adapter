<?php

namespace Dkron;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
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
     * @throws GuzzleException
     */
    public function deleteJob($name)
    {
        $this->request('/jobs/' . $name, self::METHOD_DELETE);
    }

    /**
     * @param string $name
     * @return Job
     * @throws GuzzleException
     */
    public function getJob(string $name): Job
    {
        return Job::createFromArray($this->request('/jobs/' . $name));
    }

    /**
     * @param $name
     * @return Execution[]
     * @throws GuzzleException
     */
    public function getJobExecutions($name): array
    {
        $executions = [];
        $responseData = $this->request('/jobs/' . $name . '/executions');
        foreach ($responseData as $executionData) {
            $executions[] = Execution::createFromArray($executionData);
        }

        return $executions;
    }

    /**
     * @return Job[]
     * @throws GuzzleException
     */
    public function getJobs(): array
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
     * @throws GuzzleException
     */
    public function getLeader(): Member
    {
        return Member::createFromArray($this->request('/leader'));
    }

    /**
     * @return Member[]
     * @throws GuzzleException
     */
    public function getMembers(): array
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
     * @throws GuzzleException
     */
    public function getStatus(): Status
    {
        return Status::createFromArray($this->request('/'));
    }

    /**
     * @return Member[]
     * @throws GuzzleException
     */
    public function leave(): array
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
     * @throws GuzzleException
     */
    public function runJob($name)
    {
        $this->request('/jobs/' . $name, self::METHOD_POST);
    }

    /**
     * @param Job $job
     * @throws GuzzleException
     */
    public function saveJob(Job $job)
    {
        $this->request('/jobs', self::METHOD_POST, $job->getDataToSubmit());
    }

    /**
     * @param string $url
     * @param string $method
     * @param mixed $data
     * @return array|null
     * @throws GuzzleException
     */
    protected function request($url, $method = self::METHOD_GET, $data = null)
    {
        /** @var Response $response */
        $response = $this->httpClient->request(
            $method,
            self::URL_PREFIX . ltrim($url, '/'),
            [
                'json' => $data,
            ]
        );

        return json_decode($response->getBody(), true);
    }
}
