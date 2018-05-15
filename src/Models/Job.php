<?php

namespace Dkron\Models;

class Job implements \JsonSerializable
{

    const CONCURRENCY_ALLOW = 'allow';
    const CONCURRENCY_FORBID = 'forbid';

    /** @var string */
    private $name;

    /** @var string */
    private $schedule;

    /** @var string */
    private $command;

    /** @var string */
    private $concurrency;

    /** @var array */
    private $dependent_jobs;

    /** @var bool */
    private $disabled;

    /** @var int */
    private $error_count;

    /** @var string */
    private $last_error;

    /** @var string */
    private $last_success;

    /** @var string */
    private $owner;

    /** @var string */
    private $owner_email;

    /** @var string */
    private $parent_job;

    /** @var array */
    private $processors;

    /** @var int */
    private $retries;

    /** @var bool */
    private $shell;

    /** @var int */
    private $success_count;

    /** @var array */
    private $tags;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getConcurrency()
    {
        return $this->concurrency;
    }

    /**
     * @return array
     */
    public function getDependentJobs()
    {
        return $this->dependent_jobs;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * @return int
     */
    public function getErrorCount()
    {
        return $this->error_count;
    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->last_error;
    }

    /**
     * @return string
     */
    public function getLastSuccess()
    {
        return $this->last_success;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getOwnerEmail()
    {
        return $this->owner_email;
    }

    /**
     * @return string
     */
    public function getParentJob()
    {
        return $this->parent_job;
    }

    /**
     * @return array
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * @return int
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * @return bool
     */
    public function isShell()
    {
        return $this->shell;
    }

    /**
     * @return int
     */
    public function getSuccessCount()
    {
        return $this->success_count;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Job constructor.
     * @param string $name
     * @param string $schedule
     * @param string $command
     *
     * @param string $concurrency
     * @param array $dependent_jobs
     * @param bool $disabled
     * @param int $error_count
     * @param string $last_error
     * @param string $last_success
     * @param string $owner
     * @param string $owner_email
     * @param string $parent_job
     * @param array $processors
     * @param int $retries
     * @param bool $shell
     * @param int $success_count
     * @param array $tags
     */
    public function __construct(
        $name,
        $schedule,
        $command,

        $concurrency = self::CONCURRENCY_ALLOW,
        array $dependent_jobs = null,
        $disabled = false,
        $error_count = null,
        $last_error = null,
        $last_success = null,
        $owner = "",
        $owner_email = "",
        $parent_job = "",
        array $processors = null,
        $retries = 0,
        $shell = false,
        $success_count = null,
        array $tags = null
    )
    {
        $this->name = $name;
        $this->schedule = $schedule;
        $this->command = $command;

        $this->concurrency = $concurrency;
        $this->dependent_jobs = $dependent_jobs;
        $this->disabled = $disabled;
        $this->error_count = $error_count;
        $this->last_error = $last_error;
        $this->last_success = $last_success;
        $this->owner = $owner;
        $this->owner_email = $owner_email;
        $this->parent_job = $parent_job;
        $this->processors = $processors;
        $this->retries = $retries;
        $this->shell = $shell;
        $this->success_count = $success_count;
        $this->tags = $tags;

        if ($this->concurrency !== self::CONCURRENCY_ALLOW) {
            $this->disableConcurrency();
        }
    }

    public function disableConcurrency()
    {
        $this->concurrency = self::CONCURRENCY_FORBID;
        return $this;
    }

    public function enableConcurrency()
    {
        $this->concurrency = self::CONCURRENCY_ALLOW;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'schedule' => $this->schedule,
            'command' => $this->command,

            'concurrency' => $this->concurrency,
            'dependent_jobs' => $this->dependent_jobs,
            'disabled' => $this->disabled,
            'error_count' => $this->error_count,
            'last_error' => $this->last_error,
            'last_success' => $this->last_success,
            'owner' => $this->owner,
            'owner_email' => $this->owner_email,
            'parent_job' => $this->parent_job,
            'processors' => $this->processors,
            'retries' => $this->retries,
            'shell' => $this->shell,
            'success_count' => $this->success_count,
            'tags' => $this->tags,
        ];
    }

    /**
     * @param string $schedule
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * @param string $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @param array $dependent_jobs
     */
    public function setDependentJobs($dependent_jobs)
    {
        $this->dependent_jobs = $dependent_jobs;
    }

    /**
     * @param string $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @param string $owner_email
     */
    public function setOwnerEmail($owner_email)
    {
        $this->owner_email = $owner_email;
    }

    /**
     * @param string $parent_job
     */
    public function setParentJob($parent_job)
    {
        $this->parent_job = $parent_job;
    }

    /**
     * @param int $retries
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;
    }

    /**
     * @param bool $shell
     */
    public function setShell($shell)
    {
        $this->shell = $shell;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

}