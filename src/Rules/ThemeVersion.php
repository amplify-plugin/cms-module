<?php

namespace Amplify\System\Cms\Rules;

use Closure;
use Composer\Semver\VersionParser;
use Illuminate\Contracts\Validation\ValidationRule;

class ThemeVersion implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!app(VersionParser::class)->isValid($value)) {
            $fail("The $attribute is not a valid version.");
        }
    }
}
