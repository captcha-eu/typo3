<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3\Tests\Unit;

use CaptchaEU\Typo3\Configuration;
use CaptchaEU\Typo3\ModifyConfigValueEvent;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

class ConfigurationTest extends TestCase
{
    private Configuration $configuration;
    private ServerRequestInterface $requestMock;
    private EventDispatcherInterface $eventDispatcherMock;
    private Site $siteMock;

    protected function setUp(): void
    {
        $this->requestMock = $this->createMock(ServerRequestInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->siteMock = $this->createMock(Site::class);
    }

    public function testConfigurationWithoutRequest(): void
    {
        $configuration = new Configuration(null, null);
        
        $this->assertFalse($configuration->isEnabled());
        $this->assertEquals('', $configuration->getKeyPublic());
        $this->assertEquals('', $configuration->getKeyREST());
        $this->assertEquals(Configuration::HOST_DEFAULT, $configuration->getHost());
    }

    public function testConfigurationWithSiteConfiguration(): void
    {
        $siteConfig = [
            'captchaeu_host' => 'https://custom.captcha.eu',
            'captchaeu_key_public' => 'public-key-123',
            'captchaeu_key_rest' => 'rest-key-456'
        ];

        $this->siteMock
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($siteConfig);

        $this->requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->with('site')
            ->willReturn($this->siteMock);

        $configuration = new Configuration($this->requestMock, null);

        $this->assertTrue($configuration->isEnabled());
        $this->assertEquals('public-key-123', $configuration->getKeyPublic());
        $this->assertEquals('rest-key-456', $configuration->getKeyREST());
        $this->assertEquals('https://custom.captcha.eu', $configuration->getHost());
        $this->assertEquals('https://custom.captcha.eu/validate', $configuration->getEPValidate());
        $this->assertEquals('https://custom.captcha.eu/sdk.js', $configuration->getSDKJSPath());
    }

    public function testConfigurationWithEmptyKeys(): void
    {
        $siteConfig = [
            'captchaeu_host' => 'https://custom.captcha.eu',
            'captchaeu_key_public' => '',
            'captchaeu_key_rest' => ''
        ];

        $this->siteMock
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($siteConfig);

        $this->requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->with('site')
            ->willReturn($this->siteMock);

        $configuration = new Configuration($this->requestMock, null);

        $this->assertFalse($configuration->isEnabled());
    }

    public function testConfigurationWithEventDispatcher(): void
    {
        $siteConfig = [
            'captchaeu_host' => 'https://original.captcha.eu',
            'captchaeu_key_public' => 'original-public-key',
            'captchaeu_key_rest' => 'original-rest-key'
        ];

        $this->siteMock
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($siteConfig);

        $this->requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->with('site')
            ->willReturn($this->siteMock);

        // Mock event dispatcher to modify the host value
        $this->eventDispatcherMock
            ->expects($this->exactly(3))
            ->method('dispatch')
            ->willReturnCallback(function ($event) {
                if ($event instanceof ModifyConfigValueEvent && $event->getProperty() === 'host') {
                    $event->setValue('https://modified.captcha.eu');
                }
                return $event;
            });

        $configuration = new Configuration($this->requestMock, $this->eventDispatcherMock);

        $this->assertEquals('https://modified.captcha.eu', $configuration->getHost());
    }

    public function testConfigurationWithDefaultHost(): void
    {
        $siteConfig = [
            'captchaeu_host' => '',
            'captchaeu_key_public' => 'public-key',
            'captchaeu_key_rest' => 'rest-key'
        ];

        $this->siteMock
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($siteConfig);

        $this->requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->with('site')
            ->willReturn($this->siteMock);

        $configuration = new Configuration($this->requestMock, null);

        $this->assertEquals(Configuration::HOST_DEFAULT, $configuration->getHost());
        $this->assertEquals(Configuration::HOST_DEFAULT . '/sdk.js', $configuration->getSDKJSPath());
    }

    public function testConfigurationWithNullSite(): void
    {
        $this->requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->with('site')
            ->willReturn(null);

        $configuration = new Configuration($this->requestMock, null);

        $this->assertFalse($configuration->isEnabled());
        $this->assertEquals('', $configuration->getKeyPublic());
        $this->assertEquals('', $configuration->getKeyREST());
    }
}