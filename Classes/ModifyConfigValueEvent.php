<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3;

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