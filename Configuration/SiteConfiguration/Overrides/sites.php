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

		// Widget Mode
		$GLOBALS['SiteConfiguration']['site']['columns']['captchaeu_mode'] = [
			'label' => $lll . 'site.configuration.mode',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['label' => $lll . 'site.configuration.mode.invisible', 'value' => 'invisible'],
					['label' => $lll . 'site.configuration.mode.widget', 'value' => 'widget'],
				],
				'default' => 'invisible',
			],
		];
		// Widget Theme
		$GLOBALS['SiteConfiguration']['site']['columns']['captchaeu_theme'] = [
			'label' => $lll . 'site.configuration.theme',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['label' => $lll . 'site.configuration.theme.light', 'value' => 'light'],
					['label' => $lll . 'site.configuration.theme.dark', 'value' => 'dark'],
					['label' => $lll . 'site.configuration.theme.auto', 'value' => 'auto'],
				],
				'default' => 'light',
			],
		];

		// add to showitem
		$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ',--div--;' . $lll . 'site.configuration.tab, captchaeu_key_public,captchaeu_key_rest,captchaeu_host,captchaeu_mode,captchaeu_theme,';
	}
);
