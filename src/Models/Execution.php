<?php

namespace Dkron\Models;

class Execution implements \JsonSerializable
{
    /** @var string */
    private $jobName;

    /** @var string */
    private $startedAt;

    /** @var string */
    private $finishedAt;

    /** @var bool */
    private $success;

    /** @var string */
    private $output;

    /** @var string */
    private $nodeName;

    /**
     * Execution constructor.
     * @param string $jobName
     * @param string $startedAt
     * @param string $finishedAt
     * @param bool $success
     * @param string $output
     * @param string $nodeName
     */
    public function __construct(
        string $jobName = null,
        string $startedAt = null,
        string $finishedAt = null,
        bool $success = null,
        string $output = null,
        string $nodeName = null
    ) {
        $this->jobName = $jobName;
        $this->startedAt = $startedAt;
        $this->finishedAt = $finishedAt;
        $this->success = $success;
        $this->output = $output;
        $this->nodeName = $nodeName;
    }

    /**
     * @return string
     */
    public function getJobName(): string
    {
        return $this->jobName;
    }

    /**
     * @return string
     */
    public function getStartedAt(): string
    {
        return $this->startedAt;
    }

    /**
     * @return string
     */
    public function getFinishedAt(): string
    {
        return $this->finishedAt;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getNodeName(): string
    {
        return $this->nodeName;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function jsonSerialize()
    {
        return [
            'job_name' => $this->jobName,
            'started_at' => $this->startedAt,
            'finished_at' => $this->finishedAt,
            'success' => $this->success,
            'output' => $this->output,
            'node_name' => $this->nodeName,
        ];
    }

    /**
     * @param array $data
     * @return Execution
     */
    public static function createFromArray(array $data): self
    {
        return new static(
            $data['job_name'] ?? null,
            $data['started_at'] ?? null,
            $data['finished_at'] ?? null,
            $data['success'] ?? null,
            $data['output'] ?? null,
            $data['node_name'] ?? null
        );
    }
}
