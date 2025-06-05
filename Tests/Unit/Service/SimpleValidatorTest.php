<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3\Tests\Unit\Service;

use CaptchaEU\Typo3\Configuration;
use CaptchaEU\Typo3\Service\Validator;
use PHPUnit\Framework\TestCase;

class SimpleValidatorTest extends TestCase
{
    public function testValidateReturnsFalseWhenConfigurationIsNotEnabled(): void
    {
        $configMock = $this->createMock(Configuration::class);
        $configMock->method('isEnabled')->willReturn(false);
        
        // Create minimal mocks for required dependencies
        $clientMock = $this->createMock(\Psr\Http\Client\ClientInterface::class);
        $loggerMock = $this->createMock(\Psr\Log\LoggerInterface::class);
        $requestFactoryMock = $this->createMock(\TYPO3\CMS\Core\Http\RequestFactory::class);
        
        $validator = new Validator($clientMock, $loggerMock, $configMock, $requestFactoryMock);
        
        $result = $validator->validate('test-solution');
        
        $this->assertFalse($result);
    }

    public function testValidateReturnsFalseWhenSolutionIsEmpty(): void
    {
        $configMock = $this->createMock(Configuration::class);
        $configMock->method('isEnabled')->willReturn(true);
        
        // Create minimal mocks for required dependencies
        $clientMock = $this->createMock(\Psr\Http\Client\ClientInterface::class);
        $loggerMock = $this->createMock(\Psr\Log\LoggerInterface::class);
        $requestFactoryMock = $this->createMock(\TYPO3\CMS\Core\Http\RequestFactory::class);
        
        $validator = new Validator($clientMock, $loggerMock, $configMock, $requestFactoryMock);
        
        $result = $validator->validate('');
        
        $this->assertFalse($result);
    }

    public function testValidateReturnsFalseWhenSolutionIsNull(): void
    {
        $configMock = $this->createMock(Configuration::class);
        $configMock->method('isEnabled')->willReturn(true);
        
        // Create minimal mocks for required dependencies
        $clientMock = $this->createMock(\Psr\Http\Client\ClientInterface::class);
        $loggerMock = $this->createMock(\Psr\Log\LoggerInterface::class);
        $requestFactoryMock = $this->createMock(\TYPO3\CMS\Core\Http\RequestFactory::class);
        
        $validator = new Validator($clientMock, $loggerMock, $configMock, $requestFactoryMock);
        
        $result = $validator->validate();
        
        $this->assertFalse($result);
    }
}