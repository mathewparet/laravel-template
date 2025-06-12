<?php

namespace App\Http\Controllers;

// use App\Contracts\QRCodeGenerator;
use Inertia\Inertia;
use App\Traits\RequestUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Facades\QRCodeGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Disable2faRequest;
use Illuminate\Support\Facades\Validator;
use App\Contracts\TwoFactorAuthentication;
use App\Http\Requests\TwoFactorRegistrationConfirmationRequest;

class TwoFactorAuthenticationController extends Controller
{
    use RequestUser;

    public function show()
    {
        return Inertia::render('settings/2fa');
    }

    public function request(TwoFactorAuthentication $twoFactorAuthentication)
    {
        $user = $this->requestUser();

        $totp = $twoFactorAuthentication->setUser($user->name, $user->email)->register();

        return back()->with('flash', [
            'totp' => [
                'secret' => $totp->getSecret(),
                'qrcode' => QRCodeGenerator::writeUrl($totp->generateQRCodeUrl())
            ]
        ]);
    }

    public function activate(TwoFactorRegistrationConfirmationRequest $request, TwoFactorAuthentication $twoFactorAuthentication)
    {
        $user = $this->requestUser();

        $user->forceFill([
            'two_factor_token' => $twoFactorAuthentication->getRegistrationSecret()
        ]);

        $request->session()->put('two_factor_passed', true);

        return $this->returnNewUserBackupCodes();
    }

    public function regenerateBackupCodes()
    {        
        return $this->returnNewUserBackupCodes();
    }

    public function showBackupCodes()
    {
        $user = $this->requestUser();

        return back()->with('flash', [
            'totp' => [
                'backup_codes' => $user->backup_codes
            ]
        ]);
    }

    private function returnNewUserBackupCodes()
    {
        $user = $this->requestUser();

        return back()->with('flash', [
            'totp' => [
                'backup_codes' => tap($this->generateBackupCodes(), fn($backup_codes) => $user->forceFill([
                    'backup_codes' => $backup_codes
                ])->save())
            ]
        ]);
    }

    private function generateBackupCodes()
    {
        $backup_codes = Collection::times(10, fn() => [
            'code' => Str::random(12),
            'used' => false,
        ]);

        return $backup_codes->all();
    }

    public function disable(Disable2faRequest $request)
    {
        $user = $this->requestUser();

        $user->disable2FA();

        $request->session()->forget('two_factor_passed', true);
    }
}
