<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3\Tests\Unit;

use CaptchaEU\Typo3\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testConfigurationWithoutRequestIsNotEnabled(): void
    {
        $configuration = new Configuration(null, null);
        
        $this->assertFalse($configuration->isEnabled());
    }

    public function testConfigurationDefaultHost(): void
    {
        $configuration = new Configuration(null, null);
        
        $this->assertEquals(Configuration::HOST_DEFAULT, $configuration->getHost());
    }

    public function testConfigurationDefaultKeys(): void
    {
        $configuration = new Configuration(null, null);
        
        $this->assertEquals('', $configuration->getKeyPublic());
        $this->assertEquals('', $configuration->getKeyREST());
    }

    public function testConfigurationDefaultEndpoints(): void
    {
        $configuration = new Configuration(null, null);
        
        $this->assertEquals(Configuration::HOST_DEFAULT . '/validate', $configuration->getEPValidate());
        $this->assertEquals(Configuration::HOST_DEFAULT . '/sdk.js', $configuration->getSDKJSPath());
    }
}