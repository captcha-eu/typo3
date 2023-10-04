<?php

defined('TYPO3') or die();

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Core\Imaging\IconRegistry::class
);

$iconRegistry->registerIcon(
    'captchaeu-icon',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    [
        'source' => 'EXT:captchaeu_typo3/Resources/Public/Icons/captchaeu-icon.svg'
    ]
);

call_user_func(function() {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        trim('
        plugin.tx_form.settings.yamlConfigurations {
            100 = EXT:captchaeu_typo3/Configuration/Form/CaptchaEUFormSetup.yaml
        }
        module.tx_form.settings.yamlConfigurations {
            100 = EXT:captchaeu_typo3/Configuration/Form/CaptchaEUFormSetup.yaml
        }'));
});
