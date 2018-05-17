<?php

namespace Dkron\Models;

class Job implements \JsonSerializable
{

    const CONCURRENCY_ALLOW = 'allow';
    const CONCURRENCY_FORBID = 'forbid';

    /** @var string */
    private $concurrency = self::CONCURRENCY_ALLOW;

    /** @var array */
    private $dependentJobs;

    /** @var bool */
    private $disabled = false;

    /** @var string */
    private $executor;

    /** @var array */
    private $executorConfig;

    /** @var int */
    private $errorCount;

    /** @var string */
    private $lastError;

    /** @var string */
    private $lastSuccess;

    /** @var string */
    private $name;

    /** @var string */
    private $owner;

    /** @var string */
    private $ownerEmail;

    /** @var string */
    private $parentJob;

    /** @var array */
    private $processors;

    /** @var int */
    private $retries;

    /** @var string */
    private $schedule;

    /** @var int */
    private $successCount;

    /** @var array */
    private $tags;

    /** @var string */
    private $timezone;

    /**
     * Job constructor.
     * @param string $name
     * @param string $schedule
     * @param string $concurrency
     * @param array $dependentJobs
     * @param bool $disabled
     * @param int $errorCount
     * @param string $executor
     * @param array $executorConfig
     * @param string $lastError
     * @param string $lastSuccess
     * @param string $owner
     * @param string $ownerEmail
     * @param string $parentJob
     * @param array $processors
     * @param int $retries
     * @param int $successCount
     * @param array $tags
     * @param string $timezone
     */
    public function __construct(
        string $name,
        string $schedule,
        string $concurrency = null,
        array $dependentJobs = null,
        bool $disabled = false,
        int $errorCount = null,
        string $executor = null,
        array $executorConfig = null,
        string $lastError = null,
        string $lastSuccess = null,
        string $owner = null,
        string $ownerEmail = null,
        string $parentJob = null,
        array $processors = null,
        int $retries = null,
        int $successCount = null,
        array $tags = null,
        string $timezone = null
    ) {

        $this->name = $name;

        // read-only parameters
        $this->errorCount = $errorCount;
        $this->lastError = $lastError;
        $this->lastSuccess = $lastSuccess;
        $this->successCount = $successCount;

        // pre-process && validate data before set
        $this->setConcurrency($concurrency);
        $this->setDependentJobs($dependentJobs);
        $this->setDisabled($disabled);
        $this->setExecutor($executor);
        $this->setExecutorConfig($executorConfig);
        $this->setOwner($owner);
        $this->setOwnerEmail($ownerEmail);
        $this->setParentJob($parentJob);
        $this->setProcessors($processors);
        $this->setRetries($retries);
        $this->setSchedule($schedule);
        $this->setTags($tags);
        $this->setTimezone($timezone);
    }

    public function disableConcurrency(): self
    {
        return $this->setConcurrency(self::CONCURRENCY_FORBID);
    }

    /**
     * @return array
     */
    public function getDataToSubmit(): array
    {
        return [
            'name' => $this->name,
            'schedule' => $this->schedule,
            'concurrency' => $this->concurrency,
            'dependent_jobs' => empty($this->dependentJobs) ? null : $this->dependentJobs,
            'disabled' => $this->disabled,
            'executor' => $this->executor,
            'executor_config' => empty($this->executorConfig) ? null : $this->executorConfig,
            'owner' => $this->owner,
            'owner_email' => $this->ownerEmail,
            'parent_job' => $this->parentJob,
            'processors' => empty($this->processors) ? null : $this->processors,
            'retries' => $this->retries,
            'tags' => empty($this->tags) ? null : $this->tags,
            'timezone' => $this->timezone,
        ];
    }

