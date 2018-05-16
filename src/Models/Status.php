<?php

namespace Dkron\Models;

class Status implements \JsonSerializable
{
    /** @var array */
    private $agent;

    /** @var array */
    private $serf;

    /** @var array */
    private $tags;

    /**
     * Status constructor.
     * @param array $agent
     * @param array $serf
     * @param array $tags
     */
    public function __construct(
        array $agent,
        array $serf,
        array $tags
    ) {
        $this->agent = $agent;
        $this->serf = $serf;
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function getAgent(): array
    {
        return $this->agent;
    }

    /**
     * @return array
     */
    public function getSerf(): array
    {
        return $this->serf;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }


    public function jsonSerialize()
    {
        return [
            'agent' => $this->agent,
            'serf' => $this->serf,
            'tags' => $this->tags,
        ];
    }

    /**
     * @param array $data
     * @return Status
     */
    public static function createFromArray(array $data): self
    {
        return new static(
            $data['agent'] ?? null,
            $data['serf'] ?? null,
            $data['tags'] ?? null
        );
    }
}