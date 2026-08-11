# Captcha.EU

![CI](https://github.com/captcha-eu/typo3/actions/workflows/ci.yml/badge.svg)

TYPO3 extension for captcha.eu - The intelligent GDPR-compliant non-intrusive bot protection.

See [Documentation](https://docs.captcha.eu/typo3-install?id=-form-beta) for installation and configuration instructions.

## Requirements

| Version | TYPO3       | PHP         |
|---------|-------------|-------------|
| 2.x     | 12.4 - 14.x | 8.2 - 8.4   |
| 1.x     | 11.5 - 13.4 | 8.1 - 8.3   |

## Installation

### Via Composer (recommended)

```bash
composer require captcha-eu/typo3
```

### Via TYPO3 Extension Repository (TER)

Search for `captchaeu_typo3` in the Extension Manager.

## Configuration

Configure the extension in your site configuration (`config/sites/<site>/config.yaml`):

```yaml
captchaeu_host: 'https://www.captcha.eu'
captchaeu_key_public: 'your-public-key'
captchaeu_key_rest: 'your-rest-key'
```
## Widget mode

By default, captcha.eu runs in **invisible mode**, protecting your form silently in the background with no visible challenge for the user.

You can optionally switch to **widget mode**, which shows a visible "I am human" widget on the form instead.

Configure this in **Site Management > Sites > [your site] > Captcha.eu** tab:

```yaml
captchaeu_mode: 'widget'
captchaeu_theme: 'light'
```

- **Widget Mode**: `invisible` (default) or `widget`
- **Widget Theme**: `light`, `dark`, or `auto` (only used when `widget` mode is active)

No code changes are required to use widget mode - it's controlled entirely from Site Configuration, and existing installations continue to run in invisible mode unchanged unless explicitly switched.

Add the CaptchaEU form element to your form via the TYPO3 Form Editor (**Web > Forms**) - it appears under a custom "CaptchaEU Captcha" element group, and works the same way in both invisible and widget mode.

## Credits

This extension is inspired by the great work from the folks at [Studio Mitte](https://studiomitte.com). Check out their other extensions [here](https://www.studiomitte.com/loesungen/typo3).
