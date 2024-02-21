<?php

/***************************************************************
 * Extension Manager/Repository config for ext "captchaeu_typo3"
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
	'title' => 'Captcha.eu',
	'description' => 'Captcha.eu - The intelligent GDPR-compliant Captcha solution without interrupting puzzles or riddles',
	'category' => 'plugin',
	'author' => 'Captcha.eu',
	'author_email' => 'hello@captcha.eu',
	'state' => 'stable',
	'version' => '1.0.5',
	'constraints' => [
		'depends' => [
			'typo3' => '11.5.0-12.4.99'
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
