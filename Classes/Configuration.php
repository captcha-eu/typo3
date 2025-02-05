<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3;

use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

class ModifyConfigValueEvent
{
    private string $value;
    private string $property;

    public function __construct(string $value, string $property)
    {
        $this->value = $value;
        $this->property = $property;
    }

    public function getValue(): string 
    {
        return $this->value;
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}

class Configuration
{
	// defaults
	public const HOST_DEFAULT = 'https://www.captcha.eu';

	// host
	protected string $host = '';

	// keys
	protected string $keyPublic = '';
	protected string $keyREST = '';
	private ?EventDispatcherInterface $eventDispatcher;
	private ?ServerRequestInterface $request;

	// endpoints
	protected const EP_VALIDATE = '/validate';

	public function __construct(?ServerRequestInterface $request = null, ?EventDispatcherInterface $eventDispatcher = null)
	{
		$this->eventDispatcher = $eventDispatcher;
		$this->request = $request ?? ($GLOBALS['TYPO3_REQUEST'] ?? null);

		if ($this->request === null) {
			return;
		}

		$site = $this->request->getAttribute('site');

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
		if ($this->eventDispatcher !== null) {
            $this->host = $this->dispatchValueEvent($this->host, 'host');
            $this->keyPublic = $this->dispatchValueEvent($this->keyPublic, 'keyPublic');
            $this->keyREST = $this->dispatchValueEvent($this->keyREST, 'keyREST');
        }
	}

	protected function dispatchValueEvent(string $value, string $property): string
    {
        if ($this->eventDispatcher === null) {
            return $value;
        }
        
        $event = new ModifyConfigValueEvent($value, $property);
        $event = $this->eventDispatcher->dispatch($event);
        return $event->getValue();
    }

	// make sure the essential settings are set
	public function isEnabled(): bool
	{
		// check if keys are set
		$keysSet = $this->keyPublic !== '' && $this->keyREST !== '';

		return $keysSet;
	}

	// get public key
	public function getKeyPublic(): string
	{
		return $this->keyPublic;
	}

	// get rest key
	public function getKeyREST(): string
	{
		return $this->keyREST;
	}

	// get validation endpoint
	public function getEPValidate(): string
	{
		return $this->getHost() . self::EP_VALIDATE;
	}

	// get service host
	public function getHost(): string
	{
		// config or default
		return $this->host ?: self::HOST_DEFAULT;
	}

	// sdk.js path with config host
	public function getSDKJSPath(): string
	{
		// return sdk path with configured host
		return $this->getHost() . '/sdk.js';
	}
}