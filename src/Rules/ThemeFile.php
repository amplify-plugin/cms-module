<?php

namespace Amplify\System\Cms\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\File;

class ThemeFile implements ValidationRule
{
    private string $basePath;

    public function __construct(public string $folder = 'tmp', public ?string $file = null)
    {
        $this->basePath = base_path('themes' . DIRECTORY_SEPARATOR . $this->folder . DIRECTORY_SEPARATOR);
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // for assets and layout without value(01)
        if ($value === null && $this->file !== null) {
            if (!File::exists($this->basePath . $this->file)) {
                $fail("The $attribute location is missing required {$this->file} file");
            }
        }

        //for assets and layout with value(11)
        if ($value !== null && $this->file !== null) {
            if (!File::exists($this->basePath . $value . DIRECTORY_SEPARATOR . $this->file)) {
                $fail("The $attribute location is missing required {$this->file} file");
            }
        }

        //for readme and changelog with value(10)
        if ($value !== null && $this->file === null) {
            if (!File::exists($this->basePath . $value)) {
                $fail("The $attribute file location is invalid with configuration.");
            }
        }

        //for readme and changelog with value(00)
        if ($value === null && $this->file === null) {
            return;
        }
    }
}
