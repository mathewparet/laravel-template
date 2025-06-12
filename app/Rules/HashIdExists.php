<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HashIdExists implements ValidationRule
{
    public function __construct(public string $model)
    {
        // 
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            if(!$this->model::findByHashId(optional($value)['hash_id'])) {
                $fail(':attribute not found');
            }
        }
        catch(\Throwable $e) {
            $fail('Invalid :attribute');
        }
    }
}
