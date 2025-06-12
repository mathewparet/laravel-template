<?php
namespace App\Services\TwoFactorAuthentication;

use PragmaRX\Google2FA\Google2FA;
use App\Contracts\TwoFactorAuthentication;

class Google2FAService extends TwoFactorEngine implements TwoFactorAuthentication
{
    public function __construct(private Google2FA $google2FA)
    {
        
    }

    public function generateSecret(): self
    {
        $this->secret = $this->google2FA->generateSecretKey();

        return $this;
    }

    public function verifyOtp(#[\SensitiveParameter] string $otp, #[\SensitiveParameter] ?string $secret = null): bool
    {
        $this->setSecret($secret);

        return $this->google2FA->verifyKey($this->secret, $otp);
    }
}