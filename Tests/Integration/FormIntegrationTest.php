<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3\Tests\Integration;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Integration test for CaptchaEU Form functionality
 */
class FormIntegrationTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/captchaeu_typo3',
    ];

    protected array $coreExtensionsToLoad = [
        'form',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/site_configuration.csv');
    }

    /**
     * @test
     */
    public function extensionIsLoaded(): void
    {
        $this->assertTrue(
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('captchaeu_typo3'),
            'CaptchaEU extension should be loaded'
        );
    }

    /**
     * @test
     */
    public function formValidatorCanBeInstantiated(): void
    {
        $validator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \CaptchaEU\Typo3\Form\FormValidator::class
        );
        
        $this->assertInstanceOf(
            \CaptchaEU\Typo3\Form\FormValidator::class,
            $validator,
            'FormValidator should be instantiable'
        );
    }

    /**
     * @test
     */
    public function configurationCanBeInstantiated(): void
    {
        $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \CaptchaEU\Typo3\Configuration::class
        );
        
        $this->assertInstanceOf(
            \CaptchaEU\Typo3\Configuration::class,
            $configuration,
            'Configuration should be instantiable'
        );
    }

    /**
     * @test
     */
    public function validatorServiceCanBeInstantiated(): void
    {
        $validator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \CaptchaEU\Typo3\Service\Validator::class
        );
        
        $this->assertInstanceOf(
            \CaptchaEU\Typo3\Service\Validator::class,
            $validator,
            'Validator service should be instantiable'
        );
    }

    /**
     * @test
     */
    public function configurationReturnsDefaultValues(): void
    {
        $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \CaptchaEU\Typo3\Configuration::class
        );
        
        $this->assertEquals(
            'https://www.captcha.eu',
            $configuration->getHost(),
            'Default host should be captcha.eu'
        );
        
        $this->assertEquals(
            'https://www.captcha.eu/validate',
            $configuration->getEPValidate(),
            'Validation endpoint should be correct'
        );
        
        $this->assertEquals(
            'https://www.captcha.eu/sdk.js',
            $configuration->getSDKJSPath(),
            'SDK JS path should be correct'
        );
        
        $this->assertFalse(
            $configuration->isEnabled(),
            'Configuration should not be enabled without keys'
        );
    }
}