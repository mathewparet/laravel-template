<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactorAuthenticationWhenEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();

        if($user->is_two_factor_enabled) {
            if(!$request->session()->has('two_factor_passed')) {
                $request->session()->put('url.intended', $request->url());
                return redirect()->route('2fa.page');
            }
        }

        return $next($request);
    }
}
