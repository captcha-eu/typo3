<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3\ViewHelpers;

use CaptchaEU\Typo3\Configuration;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class ConfigurationViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    private static Configuration $configuration;

    public function __construct(Configuration $configuration) 
    {
        parent::__construct();
        self::$configuration = $configuration;
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
		// pass variables to view
        return [
            'host' => self::$configuration->getHost(),
            'keyPublic' => self::$configuration->getKeyPublic(),
            'SDKJSPath' => self::$configuration->getSDKJSPath(),
            'enabled' => self::$configuration->isEnabled()
        ];
    }
}