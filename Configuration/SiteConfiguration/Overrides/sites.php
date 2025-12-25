<?php

call_user_func(
	static function() {
		$lll = 'LLL:EXT:captchaeu_typo3/Resources/Private/Language/locallang.xlf:';

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

		// Enable Logging
		$GLOBALS['SiteConfiguration']['site']['columns']['captchaeu_enable_logging'] = [
			'label' => $lll . 'site.configuration.enable_logging',
			'config' => [
				'type' => 'check',
				'default' => 0,
				'items' => [
					'1' => [
						'name' => $lll . 'site.configuration.enable_logging',
						'invertStateDisplay' => true
					]
				]
			],
		];

		// add to showitem
		$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ',--div--;' . $lll . 'site.configuration.tab, captchaeu_key_public,captchaeu_key_rest,captchaeu_host,captchaeu_enable_logging,';
	}
);
