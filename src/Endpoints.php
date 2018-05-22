<?php

namespace Dkron;

use Dkron\Exception\DkronNoAvailableServersException;
use InvalidArgumentException;

class Endpoints
{
    /** @var array */
    private $endpoints = [];

    /** @var int */
    private $offset = 0;

    /**
     * @param string|array $endpoints
     * @throws InvalidArgumentException
     */
    public function __construct($endpoints)
    {
        if (!is_string($endpoints) && !is_array($endpoints)) {
            throw new InvalidArgumentException('Parameter endpoints has to be string or array');
        }
        if (is_string($endpoints)) {
            $endpoints = [$endpoints];
        }
        if (count($endpoints) === 0) {
            throw new InvalidArgumentException('Parameter endpoints cannot be empty');
        }

        // remove duplicates
        $endpoints = array_map(function ($endpoint) {
            return $this->sanitize($endpoint);
        }, $endpoints);
        $endpoints = array_unique($endpoints);

        shuffle($endpoints);

        foreach ($endpoints as $endpoint) {
            $this->endpoints[] = [
                'available' => true,
                'url' => $endpoint,
            ];
        }
    }

    /**
     * @return string
     * @throws DkronNoAvailableServersException
     */
    public function getAvailableEndpoint(): string
    {
        $availableEndpoints = $this->getAvailableEndpoints();
        $length = count($availableEndpoints);

        if ($length === 0) {
            throw new DkronNoAvailableServersException();
        }
        if ($this->offset >= $length) {
            $this->offset = 0;
        }
        $endpoint = $availableEndpoints[$this->offset];
        $this->offset = $this->offset + 1;

        return $endpoint;
    }

    /**
     * @return array[string]
     */
    public function getAvailableEndpoints(): array
    {
        $availableEndpoints = array_values(array_filter($this->endpoints, function ($endpoint) {
            return $endpoint['available'];
        }));
        $availableEndpointsAsStrings = array_map(function ($endpoint) {
            return $endpoint['url'];
        }, $availableEndpoints);

        return $availableEndpointsAsStrings;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return count($this->endpoints);
    }

    /**
     * @param string $endpoint
     * @return bool
     */
    public function hasEndpoint(string $endpoint): bool
    {
        $url = $this->sanitize($endpoint);
        foreach ($this->endpoints as $i => $endpoint) {
            if ($endpoint['url'] === $url) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $endpoint
     * @return bool
     */
    public function isEndpointAvailable(string $endpoint): bool
    {
        $url = $this->sanitize($endpoint);
        foreach ($this->endpoints as $i => $endpoint) {
            if ($endpoint['url'] === $url) {
                return $endpoint['available'];
            }
        }
        return false;
    }

    /**
     * @param string $endpoint
     * @throws InvalidArgumentException
     */
    public function setEndpointAsUnavailable(string $endpoint)
    {
        $url = $this->sanitize($endpoint);
        foreach ($this->endpoints as $i => $endpoint) {
            if ($endpoint['url'] === $url) {
                $this->endpoints[$i]['available'] = false;
                return;
            }
        }
        throw new InvalidArgumentException('Endpoint ' . $endpoint . ' not found');
    }

    /**
     * @param string $endpoint
     * @return string
     * @throws InvalidArgumentException
     */
    protected function sanitize(string $endpoint): string
    {
        if (filter_var($endpoint, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Endpoint ' . $endpoint . ' has to be a valid URL');
        }
        $url = parse_url($endpoint);
        $endpoint = $url['scheme'] . '://' . $url['host'];
        if (isset($url['port'])) {
            $endpoint .= ':' . $url['port'];
        }

        return strtolower($endpoint);
    }
}
