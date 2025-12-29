<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3\ViewHelpers;

use CaptchaEU\Typo3\Configuration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ConfigurationViewHelper extends AbstractViewHelper
{
    protected Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
    }

    public function render()
    {
        return [
            'host' => $this->configuration->getHost(),
            'keyPublic' => $this->configuration->getKeyPublic(),
            'SDKJSPath' => $this->configuration->getSDKJSPath(),
            'enabled' => $this->configuration->isEnabled()
        ];
    }
}
