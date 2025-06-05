<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3\Tests\Unit\Service;

use CaptchaEU\Typo3\Configuration;
use CaptchaEU\Typo3\Service\Validator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\RequestFactory;

class ValidatorTest extends TestCase
{
    private Validator $validator;
    private ClientInterface $clientMock;
    private LoggerInterface $loggerMock;
    private Configuration $configurationMock;
    private RequestFactory $requestFactoryMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->configurationMock = $this->createMock(Configuration::class);
        $this->requestFactoryMock = $this->createMock(RequestFactory::class);

        $this->validator = new Validator(
            $this->clientMock,
            $this->loggerMock,
            $this->configurationMock,
            $this->requestFactoryMock
        );
    }

    public function testValidateReturnsFalseWhenNotEnabled(): void
    {
        $this->configurationMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $result = $this->validator->validate('test-solution');

        $this->assertFalse($result);
    }

    public function testValidateReturnsFalseWhenSolutionIsEmpty(): void
    {
        $this->configurationMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $result = $this->validator->validate('');

        $this->assertFalse($result);
    }

    public function testValidateReturnsTrueWhenSolutionIsValid(): void
    {
        $this->configurationMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configurationMock
            ->expects($this->once())
            ->method('getKeyREST')
            ->willReturn('test-key');

        $this->configurationMock
            ->expects($this->once())
            ->method('getEPValidate')
            ->willReturn('https://example.com/validate');

        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('{"success": true}');

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $responseMock
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);

        $this->requestFactoryMock
            ->expects($this->once())
            ->method('request')
            ->with(
                'https://example.com/validate',
                'POST',
                $this->callback(function ($payload) {
                    return isset($payload['headers']['Content-Type']) 
                        && $payload['headers']['Content-Type'] === 'application/json'
                        && isset($payload['headers']['Rest-Key'])
                        && $payload['headers']['Rest-Key'] === 'test-key'
                        && $payload['body'] === 'test-solution';
                })
            )
            ->willReturn($responseMock);

        $result = $this->validator->validate('test-solution');

        $this->assertTrue($result);
    }

    public function testValidateReturnsFalseWhenSolutionIsInvalid(): void
    {
        $this->configurationMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configurationMock
            ->expects($this->once())
            ->method('getKeyREST')
            ->willReturn('test-key');

        $this->configurationMock
            ->expects($this->once())
            ->method('getEPValidate')
            ->willReturn('https://example.com/validate');

        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('{"success": false}');

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $responseMock
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);

        $this->requestFactoryMock
            ->expects($this->once())
            ->method('request')
            ->willReturn($responseMock);

        $result = $this->validator->validate('invalid-solution');

        $this->assertFalse($result);
    }

    public function testValidateReturnsFalseOnException(): void
    {
        $this->configurationMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configurationMock
            ->expects($this->once())
            ->method('getKeyREST')
            ->willReturn('test-key');

        $this->configurationMock
            ->expects($this->once())
            ->method('getEPValidate')
            ->willReturn('https://example.com/validate');

        $this->requestFactoryMock
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new \Exception('Network error'));

        $result = $this->validator->validate('test-solution');

        $this->assertFalse($result);
    }

    public function testValidateReturnsFalseOnNon200StatusCode(): void
    {
        $this->configurationMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configurationMock
            ->expects($this->once())
            ->method('getKeyREST')
            ->willReturn('test-key');

        $this->configurationMock
            ->expects($this->once())
            ->method('getEPValidate')
            ->willReturn('https://example.com/validate');

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(500);

        $this->requestFactoryMock
            ->expects($this->once())
            ->method('request')
            ->willReturn($responseMock);

        $result = $this->validator->validate('test-solution');

        $this->assertFalse($result);
    }
}