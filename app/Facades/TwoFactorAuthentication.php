<?php
namespace App\Facades;

use App\Contracts\TwoFactorAuthentication as TwoFactorAuthenticationContract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static TwoFactorAuthentication setUser(string $name, string $email)
 * @method static TwoFactorAuthentication register(?string $name = null, ?string $email = null)
 * @method static TwoFactorAuthentication generateSecret()
 * @method static string getSecret()
 * @method static TwoFactorAuthentication setSecret(?string $secret = null)
 * @method static bool verifyOtp(string $otp, ?string $secret = null)
 * @method static string generateQRCodeUrl(?string $secret = null, ?string $name = null, ?string $email = null)
 * @method static bool verifyRegistration(string $otp): bool;
 * @method static string getRegistrationSecret(): string;
 */
class TwoFactorAuthentication extends Facade
{
    public static function getFacadeAccessor()
    {
        return TwoFactorAuthenticationContract::class;
    }
}