<?php

namespace Frontegg\HttpClient;

/**
 * Class FronteggCurl
 *
 * Abstraction for the procedural curl elements so that curl can be mocked and the implementation can be tested.
 *
 * @package Frontegg
 */
class FronteggCurl
{

    /**
     * @var resource Curl resource instance
     */
    protected $curl;

    /**
     * Make a new curl reference instance
     *
     * @return void
     */
    public function init(): void
    {
        $this->curl = curl_init();
    }

    /**
     * Set a curl option
     *
     * @param int $key
     * @param mixed $value
     *
     * @return void
     */
    public function setopt(int $key, $value): void
    {
        curl_setopt($this->curl, $key, $value);
    }

    /**
     * Set an array of options to a curl resource
     *
     * @param array $options
     *
     * @return void
     */
    public function setoptArray(array $options): void
    {
        curl_setopt_array($this->curl, $options);
    }

    /**
     * Send a curl request
     *
     * @return mixed
     */
    public function exec()
    {
        return curl_exec($this->curl);
    }

    /**
     * Return the curl error number
     *
     * @return int
     */
    public function errno(): int
    {
        return curl_errno($this->curl);
    }

    /**
     * Return the curl error message
     *
     * @return string
     */
    public function error(): string
    {
        return curl_error($this->curl);
    }

    /**
     * Get info from a curl reference
     *
     * @param int $type
     *
     * @return mixed
     */
    public function getinfo(int $type)
    {
        return curl_getinfo($this->curl, $type);
    }

    /**
     * Get the currently installed curl version
     *
     * @return array
     */
    public function version(): array
    {
        return curl_version();
    }

    /**
     * Close the resource connection to curl
     *
     * @return void
     */
    public function close(): void
    {
        curl_close($this->curl);
    }
}
