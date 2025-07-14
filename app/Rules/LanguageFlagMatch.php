<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LanguageFlagMatch implements ValidationRule
{
    protected string $languageCode;

    public function __construct(string $languageCode)
    {
        $this->languageCode = $languageCode;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
         $value = strtoupper($value);
        $languageCode = strtolower($this->languageCode);
        $mapping = include resource_path('language-data/mapping.php');

        if (!array_key_exists($languageCode, $mapping)) {
            $fail(__('errors.invalid_language_flag_combination'));
            return;
        }

        $validFlags = $mapping[$languageCode];

        $isValid = is_array($validFlags)
            ? in_array($value, $validFlags)
            : $value === $validFlags;

        if (! $isValid) {
            $fail(__('errors.invalid_language_flag_combination'));
        }
    }
}
