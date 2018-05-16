<?php

namespace Dkron\Models;

/**
 * Class Job
 * @package Dkron\Models
 *
 *
 * TODO:
 *  convert empty arrays ($dependent_jobs, $processors, $tags) to null before saving
 */
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

        //$this->concurrency = $concurrency;
        //$this->dependent_jobs = $dependent_jobs;
        //$this->disabled = $disabled;
        $this->error_count = $error_count;
        $this->last_error = $last_error;
        $this->last_success = $last_success;
        $this->owner = $owner;
        $this->owner_email = $owner_email;
        $this->parent_job = $parent_job;
        //$this->processors = $processors;
        $this->retries = $retries;
        //$this->shell = $shell;
        $this->success_count = $success_count;
        //$this->tags = $tags;

        // pre-process && validate data before set
        $this->setConcurrency($concurrency);
        $this->setDependentJobs($dependent_jobs);
        $this->setDisabled($disabled);
        $this->setProcessors($processors);
        $this->setShell($shell);
        $this->setTags($tags);


    }

    public function disableConcurrency()
    {
        return $this->setConcurrency(self::CONCURRENCY_FORBID);
    }

    public function enableConcurrency()
    {
        return $this->setConcurrency(self::CONCURRENCY_ALLOW);
    }

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
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * @return bool
     */
    public function isShell()
    {
        return $this->shell;
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
        return $this;
    }

    /**
     * @param string $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @param string $concurrency
     */
    public function setConcurrency($concurrency)
    {
        if (!in_array($concurrency, [self::CONCURRENCY_ALLOW, self::CONCURRENCY_FORBID], true)) {
            throw new \InvalidArgumentException('Concurrency value is incorrect. Allowed values are '
                . self::CONCURRENCY_ALLOW . ' or ' . self::CONCURRENCY_FORBID);
        }
        $this->concurrency = $concurrency;
        return $this;
    }

    /**
     * @param array $dependent_jobs
     */
    public function setDependentJobs($dependent_jobs)
    {
        if (empty($dependent_jobs)) {
            $dependent_jobs = null;
        } else if (!is_array($dependent_jobs)) {
            throw new \InvalidArgumentException('DependendJobs has to be array or null');
        }
        $this->dependent_jobs = $dependent_jobs;
        return $this;
    }

    /**
     * @param bool $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (bool)$disabled;
        return $this;
    }

    /**
     * @param string $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @param string $owner_email
     */
    public function setOwnerEmail($owner_email)
    {
        $this->owner_email = $owner_email;
        return $this;
    }

    /**
     * @param string $parent_job
     */
    public function setParentJob($parent_job)
    {
        $this->parent_job = $parent_job;
        return $this;
    }

    /**
     * @param array $tags
     */
    public function setProcessors($processors)
    {
        if (empty($processors)) {
            $processors = null;
        } else if (!is_array($processors)) {
            throw new \InvalidArgumentException('Processors has to be array or null');
        }
        $this->processors = $processors;
        return $this;
    }

    /**
     * @param int $retries
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;
        return $this;
    }

    /**
     * @param bool $shell
     */
    public function setShell($shell)
    {
        $this->shell = (bool)$shell;
        return $this;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        if (empty($tags)) {
            $tags = null;
        } else if (!is_array($tags)) {
            throw new \InvalidArgumentException('Tags has to be array or null');
        }
        $this->tags = $tags;
        return $this;
    }


    public static function createFromArray(array $data)
    {
        $data = array_merge([
            'name' => null,
            'schedule' => null,
            'command' => null,
            'concurrency' => self::CONCURRENCY_ALLOW,
            'dependent_jobs' => null,
            'disabled' => false,
            'error_count' => 0,
            'last_error' => null,
            'last_success' => null,
            'owner' => null,
            'owner_email' => null,
            'parent_job' => null,
            'processors' => null,
            'retries' => 0,
            'shell' => false,
            'success_count' => 0,
            'tags' => null,
        ], $data);

        return new static(
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

}