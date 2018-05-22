<?php

namespace Dkron;

use Dkron\Exception\{
    DkronException, DkronResponseException, DkronNoAvailableServersException
};
use Dkron\Models\{
    Execution, Job, Member, Status
};
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\{
    ConnectException, GuzzleException
};
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;

class Api
{
    const METHOD_DELETE = 'DELETE';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const TIMEOUT = 10;
    const URL_PREFIX = '/v1/';

    /** @var Endpoints */
    private $endpoints;

    /** @var ClientInterface */
    private $httpClient;

    /**
     * Api constructor.
     * @param string|array|Endpoints $endpoints
     * @param ClientInterface $httpClient
     * @throws InvalidArgumentException
     */
    public function __construct($endpoints, ClientInterface $httpClient = null)
    {
        if (!($endpoints instanceof Endpoints)) {
            $endpoints = new Endpoints($endpoints);
        }
        $this->endpoints = $endpoints;

        if (is_null($httpClient)) {
            $httpClient = new Client([
                'timeout' => self::TIMEOUT,
            ]);
        }
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $name
     * @throws DkronException
     */
    public function deleteJob($name)
    {
        $this->request('/jobs/' . $name, self::METHOD_DELETE);
    }

    /**
     * @param string $name
     * @return Job
     * @throws DkronException
     */
    public function getJob(string $name): Job
    {
        return Job::createFromArray($this->request('/jobs/' . $name));
    }

    /**
     * @param $name
     * @return Execution[]
     * @throws DkronException
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
     * @throws DkronException
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
     * @throws DkronException
     */
    public function getLeader(): Member
    {
        return Member::createFromArray($this->request('/leader'));
    }

    /**
     * @return Member[]
     * @throws DkronException
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
     * @throws DkronException
     */
    public function getStatus(): Status
    {
        return Status::createFromArray($this->request('/'));
    }

    /**
     * @param string $endpoint
     * @return Member[]
     * @throws DkronException
     */
    public function leave(string $endpoint = null): array
    {
        if (is_null($endpoint) && $this->endpoints->getSize() === 1) {
            $endpoint = $this->endpoints->getAvailableEndpoint();
        }
        if (is_null($endpoint)) {
            throw new InvalidArgumentException('Parameter endpoint has to be set');
        }

        $members = [];
        $responseData = $this->request('/leave', self::METHOD_GET, null, $endpoint);
        foreach ($responseData as $memberData) {
            $members[] = Member::createFromArray($memberData);
        }

        return $members;
    }

    /**
     * @param string $name
     * @throws DkronException
     */
    public function runJob($name)
    {
        $this->request('/jobs/' . $name, self::METHOD_POST);
    }

    /**
     * @param Job $job
     * @throws DkronException
     */
    public function saveJob(Job $job)
    {
        $this->request('/jobs', self::METHOD_POST, $job->getDataToSubmit());
    }

    /**
     * @param string $url
     * @param string $method
     * @param mixed $data
     * @param array|string|Endpoints $endpoints
     * @return array|null
     * @throws DkronException
     */
    protected function request($url, $method = self::METHOD_GET, $data = null, $endpoints = null)
    {
        if (is_null($endpoints)) {
            $endpoints = $this->endpoints;
        }
        if (!($endpoints instanceof Endpoints)) {
            $endpoints = new Endpoints($endpoints);
        }

        while ($endpoint = $endpoints->getAvailableEndpoint()) {
            try {
                /** @var Response $response */
                $response = $this->httpClient->request($method, $endpoint . self::URL_PREFIX . ltrim($url, '/'), [
                    'json' => $data,
                ]);

                $data = json_decode($response->getBody(), true);
                if (JSON_ERROR_NONE !== json_last_error()) {
                    throw new DkronResponseException('json_decode error: ' . json_last_error_msg());
                }

                return $data;
            } catch (ConnectException $exception) {
                $this->endpoints->setEndpointAsUnavailable($endpoint);
            } catch (DkronException $exception) {
                throw $exception;
            } catch (\Throwable $exception) {
                throw new DkronException($exception->getMessage());
            }
        }
    }

}
