<?php

namespace Dkron\Models;


class Member implements \JsonSerializable
{
    /** @var string */
    private $Name;

    /** @var string */
    private $Addr;

    /** @var int */
    private $Port;

    /** @var array */
    private $Tags;

    /** @var int */
    private $Status;

    /** @var int */
    private $ProtocolMin;

    /** @var int */
    private $ProtocolMax;

    /** @var int */
    private $ProtocolCur;

    /** @var int */
    private $DelegateMin;

    /** @var int */
    private $DelegateMax;

    /** @var int */
    private $DelegateCur;

    /**
     * Member constructor.
     * @param string $Name
     * @param string $Addr
     * @param int $Port
     * @param array $Tags
     * @param int $Status
     * @param int $ProtocolMin
     * @param int $ProtocolMax
     * @param int $ProtocolCur
     * @param int $DelegateMin
     * @param int $DelegateMax
     * @param int $DelegateCur
     */
    public function __construct(
        $Name,
        $Addr,
        $Port,
        array $Tags,
        $Status,
        $ProtocolMin,
        $ProtocolMax,
        $ProtocolCur,
        $DelegateMin,
        $DelegateMax,
        $DelegateCur
    )
    {
        $this->Name = $Name;
        $this->Addr = $Addr;
        $this->Port = $Port;
        $this->Tags = $Tags;
        $this->Status = $Status;
        $this->ProtocolMin = $ProtocolMin;
        $this->ProtocolMax = $ProtocolMax;
        $this->ProtocolCur = $ProtocolCur;
        $this->DelegateMin = $DelegateMin;
        $this->DelegateMax = $DelegateMax;
        $this->DelegateCur = $DelegateCur;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * @return string
     */
    public function getAddr()
    {
        return $this->Addr;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->Port;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->Tags;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->Status;
    }

    /**
     * @return int
     */
    public function getProtocolMin()
    {
        return $this->ProtocolMin;
    }

    /**
     * @return int
     */
    public function getProtocolMax()
    {
        return $this->ProtocolMax;
    }

    /**
     * @return int
     */
    public function getProtocolCur()
    {
        return $this->ProtocolCur;
    }

    /**
     * @return int
     */
    public function getDelegateMin()
    {
        return $this->DelegateMin;
    }

    /**
     * @return int
     */
    public function getDelegateMax()
    {
        return $this->DelegateMax;
    }

    /**
     * @return int
     */
    public function getDelegateCur()
    {
        return $this->DelegateCur;
    }

    public function jsonSerialize()
    {
        return [
            'Name' => $this->Name,
            'Addr' => $this->Addr,
            'Port' => $this->Port,
            'Tags' => $this->Tags,
            'Status' => $this->Status,
            'ProtocolMin' => $this->ProtocolMin,
            'ProtocolMax' => $this->ProtocolMax,
            'ProtocolCur' => $this->ProtocolCur,
            'DelegateMin' => $this->DelegateMin,
            'DelegateMax' => $this->DelegateMax,
            'DelegateCur' => $this->DelegateCur,
        ];
    }

    public static function createFromArray(array $data)
    {
        $data = array_merge([
            'Name' => null,
            'Addr' => null,
            'Port' => null,
            'Tags' => null,
            'Status' => null,
            'ProtocolMin' => null,
            'ProtocolMax' => null,
            'ProtocolCur' => null,
            'DelegateMin' => null,
            'DelegateMax' => null,
            'DelegateCur' => null,
        ], $data);

        return new static(
            $data['Name'],
            $data['Addr'],
            $data['Port'],
            $data['Tags'],
            $data['Status'],
            $data['ProtocolMin'],
            $data['ProtocolMax'],
            $data['ProtocolCur'],
            $data['DelegateMin'],
            $data['DelegateMax'],
            $data['DelegateCur']
        );
    }
}