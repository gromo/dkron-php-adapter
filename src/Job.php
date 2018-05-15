<?php

namespace Dkron;

class Job extends AbstractObject
{

    /** @var string */
    private $name = '';

//    $data = [
//        "name" => "string",
//        "schedule" => "string",
//        "command" => "string",
//        "shell" => true,
//        "owner" => "string",
//        "owner_email" => "string",
//        //"success_count" => 0,
//        //"error_count" => 0,
//        //"last_success" => "string",
//        //"last_error" => "string",
//        "disabled" => true,
//        "tags" => [
//            //"string" => "string",
//        ],
//        "retries" => 2,
//        "parent_job" => "parent_job",
//        "dependent_jobs" => [
//            "string"
//        ],
//        "processors" => [
//            "string" => "string",
//        ],
//        "concurrency" => "allow"
//    ];

    /**
     * Job constructor.
     * @param array $data
     * @param Api $api
     */
    public function __construct(array $data = [], Api $api)
    {
        parent::__construct($data, $api);

        if (empty($data['name'])) {
            throw new \Exception('Job name is required. It must be unique and cannot be changed');
        }
        $this->name = $data['name'];
    }

    public function delete()
    {
        return $this->api->request([
            'method' => Api::METHOD_DELETE,
            'url' => '/jobs/' . $this->name,
        ]);
    }

    public function disable()
    {
        $this->setState(false);
        return $this;
    }

    public function disableConcurrency()
    {
        $this->data['concurrency'] = 'forbid';
        return $this;
    }

    public function enable()
    {
        $this->setState(true);
        return $this;
    }

    public function enableConcurrency()
    {
        $this->data['concurrency'] = 'allow';
        return $this;
    }

    public function getResults()
    {
        return $this->api->request([
            'url' => '/jobs/' . $this->name . '/executions'
        ]);
    }

    public function isValid()
    {
        $data = $this->data;
        return !empty($data['command'])
            && (!empty($data['schedule']) || !empty($data['parent_job']))
            && (!isset($data['concurrency']) || in_array($data['concurrency'], ['allow', 'forbid']));
    }

    public function run()
    {
        return $this->api->request([
            'method' => Api::METHOD_POST,
            'url' => '/jobs/' . $this->name,
        ]);
    }

    public function save()
    {
        if (!$this->isValid()) {
            throw new \Exception('Dkron job is invalid. Please check parameters');
        }

        return $this->api->request([
            'data' => $this->getSanitizedData(),
            'method' => Api::METHOD_POST,
            'url' => '/jobs',
        ]);
    }

    /**
     * @param string $command
     * @return $this
     */
    public function setCommand(string $command)
    {
        $this->data['command'] = $command;
        return $this;
    }

    /**
     * @param array $jobs
     * @return $this
     */
    public function setDepenentJobs(array $jobs)
    {
        $this->data['dependent_jobs'] = $jobs;
        return $this;
    }

    /**
     * @param string $parentJob
     * @return $this
     */
    public function setParentJob(string $parentJob)
    {
        $this->data['parent_job'] = $parentJob;
        return $this;
    }

    /**
     * @param $schedule
     * @return $this
     */
    public function setSchedule($schedule)
    {
        $this->data['schedule'] = $schedule;
        return $this;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setState(bool $enabled)
    {
        $this->data['disabled'] = !$enabled;
        return $this;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function setTags(array $tags)
    {
        $this->data['tags'] = $tags;
        return $this;
    }

    /**
     * Return only data that can be saved
     * @param array $data
     * @return array
     */
    protected function getSanitizedData(array $data = null)
    {
        if (!$data) {
            $data = $this->data;
        }
        $keys = [
            "command",
            "concurrency",
            "dependent_jobs",
            "disabled",
            "name",
            "owner",
            "owner_email",
            "parent_job",
            "processors",
            "retries",
            "schedule",
            "shell",
            "tags",
        ];
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $keys)) {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }
}