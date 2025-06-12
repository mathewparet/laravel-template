<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class PasswordConfirmationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $lastConfirmation = $request->session()->get(
            'auth.password_confirmed_at', 0
        );

        $lastConfirmed = (Date::now()->unix() - $lastConfirmation);

        $confirmed = $lastConfirmed < $request->input(
            'seconds', config('auth.password_timeout', 900)
        );

        return response()->json([
            'confirmed' => $confirmed,
        ], headers: array_filter([
            'X-Retry-After' => $confirmed ? $lastConfirmed : null,
        ]));
    }
}
