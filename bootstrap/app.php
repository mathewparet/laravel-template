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
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        $exceptions->render(function (Throwable $exeption, \Illuminate\Http\Request $request) {
            
            if ($exeption instanceof AuthenticationException) {
                return null;
            }

            if ($exeption instanceof ValidationException) {
                return null;
            }

            $status = method_exists($exeption, 'getStatusCode') ? $exeption->getStatusCode() : null;

            if($exeption instanceof HttpException && (!$status || $status < 400)) {
                return null;
            }

            $requestId = $request->requestId();

            if($exeption instanceof MaskedException) {
                $message = $exeption->getMaskedMessage()['message'];
                $status = $exeption->getMaskedMessage()['status'];
            }
            else {
                if(!app()->isProduction() && app()->hasDebugModeEnabled()) {
                    return;
                }
                $message = $exeption->getMessage();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => $message,
                    'code' => $status,
                    'requestId' => $requestId,
                ], $status);
            }

            if (
                    (
                        !$request->headers->has('referer') 
                        || !str_starts_with($request->headers->get('referer'), config('app.url'))
                    ) 
                    && !$request->headers->has('X-Inertia')
                ) {
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
                ->with('requestId', $requestId);
        });
    })->create();
