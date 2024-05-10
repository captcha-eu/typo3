<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3;

use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Configuration
{
	// defaults
	public const HOST_DEFAULT = 'https://www.captcha.eu';

	// host
	protected $host = '';

	// keys
	protected  $keyPublic = '';
	protected  $keyREST = '';

	// endpoints
	protected const EP_VALIDATE = '/validate';

	public function __construct(Site $site = null)
	{
		if ($site === null) {
			$site = $GLOBALS['TYPO3_REQUEST']->getAttribute('site');
		}

		if ($site === null) {
			return;
		}

		// preview/NullSite
		if(!method_exists($site, 'getConfiguration')) {
			return;
		}

		// get site config
		$siteConfiguration = $site->getConfiguration();

		// assign values from site configuration
		$this->host = trim($siteConfiguration['captchaeu_host'] ?? '');
		$this->keyPublic = trim($siteConfiguration['captchaeu_key_public'] ?? '');
		$this->keyREST = trim($siteConfiguration['captchaeu_key_rest'] ?? '');
	}

	// make sure the essential settings are set
	public function isEnabled()
	{
		// check if keys are set
		$keysSet = $this->keyPublic !== '' && $this->keyREST !== '';

		return $keysSet;
	}

	// get public key
	public function getKeyPublic()
	{
		return $this->keyPublic;
	}

	// get rest key
	public function getKeyREST()
	{
		return $this->keyREST;
	}

	// get validation endpoint
	public function getEPValidate()
	{
		return $this->getHost() . self::EP_VALIDATE;
	}

	// get service host
	public function getHost()
	{
		// config or default
		return $this->host ?: self::HOST_DEFAULT;
	}

	// sdk.js path with config host
	public function getSDKJSPath()
	{
		// return sdk path with configured host
		return $this->getHost() . '/sdk.js';
	}
}

