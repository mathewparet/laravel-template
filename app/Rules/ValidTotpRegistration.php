<?php

namespace App\Rules;

use App\Facades\TwoFactorAuthentication;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTotpRegistration implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(!TwoFactorAuthentication::verifyRegistration($value)) {
            $fail(":Attribute is invalid");
        }
    }
}
