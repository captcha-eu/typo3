<?php

call_user_func(
	static function() {
		$lll = 'LLL:EXT:captchaeu-typo3/Resources/Private/Language/locallang.xlf:';

		// Public Key
		$GLOBALS['SiteConfiguration']['site']['columns']['captchaeu_key_public'] = [
			'label' => $lll . 'site.configuration.key_public',
			'config' => [
				'type' => 'input',
				'placeholder' => 'XYXYXYXYXYXY',
				'eval' => 'required'
			],
		];

		// REST Key
		$GLOBALS['SiteConfiguration']['site']['columns']['captchaeu_key_rest'] = [
			'label' => $lll . 'site.configuration.key_rest',
			'config' => [
				'type' => 'input',
				'placeholder' => 'XYXYXYXYXYXY',
				'eval' => 'required'
			],
		];

		// Host
		$GLOBALS['SiteConfiguration']['site']['columns']['captchaeu_host'] = [
			'label' => $lll . 'site.configuration.host',
			'config' => [
				'type' => 'input',
				'placeholder' => \CaptchaEU\Typo3\Configuration::HOST_DEFAULT,
				'default' => \CaptchaEU\Typo3\Configuration::HOST_DEFAULT,
				'eval' => 'required'
			],
		];

		// add to showitem
		$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ',--div--;' . $lll . 'site.configuration.tab, captchaeu_key_public,captchaeu_key_rest,captchaeu_host,';
	}
);
