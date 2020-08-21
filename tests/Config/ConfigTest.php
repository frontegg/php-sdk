<?php

namespace Frontegg\Tests\Config;

use Frontegg\Config\Config;
use Frontegg\Exception\InvalidUrlConfigException;
use Frontegg\Http\RequestInterface;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @return void
     */
    public function testConfigHasClientCredentials(): void
    {
        // Arrange
        $config = new Config(
            'clientTestID',
            'apiTestSecretKey',
            'https://api.frontegg.com/',
            [],
            false,
            function (RequestInterface $request) {
                return [];
            }
        );

        // Assert
        $this->assertEquals('clientTestID', $config->getClientId());
        $this->assertEquals('apiTestSecretKey', $config->getClientSecret());
    }

    /**
     * @throws InvalidUrlConfigException
     *
     * @return void
     */
    public function testConfigHasApiUrls(): void
    {
        // Arrange
        $config = new Config(
            'clientTestID',
            'apiTestSecretKey',
            'https://api.frontegg.com/',
            [
                Config::SERVICE_AUTHENTICATION => '/test/auth',
                Config::SERVICE_AUDITS => '/audits',
                Config::SERVICE_EVENTS => '/eventzz',
                'randomUrl' => 'should not be in the config',
            ],
            false,
            function (RequestInterface $request) {
                return [];
            }
        );

        // Assert
        $this->assertEquals('https://api.frontegg.com', $config->getBaseUrl());
        $this->assertEquals(
            'https://api.frontegg.com/test/auth',
            $config->getServiceUrl(Config::SERVICE_AUTHENTICATION)
        );
        $this->assertEquals(
            'https://api.frontegg.com/audits',
            $config->getServiceUrl(Config::SERVICE_AUDITS)
        );
        $this->assertEquals(
            'https://api.frontegg.com/eventzz',
            $config->getServiceUrl(Config::SERVICE_EVENTS)
        );
        $this->assertEquals(
            'https://api.frontegg.com',
            $config->getProxyUrl()
        );
        $this->expectException(InvalidUrlConfigException::class);
        $this->assertNotEquals(
            'should not be in the config',
            $config->getServiceUrl('randomUrl')
        );
    }

    /**
     * @throws InvalidUrlConfigException
     *
     * @return void
     */
    public function testConfigHasDefaultApiUrls(): void
    {
        // Arrange
        $config = new Config(
            'clientTestID',
            'apiTestSecretKey',
            'https://api.frontegg.com/',
            [],
            false,
            function (RequestInterface $request) {
                return [];
            }
        );

        // Assert
        $this->assertEquals(
            $config->getBaseUrl() . Config::SERVICE_AUTHENTICATION_DEFAULT_URL,
            $config->getServiceUrl(Config::SERVICE_AUTHENTICATION)
        );
        $this->expectException(InvalidUrlConfigException::class);
        $this->assertNotEquals(
            'should not be in the config',
            $config->getServiceUrl('randomUrl')
        );
    }
}
