<?php

namespace Dkron\Models;


class Member implements \JsonSerializable
{
    /** @var string */
    private $name;

    /** @var string */
    private $addr;

    /** @var int */
    private $port;

    /** @var array */
    private $tags;

    /** @var int */
    private $status;

    /** @var int */
    private $protocolMin;

    /** @var int */
    private $protocolMax;

    /** @var int */
    private $protocolCur;

    /** @var int */
    private $delegateMin;

    /** @var int */
    private $delegateMax;

    /** @var int */
    private $delegateCur;

    /**
     * Member constructor.
     * @param string $name
     * @param string $addr
     * @param int $port
     * @param array $tags
     * @param int $status
     * @param int $protocolMin
     * @param int $protocolMax
     * @param int $protocolCur
     * @param int $delegateMin
     * @param int $delegateMax
     * @param int $delegateCur
     */
    public function __construct(
        string $name = null,
        string $addr = null,
        int $port = null,
        array $tags = null,
        int $status = null,
        int $protocolMin = null,
        int $protocolMax = null,
        int $protocolCur = null,
        int $delegateMin = null,
        int $delegateMax = null,
        int $delegateCur = null
    ) {
        $this->name = $name;
        $this->addr = $addr;
        $this->port = $port;
        $this->tags = $tags;
        $this->status = $status;
        $this->protocolMin = $protocolMin;
        $this->protocolMax = $protocolMax;
        $this->protocolCur = $protocolCur;
        $this->delegateMin = $delegateMin;
        $this->delegateMax = $delegateMax;
        $this->delegateCur = $delegateCur;
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
    public function getAddr(): string
    {
        return $this->addr;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getProtocolMin(): int
    {
        return $this->protocolMin;
    }

    /**
     * @return int
     */
    public function getProtocolMax(): int
    {
        return $this->protocolMax;
    }

    /**
     * @return int
     */
    public function getProtocolCur(): int
    {
        return $this->protocolCur;
    }

    /**
     * @return int
     */
    public function getDelegateMin(): int
    {
        return $this->delegateMin;
    }

    /**
     * @return int
     */
    public function getDelegateMax(): int
    {
        return $this->delegateMax;
    }

    /**
     * @return int
     */
    public function getDelegateCur(): int
    {
        return $this->delegateCur;
    }


    public function jsonSerialize()
    {
        return [
            'Name' => $this->name,
            'Addr' => $this->addr,
            'Port' => $this->port,
            'Tags' => $this->tags,
            'Status' => $this->status,
            'ProtocolMin' => $this->protocolMin,
            'ProtocolMax' => $this->protocolMax,
            'ProtocolCur' => $this->protocolCur,
            'DelegateMin' => $this->delegateMin,
            'DelegateMax' => $this->delegateMax,
            'DelegateCur' => $this->delegateCur,
        ];
    }

    public static function createFromArray(array $data)
    {
        return new static(
            $data['Name'] ?? null,
            $data['Addr'] ?? null,
            $data['Port'] ?? null,
            $data['Tags'] ?? null,
            $data['Status'] ?? null,
            $data['ProtocolMin'] ?? null,
            $data['ProtocolMax'] ?? null,
            $data['ProtocolCur'] ?? null,
            $data['DelegateMin'] ?? null,
            $data['DelegateMax'] ?? null,
            $data['DelegateCur'] ?? null
        );
    }
}
