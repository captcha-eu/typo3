<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3\Form;

use CaptchaEU\Typo3\Service\Validator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class FormValidator extends AbstractValidator
{
	protected function isValid($solution): void
	{
		// get validator instance
		$captchaEUValidator = GeneralUtility::makeInstance(Validator::class);
		$isValid = $captchaEUValidator->validate($solution);

		// check if solution is valid
		if (!$isValid) {
			// invalid => add error
			$this->addError($this->translateErrorMessage('error.solution_invalid', 'captchaeu_typo3'), 1695723714);
		}
	}

	public function setOptions(array $options): void
	{
		// @todo: Remove this method when v11 compatibility is dropped.
		$this->initializeDefaultOptions($options);
	}
}
