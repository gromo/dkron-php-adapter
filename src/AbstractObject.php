<?php

namespace Dkron;


abstract class AbstractObject implements \JsonSerializable
{
    /** @var Api */
    protected $api;

    /** @var array */
    protected $data = [];

    /**
     * AbstractObject constructor.
     *
     * @param array $data
     * @param Api $api
     */
    public function __construct(array $data = [], Api $api)
    {
        $this->api = $api;
        $this->data = $data;
    }

    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}