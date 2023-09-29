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

	public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
	{
		$configuration = new Configuration();

		// pass variables to view
		return [
			'host' => $configuration->getHost(),
			'keyPublic' => $configuration->getKeyPublic(),
			'SDKJSPath' => $configuration->getSDKJSPath(),
			'enabled' => $configuration->isEnabled()
		];
	}
}