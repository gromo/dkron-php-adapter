<?php

namespace Dkron\Models;

class Execution implements \JsonSerializable
{
    /** @var string */
    private $job_name;

    /** @var string */
    private $started_at;

    /** @var string */
    private $finished_at;

    /** @var bool */
    private $success;

    /** @var string */
    private $output;

    /** @var string */
    private $node_name;

    /**
     * Execution constructor.
     * @param string $job_name
     * @param string $started_at
     * @param string $finished_at
     * @param bool $success
     * @param string $output
     * @param string $node_name
     */
    public function __construct(
        $job_name,
        $started_at,
        $finished_at,
        $success,
        $output,
        $node_name
    )
    {
        $this->job_name = $job_name;
        $this->started_at = $started_at;
        $this->finished_at = $finished_at;
        $this->success = $success;
        $this->output = $output;
        $this->node_name = $node_name;
    }

    /**
     * @return string
     */
    public function getJobName()
    {
        return $this->job_name;
    }

    /**
     * @return string
     */
    public function getStartedAt()
    {
        return $this->started_at;
    }

    /**
     * @return string
     */
    public function getFinishedAt()
    {
        return $this->finished_at;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getNodeName()
    {
        return $this->node_name;
    }

    public function jsonSerialize()
    {
        return [
            'job_name' => $this->job_name,
            'started_at' => $this->started_at,
            'finished_at' => $this->finished_at,
            'success' => $this->success,
            'output' => $this->output,
            'node_name' => $this->node_name,
        ];
    }

    public static function createFromArray(array $data)
    {
        $data = array_merge([
            'job_name' => null,
            'started_at' => null,
            'finished_at' => null,
            'success' => null,
            'output' => null,
            'node_name' => null,
        ], $data);

        return new static(
            $data['job_name'],
            $data['started_at'],
            $data['finished_at'],
            $data['success'],
            $data['output'],
            $data['node_name']
        );
    }
}