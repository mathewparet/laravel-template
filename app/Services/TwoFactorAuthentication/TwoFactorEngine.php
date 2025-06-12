<?php
namespace App\Services\TwoFactorAuthentication;

use Illuminate\Support\Facades\Session;
use App\Contracts\TwoFactorAuthentication;

abstract class TwoFactorEngine implements TwoFactorAuthentication
{
    protected string $name, $email, $secret;

    public function setUser(?string $name = null, ?string $email = null): TwoFactorAuthentication
    {
        $this->name = $name ?? $this->name;
        $this->email = $email ?? $this->email;

        return $this;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function setSecret(?string $secret = null): TwoFactorAuthentication
    {
        $this->secret = $secret ?? $this->secret;

        return $this;
    }

    public function register(?string $name = null, ?string $email = null): TwoFactorAuthentication
    {
        $this->setUser($name, $email);

        return tap(app()->call([$this, 'generateSecret']), fn($totp) => Session::put('totp-registration', $totp));
    }

    public function verifyRegistration(string $otp): bool
    {
        /**
         * @var \App\Contracts\TwoFactorAuthentication $totp
         */
        $totp = Session::get('totp-registration');

        return $totp->verifyOtp($otp, $totp->getSecret());
    }

    public function getRegistrationSecret(): string
    {
        /**
         * @var \App\Contracts\TwoFactorAuthentication $totp
         */
        $totp = Session::get('totp-registration');

        return tap($totp->getSecret(), fn() => Session::forget('totp-registration'));
    }

    public function generateQRCodeUrl(#[\SensitiveParameter] ?string $secret = null, ?string $name = null, ?string $email = null): string
    {
        $this->setUser($name, $email);

        $secret = $secret ?? $this->secret;

        return __("otpauth://totp/:name::email?secret=:secret&issuer=:issuer&algorithm=:algorithm&digits=:digits&period=:period", [
            'name' => urlencode($this->name),
            'email' => urlencode($this->email),
            'secret' => $secret,
            'algorithm' => 'SHA1',
            'issuer' => config('app.name'),
            'digits' => 6,
            'period' => 30,
        ]);
    }
}