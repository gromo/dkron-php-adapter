<?php
/**
 * Dkron API: https://dkron.io/usage/api/
 *
 */

namespace Dkron;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;

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
     * @return Job
     * @throws \Exception
     */
    public function getJob(string $name)
    {
        $data = $this->request([
            'url' => '/jobs/' . $name
        ]);
        return new Job($data, $this);
    }

    /**
     * @return array of Job
     * @throws \Exception
     */
    public function getJobs()
    {
        $jobs = $this->request([
            'url' => '/jobs',
        ]);
        foreach ($jobs as $i => $data) {
            $jobs[$i] = new Job($data, $this);
        }
        return $jobs;
    }


    /**
     * @return Member
     * @throws \Exception
     */
    public function getLeader()
    {
        $data = $this->request([
            'url' => '/leader',
        ]);
        $member = new Member($data, $this);
        return $member;
    }

    /**
     * @return array of Member
     * @throws \Exception
     */
    public function getMembers()
    {
        $members = $this->request([
            'url' => '/members',
        ]);
        foreach ($members as $i => $data) {
            $members[$i] = new Member($data, $this);
        }
        return $members;

    }

    public function getStatus()
    {
        return $this->request([
            'url' => '/',
        ]);
    }

    /**
     * Force the node to leave the cluster.
     */
    public function leave()
    {
        return $this->request([
            'url' => '/leave',
        ]);
    }

    /**
     * @param array $data
     * @return Job
     */
    public function newJob(array $data)
    {
        return new Job($data, $this);
    }

    /**
     * @param array $options
     * @return array|null
     */
    public function request(array $options)
    {
        $options = array_merge([
            'data' => null,
            'method' => self::METHOD_GET,
            'url' => '/',
        ], $options);

        /** @var Response $response */
        $response = $this->httpClient->request($options['method'], self::URL_PREFIX . ltrim($options['url'], '/'), [
            'json' => $options['data'],
        ]);
        return json_decode($response->getBody(), true);
    }
}