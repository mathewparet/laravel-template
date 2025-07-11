<?php

namespace App\Http\Requests;

use App\Rules\ValidTwoFactorLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Http\FormRequest;

class TwoFactorLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'otp' => ['required', new ValidTwoFactorLogin],
        ];
    }

    public function authenticate()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();

        Session::put('two_factor_passed', true);
    }
}
