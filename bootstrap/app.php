<?php

use App\Contracts\MaskedException;
use App\Http\Middleware\AddLoggingContext;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use PioneerDynamics\InertiaApiMiddleware\Http\Middleware\InertiaApiMiddleware;
use App\Http\Middleware\RequireTwoFactorAuthenticationWhenEnabled;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance']);
        $middleware->statefulApi();
        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            AddLoggingContext::class,
        ]);
        
        $middleware->alias([
            '2fa' => RequireTwoFactorAuthenticationWhenEnabled::class,
        ]);

        $middleware->api(append: [
            AddLoggingContext::class,
            InertiaApiMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
            
            if ($e instanceof AuthenticationException) {
                return null;
            }

            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : null;

            // Only handle 4xx and 5xx errors
            if (!$status || $status < 500) {
                return null; // fallback to default handling
            }

            $requestId = $request->requestId();

            if($e instanceof MaskedException) {
                $message = $e->getMaskedMessage()['message'];
                $status = $e->getMaskedMessage()['status'];
            }
            else {
                $message = $e->getMessage();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => $message,
                    'code' => $status,
                    'request_id' => $requestId,
                ], $status);
            }

            if (!url()->previous() || url()->previous() === $request->fullUrl()) {
                return inertia('Error/Show', [
                    'message' => $message,
                    'code' => $status,
                    'requestId' => $requestId,
                ])->toResponse($request)->setStatusCode($status);
            }

            // For non-JSON (e.g. browser requests), use flash
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $message)
                ->with('request-id', $requestId);
        });
    })->create();
