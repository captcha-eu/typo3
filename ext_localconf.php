<?php

defined('TYPO3') or die();

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
