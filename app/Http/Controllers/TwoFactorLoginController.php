<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Contracts\TwoFactorAuthentication;
use App\Http\Requests\TwoFactorLoginRequest;

class TwoFactorLoginController extends Controller
{
    public function page()
    {
        if(Session::has('two_factor_passed'))
            return redirect('/dashboard');

        return Inertia::render('auth/2fa');
    }

    public function store(TwoFactorLoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
