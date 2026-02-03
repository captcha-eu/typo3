<?php

/***************************************************************
 * Extension Manager/Repository config for ext "captchaeu_typo3"
 ***************************************************************/

$EM_CONF['captchaeu_typo3'] = [
	'title' => 'Captcha.eu',
	'description' => 'Captcha.eu - The intelligent GDPR-compliant Captcha solution without interrupting puzzles or riddles',
	'category' => 'plugin',
	'author' => 'Captcha.eu',
	'author_email' => 'hello@captcha.eu',
	'state' => 'stable',
	'version' => '2.0.0',
	'constraints' => [
		'depends' => [
			'typo3' => '12.4.0-14.4.99',
			'php' => '8.2.0-8.4.99'
		],
		'conflicts' => [],
		'suggests' => []
	],
	'autoload' => [
		'psr-4' => [
			'CaptchaEU\\Typo3\\' => 'Classes',
		],
	]
];
