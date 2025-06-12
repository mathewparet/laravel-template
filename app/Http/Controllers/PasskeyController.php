<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use PioneerDynamics\LaravelPasskey\Passkey\Passkey as PasskeyTool;
use PioneerDynamics\LaravelPasskey\Http\Requests\VerifyPasskeyRequest;
use PioneerDynamics\LaravelPasskey\Http\Controllers\PasskeyController as PioneerDynamicsPasskeyController;

class PasskeyController extends PioneerDynamicsPasskeyController
{
    public function index()
    {
        Auth::user()->load('passkeys');

        return Inertia::render('settings/Passkey');
    }

    public function login(VerifyPasskeyRequest $request)
    {
        if(isset(PasskeyTool::$updateModelCallback) && is_callable(PasskeyTool::$updateModelCallback)) {
            app()->call(PasskeyTool::$updateModelCallback);
        }
        else
        {
            $pk = json_decode($request->publicKeyCredentialSource, true);
            

            Config::get('passkey.models.passkey')::credential($pk['credential']['id'])
                ->update([
                    'public_key' => $request->publicKeyCredentialSource,
                ]);
        }

        $passkeyable = Config::get('passkey.models.passkey')::credential($pk['credential']['id'])->firstOrFail()->passkeyable;

        Auth::loginUsingId($passkeyable->id, $request->remember == 'on');

        if($passkeyable->is_two_factor_enabled) {
            $request->session()->put('two_factor_passed', true);
        }

        return redirect()->intended(Config::get('passkey.home'));
    }
}
