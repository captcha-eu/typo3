<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3\Form;

use CaptchaEU\Typo3\Service\Validator;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class FormValidator extends AbstractValidator
{
	protected bool $acceptsEmptyValues = false;

	private Validator $validator;

	public function __construct(Validator $validator)
	{
		$this->validator = $validator;
	}

	protected function isValid(mixed $solution): void
	{
		$isValid = $this->validator->validate((string)$solution);

		// check if solution is valid
		if (!$isValid) {
			// invalid => add error
			$this->addError(
				$this->translateErrorMessage('error.solution_invalid', 'captchaeu_typo3') ?? 'Invalid captcha solution',
				1695723714
			);
		}
	}
}
