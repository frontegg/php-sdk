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
                Config::AUTHENTICATION_SERVICE => '/test/auth',
                Config::AUDITS_SERVICE => '/audits',
                Config::EVENTS_SERVICE => '/eventzz',
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
            $config->getServiceUrl(Config::AUTHENTICATION_SERVICE)
        );
        $this->assertEquals(
            'https://api.frontegg.com/audits',
            $config->getServiceUrl(Config::AUDITS_SERVICE)
        );
        $this->assertEquals(
            'https://api.frontegg.com/eventzz',
            $config->getServiceUrl(Config::EVENTS_SERVICE)
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
            $config->getBaseUrl() . Config::AUTHENTICATION_SERVICE_DEFAULT_URL,
            $config->getServiceUrl(Config::AUTHENTICATION_SERVICE)
        );
        $this->expectException(InvalidUrlConfigException::class);
        $this->assertNotEquals(
            'should not be in the config',
            $config->getServiceUrl('randomUrl')
        );
    }
}
