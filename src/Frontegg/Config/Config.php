<?php

namespace Frontegg\Config;

use Frontegg\Exception\InvalidUrlConfigException;

/**
 * Class Config
 *
 * @package Frontegg
 */
class Config
{
    public const SERVICE_AUTHENTICATION = 'authentication';
    public const SERVICE_AUDITS = 'audits';
    public const SERVICE_EVENTS = 'events';

    public const SERVICE_AUTHENTICATION_DEFAULT_URL = '/auth/vendor';
    public const SERVICE_AUDITS_DEFAULT_URL = '/audits';
    public const SERVICE_EVENTS_DEFAULT_URL = '/event/resources/triggers/v2';

    /**
     * List of allowed API service URLs and its' default values.
     *
     * @var string[]
     */
    protected static $API_URL_KEYS = [
        self::SERVICE_AUTHENTICATION => self::SERVICE_AUTHENTICATION_DEFAULT_URL,
        self::SERVICE_AUDITS => self::SERVICE_AUDITS_DEFAULT_URL,
        self::SERVICE_EVENTS => self::SERVICE_EVENTS_DEFAULT_URL,
    ];

    /**
     * Client ID.
     *
     * @var string
     */
    protected $clientId;

    /**
     * API secret key.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * Frontegg API base URL.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Frontegg API endpoints relative URLs.
     *
     * @var array
     */
    protected $urls;

    /**
     * @var callable
     */
    protected $contextResolver;

    /**
     * @var bool
     */
    protected $disableCors;

    /**
     * Config constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $baseUrl
     * @param array $urls
     * @param bool $disableCors
     * @param callable $contextResolver
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $baseUrl,
        array $urls,
        bool $disableCors,
        callable $contextResolver
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->baseUrl = trim($baseUrl, '/');
        $this->setApiUrls($urls);
        $this->contextResolver = $contextResolver;
        $this->disableCors = $disableCors;
    }

    /**
     * @return callable
     */
    public function getContextResolver(): callable
    {
        return $this->contextResolver;
    }

    /**
     * @return bool
     */
    public function isDisableCors(): bool
    {
        return $this->disableCors;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Returns API URL by service name.
     *
     * @param string $urlKey
     *
     * @throws InvalidUrlConfigException
     *
     * @return string
     */
    public function getServiceUrl(string $urlKey): string
    {
        if (!isset(static::$API_URL_KEYS[$urlKey])) {
            throw new InvalidUrlConfigException(
                sprintf('URL "%s" is not a part of allowed API', $urlKey)
            );
        }

        if (isset($this->urls[$urlKey])) {
            return $this->baseUrl . $this->urls[$urlKey];
        }

        return $this->baseUrl . static::$API_URL_KEYS[$urlKey];
    }

    /**
     * Returns URL of the Frontegg proxy.
     *
     * @return string
     */
    public function getProxyUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Sets up only allowed API URLs.
     *
     * @param array $urls
     *
     * @return void
     */
    protected function setApiUrls(array $urls = []): void
    {
        $this->urls = [];

        foreach ($urls as $key => $url) {
            if (!isset(static::$API_URL_KEYS[$key])) {
                continue;
            }

            $this->urls[$key] = $url;
        }
    }
}
