<?php

namespace App\Rules;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Facades\TwoFactorAuthentication;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTwoFactorLogin implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();

        if(!TwoFactorAuthentication::verifyOtp($value, $user->two_factor_token)) {
            if(!$this->attemptLoginViaBackupCode($value, $user)) {
                $fail('Invalid two factor code or backup code.');
            }
        }
    }

    private function attemptLoginViaBackupCode($value, User $user)
    {
        $backup_codes = collect($user->backup_codes);

        $code = $backup_codes->where('used', false)->where('code', $value)->count();

        return tap($code, function() use($backup_codes, $user, $value) {
            $user->backup_codes = $backup_codes->map(fn($item) => $item['code'] == $value ? array_merge($item, ['used' => true]) : $item)->all();
            $user->save();
        });
    }
}
