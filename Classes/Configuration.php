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

	public function setValue(string $value): void
    {
        $this->value = $value;
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
	public const MODE_DEFAULT = 'invisible';
	public const THEME_DEFAULT = 'light';

	// host
	protected string $host = '';

	// keys
	protected string $keyPublic = '';
	protected string $keyREST = '';

	// widget mode ('invisible' or 'widget') and theme ('light', 'dark', 'auto')
	protected string $mode = self::MODE_DEFAULT;
	protected string $theme = self::THEME_DEFAULT;
	protected array $sdkDataAttributes = [];
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
		$this->mode = trim($siteConfiguration['captchaeu_mode'] ?? '') ?: self::MODE_DEFAULT;
		$this->theme = trim($siteConfiguration['captchaeu_theme'] ?? '') ?: self::THEME_DEFAULT;
		$this->sdkDataAttributes = $this->parseSdkDataAttributes(
			$siteConfiguration['captchaeu_sdk_data_attributes'] ?? []
		);
		if ($this->eventDispatcher !== null) {
            $this->host = $this->dispatchValueEvent($this->host, 'host');
            $this->keyPublic = $this->dispatchValueEvent($this->keyPublic, 'keyPublic');
            $this->keyREST = $this->dispatchValueEvent($this->keyREST, 'keyREST');
            $this->mode = $this->dispatchValueEvent($this->mode, 'mode');
            $this->theme = $this->dispatchValueEvent($this->theme, 'theme');
        }
	}

	/**
	 * Normalises the SDK data-attribute config into a name => value map.
	 * Accepts an array (YAML map) or a string with one "name=value" per line.
	 *
	 * @param mixed $raw
	 * @return array<string, string>
	 */
	protected function parseSdkDataAttributes($raw): array
	{
		if (is_array($raw)) {
			return $raw;
		}

		if (!is_string($raw) || trim($raw) === '') {
			return [];
		}

		$attributes = [];
		foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
			$line = trim($line);
			if ($line === '' || strpos($line, '=') === false) {
				continue;
			}
			[$name, $value] = explode('=', $line, 2);
			$name = trim($name);
			if ($name !== '') {
				$attributes[$name] = trim($value);
			}
		}

		return $attributes;
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

	// get widget mode ('invisible' or 'widget')
	public function getMode(): string
	{
		return $this->mode ?: self::MODE_DEFAULT;
	}

	// true if widget mode is active
	public function isWidgetMode(): bool
	{
		return $this->getMode() === 'widget';
	}

	// get widget theme
	public function getTheme(): string
	{
		return $this->theme ?: self::THEME_DEFAULT;
	}

	// extra data-* attributes for the SDK <script> tag
	public function getSdkDataAttributes(): array
	{
		return $this->sdkDataAttributes;
	}

	// sdk.js path with config host
	public function getSDKJSPath(): string
	{
		// return sdk path with configured host
		return $this->getHost() . '/sdk.js';
	}
}