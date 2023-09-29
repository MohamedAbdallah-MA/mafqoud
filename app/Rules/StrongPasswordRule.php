<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPasswordRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the password contains at least one special character
        $hasSpecialChar = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value);

        // Check if the password contains at least one uppercase letter
        $hasUppercase = preg_match('/[A-Z]/', $value);

        // Check if the password has a minimum length of 8 characters
        $hasMinLength = strlen($value) >= 8;

        // Return true if all conditions are met
        if(! ($hasSpecialChar && $hasUppercase && $hasMinLength) ){
            $fail('The password must be at least 8 characters long, contain at least one special character, and have at least one uppercase letter.');
        }

    }
}