    public function enableConcurrency(): self
    {
        return $this->setConcurrency(self::CONCURRENCY_ALLOW);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSchedule(): string
    {
        return $this->schedule;
    }

    /**
     * @return string
     */
    public function getConcurrency(): string
    {
        return $this->concurrency;
    }

    /**
     * @return array
     */
    public function getDependentJobs(): array
    {
        return $this->dependentJobs;
    }

    /**
     * @return int
     */
    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    /**
     * @return string
     */
    public function getExecutor(): string
    {
        return $this->executor;
    }

    /**
     * @return array
     */
    public function getExecutorConfig(): array
    {
        return $this->executorConfig;
    }

    /**
     * @return string
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * @return string
     */
    public function getLastSuccess(): string
    {
        return $this->lastSuccess;
    }

    /**
     * @return string
     */
    public function getOwner(): string
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getOwnerEmail(): string
    {
        return $this->ownerEmail;
    }

    /**
     * @return string
     */
    public function getParentJob(): string
    {
        return $this->parentJob;
    }

    /**
     * @return array
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    /**
     * @return int
     */
    public function getRetries(): int
    {
        return $this->retries;
    }

    /**
     * @return int
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'schedule' => $this->schedule,
            'concurrency' => $this->concurrency,
            'dependent_jobs' => $this->dependentJobs,
            'disabled' => $this->disabled,
            'error_count' => $this->errorCount,
            'executor' => $this->executor,
            'executor_config' => $this->executorConfig,
            'last_error' => $this->lastError,
            'last_success' => $this->lastSuccess,
            'owner' => $this->owner,
            'owner_email' => $this->ownerEmail,
            'parent_job' => $this->parentJob,
            'processors' => $this->processors,
            'retries' => $this->retries,
            'success_count' => $this->successCount,
            'tags' => $this->tags,
            'timezone' => $this->timezone,
        ];
    }

    /**
     * Set concurrency
     *  If null, use default value 'allow'
     *
     * @param string $concurrency
     * @return $this
     */
    public function setConcurrency(string $concurrency = null): self
    {
        if (!is_null($concurrency)) {
            if (!in_array($concurrency, [self::CONCURRENCY_ALLOW, self::CONCURRENCY_FORBID], true)) {
                throw new \InvalidArgumentException('Concurrency value is incorrect. Allowed values are '
                    . self::CONCURRENCY_ALLOW . ' or ' . self::CONCURRENCY_FORBID);
            }
            $this->concurrency = $concurrency;
        }

        return $this;
    }

    /**
     * @param array $dependentJobs
     * @return $this
     */
    public function setDependentJobs(array $dependentJobs = null): self
    {
        if (!is_null($dependentJobs)) {
            $this->dependentJobs = $dependentJobs;
        }
        return $this;
    }

    /**
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled): self
    {
        $this->disabled = (bool)$disabled;
        return $this;
    }

    /**
     * @param string $executor
     * @return $this
     */
    public function setExecutor(string $executor = null)
    {
        $this->executor = $executor;
        return $this;
    }

    /**
     * @param array $executorConfig
     * @return $this
     */
    public function setExecutorConfig(array $executorConfig = null)
    {
        $this->executorConfig = $executorConfig;
        return $this;
    }

    /**
     * @param string $owner
     * @return $this
     */
    public function setOwner($owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @param string $ownerEmail
     * @return $this
     */
    public function setOwnerEmail($ownerEmail): self
    {
        $this->ownerEmail = $ownerEmail;
        return $this;
    }

    /**
     * @param string $parentJob
     * @return $this
     */
    public function setParentJob($parentJob): self
    {
        $this->parentJob = $parentJob;
        return $this;
    }

    /**
     * @param array $processors
     * @return $this
     */
    public function setProcessors(array $processors = null): self
    {
        $this->processors = $processors;
        return $this;
    }

    /**
     * @param int $retries
     * @return $this
     */
    public function setRetries(int $retries = null): self
    {
        if (is_null($retries)) {
            $retries = 0;
        }
        $this->retries = $retries;
        return $this;
    }

    /**
     * @param string $schedule
     * @return $this
     */
    public function setSchedule($schedule): self
    {
        $this->schedule = $schedule;
        return $this;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function setTags(array $tags = null): self
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @param string|null $timezone
     * @return $this
     */
    public function setTimezone(string $timezone = null)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @param array $data
     * @return Job
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['name'] ?? null,
            $data['schedule'] ?? null,
            $data['concurrency'] ?? null,
            $data['dependent_jobs'] ?? null,
            $data['disabled'] ?? false,
            $data['error_count'] ?? 0,
            $data['executor'] ?? null,
            $data['executor_config'] ?? null,
            $data['last_error'] ?? null,
            $data['last_success'] ?? null,
            $data['owner'] ?? null,
            $data['owner_email'] ?? null,
            $data['parent_job'] ?? null,
            $data['processors'] ?? null,
            $data['retries'] ?? 0,
            $data['success_count'] ?? 0,
            $data['tags'] ?? null,
            $data['timezone'] ?? null
        );
    }

}